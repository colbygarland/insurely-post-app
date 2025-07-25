<?php

namespace App\Http\Controllers;

use App\Models\CallLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

class RingCentralController extends Controller
{
    // The admin account id
    private const ACCOUNT_ID = '1254284024';

    private const STAFF_ACCOUNT_IDS = [
        'Adriana Paul' => '1742506024',
        'Christine Boyd' => '1372741024',
        'Erin E' => '1305758024',
        'Kade Bowie' => '418710025',
        'Karen Gilkyson' => '507489025',
        'Kiarra Blanchard' => '1670212024',
        'Lexi Adam' => '1670211024',
        // 'Lisa K' => '1305759024',
        'Marissa Loeppky' => '1742507024',
        'Meriska Kuntz' => '1649096024',
        'Sara Zwaagstra' => '1372739024',
        'Savanna Lafferty' => '1620828024',
        'Tori Fraser' => '446566025',
    ];

    private const RING_CENTRAL_URL = 'https://platform.ringcentral.com/restapi/v1.0/';

    public function index(Request $request)
    {
        // Update the call logs
        $success = self::getCallLog();
        if (! $success) {
            Session::flash('errorMessage', 'Failed to get updated call log');
        }

        $perPage = 50;
        $fromNameFilter = $request->get('from_name', 'all');
        $startDate = $request->get('start_date', now()->startOfWeek()->format('Y-m-d'));
        $endDate = $request->get('end_date', now()->format('Y-m-d'));

        $callLogs = CallLog::list($perPage, $fromNameFilter, $startDate, $endDate);
        $fromNames = CallLog::getDistinctFromNames();

        // Calculate stats for the same filtered data
        $statsQuery = CallLog::query();

        // Apply the same filtering as the main list
        if (! Gate::allows('is-admin')) {
            CallLog::applyUserNameFilter($statsQuery);
        }

        if ($fromNameFilter && $fromNameFilter !== 'all') {
            $statsQuery->where('from_name', 'LIKE', $fromNameFilter.'%');
        }

        // Apply date filtering
        if ($startDate) {
            try {
                $startDateTime = \Carbon\Carbon::parse($startDate)->startOfDay();
                $statsQuery->where('start_time', '>=', $startDateTime);
            } catch (\Exception $e) {
                // Invalid date format, ignore filter
            }
        }

        if ($endDate) {
            try {
                $endDateTime = \Carbon\Carbon::parse($endDate)->endOfDay();
                $statsQuery->where('start_time', '<=', $endDateTime);
            } catch (\Exception $e) {
                // Invalid date format, ignore filter
            }
        }

        // Calculate stats
        $totalCalls = $statsQuery->count();
        $transcribedCalls = $statsQuery->whereNotNull('transcription')->count();

        // For average duration, only include calls that actually connected and have duration > 0
        $successfulCallsQuery = clone $statsQuery;
        $successfulCalls = $successfulCallsQuery->where('result', 'Call connected')
            ->where('duration', '>', 0)
            ->get();

        $totalSuccessfulDuration = $successfulCalls->sum('duration');
        $successfulCallCount = $successfulCalls->count();
        $avgDuration = $successfulCallCount > 0 ? $totalSuccessfulDuration / $successfulCallCount : 0;

        // Calculate total transcription cost
        $callsWithTranscription = $statsQuery->whereNotNull('transcription')->get();
        $totalTranscriptionCost = $callsWithTranscription->sum('transcriptionCost');

        // Format average duration for display
        $avgDurationFormatted = $this->formatDuration($avgDuration);

        $stats = [
            'total_calls' => $totalCalls,
            'transcribed_calls' => $transcribedCalls,
            'avg_duration' => $avgDurationFormatted,
            'total_transcription_cost' => round($totalTranscriptionCost, 4), // Round to 4 decimal places for display
        ];

        return view('ring-central', compact('callLogs', 'fromNames', 'fromNameFilter', 'stats', 'startDate', 'endDate'));
    }

    public function show(CallLog $callLog)
    {
        // Check if non-admin user is trying to view someone else's call
        if (! Gate::allows('is-admin') && ! $callLog->belongsToUser(Auth::user())) {
            Session::flash('errorMessage', 'You can only view your own call logs.');

            return redirect()->route('ringcentral.index');
        }
        Log::debug('Call log: '.$callLog->id);

        $accessToken = CallLog::getRingCentralAccessToken();

        // Initiate the upload to Gemini
        $callLog->uploadAudioToGemini();

        return view('ring-central-details', compact('callLog', 'accessToken'));
    }

    public function webhook(Request $request)
    {
        Log::debug('RingCentral webhook received');
        // Log::debug($request->all());

        $validationToken = $request->header('validation-token');

        return response('', 200)->header('Validation-Token', $validationToken)->header('Content-Type', 'application/json');
    }

    public function createWebhook()
    {
        Log::debug('Creating webhook');

        $accessToken = CallLog::getRingCentralAccessToken();

        $response = Http::withHeaders(['Authorization' => 'Bearer '.$accessToken])->post(self::RING_CENTRAL_URL.'subscription', [
            'eventFilters' => ['/restapi/v1.0/account/'.self::ACCOUNT_ID.'/telephony/sessions'],
            'deliveryMode' => [
                'transportType' => 'WebHook',
                'address' => 'https://linkedin.insurely.ca/api/ringcentral/webhook',
            ],
            // 'expiresIn' => 315360000, // 10 years
        ]);

        if ($response->status() != 200) {
            Log::error('Failed to create webhook');
            Log::error($response->body());

            return response()->json(['error' => 'Failed to create webhook', 'data' => json_decode($response->body())], 400);
        }

        $response = $response->json();

        Log::debug('Webhook created');
        Log::debug($response);

        return response()->json(['message' => 'Webhook created', 'data' => $response], 200);
    }

    public static function getCallLog()
    {
        $accessToken = CallLog::getRingCentralAccessToken();
        $queryParams = [
            'page' => 1,
            'perPage' => 500,
            'recordingType' => 'Automatic',
        ];

        $response = Http::withHeaders(['Authorization' => 'Bearer '.$accessToken])->get(self::RING_CENTRAL_URL.'account/'.self::ACCOUNT_ID.'/call-log', $queryParams);
        if ($response->status() != 200) {
            Session::flash('errorMessage', 'Failed to get updated call log: '.$response->body());

            return false;
        }
        $records = $response->json()['records'];

        // records -> recording -> contentUri gives us the recording. need to append ?access_token=accessToken to get the recording
        $recordings = [];
        foreach ($records as $record) {
            if (isset($record['recording'])) {
                $recording = $record['recording'];
                $recordingUrl = $recording['contentUri'];
                $recordings[] = [
                    'url' => $recordingUrl,
                    'id' => $record['id'],
                    'from' => $record['from'],
                    'to' => $record['to'],
                    'startTime' => $record['startTime'],
                    'result' => $record['result'],
                ];

                CallLog::updateOrCreate([
                    'ringcentral_id' => $record['id'],
                ], [
                    'session_id' => $record['sessionId'],
                    'duration' => $record['duration'],
                    'direction' => $record['direction'],
                    'result' => $record['result'],
                    'url' => $recordingUrl,
                    'from_name' => $record['from']['name'] ?? null,
                    'from_phone_number' => $record['from']['phoneNumber'] ?? null,
                    'from_location' => $record['from']['location'] ?? null,
                    'to' => $record['to']['phoneNumber'] ?? '',
                    'start_time' => $record['startTime'],
                    'party_id' => $record['partyId'],
                    'telephony_session_id' => $record['telephonySessionId'],
                ]);
            }
        }

        return response()->json(['message' => 'Call log', 'access_token' => $accessToken, 'recordings' => $recordings, 'data' => $response->json()], 200);
    }

    public function getExtension(Request $request)
    {
        $extension = $request->get('extension');
        $accessToken = CallLog::getRingCentralAccessToken();
        $response = Http::withHeaders(['Authorization' => 'Bearer '.$accessToken])
            ->get(self::RING_CENTRAL_URL.'account/'.self::ACCOUNT_ID.'/extension/'.$extension);

        if ($response->status() != 200) {
            return response()->json(['error' => 'Failed to get extension', 'data' => $response->json()], 400);
        }

        return response()->json(['message' => 'Extension', 'data' => $response->json()], 200);
    }

    /**
     * Format duration from seconds to human readable format
     *
     * @param  int  $seconds
     * @return string
     */
    private function formatDuration($seconds)
    {
        if ($seconds < 60) {
            return $seconds.'s';
        } elseif ($seconds < 3600) {
            $minutes = floor($seconds / 60);
            $remainingSeconds = $seconds % 60;

            return $minutes.'m '.$remainingSeconds.'s';
        } else {
            $hours = floor($seconds / 3600);
            $minutes = floor(($seconds % 3600) / 60);

            return $hours.'h '.$minutes.'m';
        }
    }
}
