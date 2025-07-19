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
    ];

    public static function list()
    {
        return self::orderBy('start_time', 'desc')->get();
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
                Log::error('Failed to upload file to Gemini: '.$uploadResponse->body());

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
                Log::error('Failed to generate transcript: '.$transcriptResponse->body());

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
}
