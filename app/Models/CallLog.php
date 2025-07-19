<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class CallLog extends Model
{
    protected $table = 'call_log';

    protected $fillable = [
        'ringcentral_id',
        'session_id',
        'duration',
        'direction',
        'result',
        'url',
        'from_name',
        'from_phone_number',
        'from_location',
        'to',
        'start_time',
        'party_id',
        'telephony_session_id',
        'transcription',
    ];

    public static function list($perPage = 25, $fromName = null)
    {
        $query = self::orderBy('start_time', 'desc');

        if ($fromName && $fromName !== 'all') {
            $query->where('from_name', 'LIKE', $fromName.'%');
        }

        return $query->paginate($perPage);
    }

    public static function getDistinctFromNames()
    {
        $allNames = self::whereNotNull('from_name')
            ->where('from_name', '!=', '')
            ->pluck('from_name');

        // Clean names by removing phone numbers and get unique values
        $cleanedNames = $allNames->map(function ($name) {
            return self::cleanFromName($name);
        })->unique()->sort()->values();

        return $cleanedNames;
    }

    public static function cleanFromName($name)
    {
        if (! $name) {
            return $name;
        }

        // Remove phone numbers from the end (patterns like +1234567890, (123) 456-7890, etc.)
        $cleaned = preg_replace('/\s*[\+\(]?[\d\s\-\(\)\.]{10,}$/', '', $name);

        return trim($cleaned);
    }

    public function getTranscript($accessToken)
    {
        if ($this->transcription) {
            return $this->transcription;
        }

        if (! $this->url) {
            return null;
        }

        $apiKey = env('GEMINI_API_KEY');
        if (! $apiKey) {
            Log::error('GEMINI_API_KEY not configured');

            return 'Error: API key not configured';
        }

        try {
            // Step 1: Download the audio file from RingCentral
            $audioResponse = Http::timeout(300)->get($this->url.'?access_token='.$accessToken);

            if (! $audioResponse->successful()) {
                Log::error('Failed to download audio file from: '.$this->url);

                return 'Error: Failed to download audio file';
            }

            $audioData = $audioResponse->body();

            // Step 2: Upload the file to Gemini Files API
            $uploadResponse = Http::withHeaders([
                'X-Goog-Api-Key' => $apiKey,
            ])->attach('file', $audioData, 'audio.mp3', ['Content-Type' => 'audio/mpeg'])
                ->post('https://generativelanguage.googleapis.com/upload/v1beta/files');

            if (! $uploadResponse->successful()) {
                $statusCode = $uploadResponse->status();
                $responseBody = $uploadResponse->body();

                // Handle rate limiting specifically
                if ($statusCode === 429 || str_contains($responseBody, 'quota') || str_contains($responseBody, 'rate limit')) {
                    Log::warning('Gemini API rate limited during file upload: '.$responseBody);

                    return 'Rate Limited: Please try again in a few minutes. The Gemini API has temporary usage limits.';
                }

                Log::error('Failed to upload file to Gemini: '.$responseBody);

                return 'Error: Failed to upload audio to Gemini';
            }

            $uploadedFile = $uploadResponse->json();
            $fileUri = $uploadedFile['file']['uri'];

            // Step 3: Generate transcript using the uploaded file
            $transcriptResponse = Http::withHeaders([
                'Content-Type' => 'application/json',
                'X-Goog-Api-Key' => $apiKey,
            ])->post('https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent', [
                'contents' => [
                    [
                        'parts' => [
                            [
                                'text' => 'Please provide a transcript of this audio recording with speaker separation. Format the output so each speaker gets their own line, like:\n\nSpeaker 1: [what they said]\nSpeaker 2: [what they said]\n\nIf you can identify the speakers by name, use their names instead of Speaker 1/Speaker 2. Return only the transcript text without any additional formatting or commentary.',
                            ],
                            [
                                'file_data' => [
                                    'mime_type' => 'audio/mpeg',
                                    'file_uri' => $fileUri,
                                ],
                            ],
                        ],
                    ],
                ],
            ]);

            if (! $transcriptResponse->successful()) {
                $statusCode = $transcriptResponse->status();
                $responseBody = $transcriptResponse->body();

                // Handle rate limiting specifically
                if ($statusCode === 429 || str_contains($responseBody, 'quota') || str_contains($responseBody, 'rate limit')) {
                    Log::warning('Gemini API rate limited during transcript generation: '.$responseBody);

                    return 'Rate Limited: Please try again in a few minutes. The Gemini API has temporary usage limits.';
                }

                Log::error('Failed to generate transcript: '.$responseBody);

                return 'Error: Failed to generate transcript';
            }

            $transcriptData = $transcriptResponse->json();

            // Extract the transcript text from the response
            $transcript = $transcriptData['candidates'][0]['content']['parts'][0]['text'] ?? 'No transcript generated';

            $this->transcription = trim($transcript);
            $this->save();

            return trim($transcript);

        } catch (\Exception $e) {
            Log::error('Error generating transcript: '.$e->getMessage());

            return 'Error: '.$e->getMessage();
        }
    }

    public static function getRingCentralAccessToken()
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
