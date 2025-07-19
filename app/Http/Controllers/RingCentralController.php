<?php

namespace App\Http\Controllers;

use App\Models\CallLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class RingCentralController extends Controller
{
    private const ACCOUNT_ID = '1254284024';

    public function index()
    {
        return view('ring-central');
    }

    public function webhook(Request $request)
    {
        Log::debug('RingCentral webhook received');
        Log::debug($request->all());

        $validationToken = $request->header('validation-token');

        // TODO: this is where we will handle the webhook, maybe make a call to the API and get the transcript

        return response('', 200)->header('Validation-Token', $validationToken)->header('Content-Type', 'application/json');
    }

    public function createWebhook()
    {
        Log::debug('Creating webhook');

        $accessToken = $this->getAccessToken();

        $response = Http::withHeaders(['Authorization' => 'Bearer '.$accessToken])->post('https://platform.ringcentral.com/restapi/v1.0/subscription', [
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

    public function getCallLog()
    {
        $accessToken = $this->getAccessToken();
        $queryParams = [
            'page' => 1,
            'perPage' => 100,
            'recordingType' => 'Automatic',
        ];

        $response = Http::withHeaders(['Authorization' => 'Bearer '.$accessToken])->get('https://platform.ringcentral.com/restapi/v1.0/account/'.self::ACCOUNT_ID.'/call-log?', implode('&', $queryParams));
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

    private function getAccessToken()
    {
        $authTokenResponse = Http::asForm()
            ->withHeaders([
                'Authorization' => 'Basic '.base64_encode(env('RING_CENTRAL_CLIENT_ID').':'.env('RING_CENTRAL_CLIENT_SECRET')),
            ])
            ->post('https://platform.ringcentral.com/restapi/oauth/token', [
                'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
                'assertion' => env('RING_CENTRAL_JWT'),
            ]);

        if ($authTokenResponse->status() != 200) {
            Log::error('Failed to get auth token');
            Log::error($authTokenResponse->body());

            return response()->json(['error' => 'Failed to get auth token', 'data' => json_decode($authTokenResponse->body())], 400);
        }

        return $authTokenResponse->json()['access_token'];
    }
}
