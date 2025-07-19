<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class CallLog extends Model
{
    protected $table = 'call_log';

    /**
     * Call log entries that should be visible to all users (not tied to specific individuals)
     */
    protected static $sharedCallNames = [
        // Uncomment if we want to show these to the users
        // 'New Quotes',
        // 'Other Inquiries',
        // 'Overflow - Teamwork!',
        // 'Existing Policies',
    ];

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

    /**
     * Apply user name filtering that handles both full names and "FirstName LastInitial" patterns,
     * plus shared call entries that are visible to all users
     */
    public static function applyUserNameFilter($query)
    {
        $userName = Auth::user()->name;

        $query->where(function ($mainQuery) use ($userName) {
            // Add user-specific name matching
            $mainQuery->where(function ($userQuery) use ($userName) {
                // Split the name into parts
                $nameParts = explode(' ', trim($userName));

                if (count($nameParts) >= 2) {
                    $firstName = $nameParts[0];
                    $lastInitial = substr($nameParts[1], 0, 1);

                    // Match either full name or "FirstName LastInitial" pattern
                    $userQuery->where('from_name', 'LIKE', '%'.$userName.'%')
                        ->orWhere('from_name', 'LIKE', '%'.$firstName.' '.$lastInitial.'%')
                        ->orWhere('from_name', 'LIKE', '%'.$firstName.' '.$lastInitial.'.%');
                } else {
                    // Fallback to original exact match if name format is unexpected
                    $userQuery->where('from_name', 'LIKE', '%'.$userName.'%');
                }
            });

            // Add shared call names that everyone can see
            foreach (self::$sharedCallNames as $sharedName) {
                $mainQuery->orWhere('from_name', 'LIKE', '%'.$sharedName.'%');
            }
        });

        return $query;
    }

    /**
     * Check if this call log belongs to the given user using flexible name matching,
     * or if it's a shared call that everyone can access
     */
    public function belongsToUser($user)
    {
        $fromName = $this->from_name;

        // Check if this is a shared call first
        if (self::isSharedCallName($fromName)) {
            return true;
        }

        // Check user-specific name matching
        $userName = $user->name;

        // Direct match
        if (stripos($fromName, $userName) !== false) {
            return true;
        }

        // Split the user name into parts
        $nameParts = explode(' ', trim($userName));

        if (count($nameParts) >= 2) {
            $firstName = $nameParts[0];
            $lastInitial = substr($nameParts[1], 0, 1);

            // Check for "FirstName LastInitial" patterns
            $patterns = [
                $firstName.' '.$lastInitial,
                $firstName.' '.$lastInitial.'.',
            ];

            foreach ($patterns as $pattern) {
                if (stripos($fromName, $pattern) !== false) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Get the list of shared call names that are visible to all users
     */
    public static function getSharedCallNames()
    {
        return self::$sharedCallNames;
    }

    /**
     * Add a new shared call name that will be visible to all users
     */
    public static function addSharedCallName($name)
    {
        if (! in_array($name, self::$sharedCallNames)) {
            self::$sharedCallNames[] = $name;
        }
    }

    /**
     * Remove a shared call name
     */
    public static function removeSharedCallName($name)
    {
        self::$sharedCallNames = array_filter(self::$sharedCallNames, function ($sharedName) use ($name) {
            return $sharedName !== $name;
        });
    }

    /**
     * Check if a name (raw or cleaned) should be considered a shared call
     */
    public static function isSharedCallName($name)
    {
        $cleanedName = self::cleanFromName($name);

        foreach (self::$sharedCallNames as $sharedName) {
            // Check both raw and cleaned name against shared names
            if (stripos($name, $sharedName) !== false ||
                stripos($cleanedName, $sharedName) !== false ||
                stripos($sharedName, $name) !== false ||
                stripos($sharedName, $cleanedName) !== false) {
                return true;
            }
        }

        return false;
    }

    public static function list($perPage = 25, $fromName = null, $startDate = null, $endDate = null)
    {
        $query = self::orderBy('start_time', 'desc');

        // Apply user-based filtering for non-admin users
        if (! Gate::allows('is-admin')) {
            self::applyUserNameFilter($query);
        }

        if ($fromName && $fromName !== 'all') {
            $query->where('from_name', 'LIKE', $fromName.'%');
        }

        // Apply date filtering
        if ($startDate) {
            try {
                $startDateTime = \Carbon\Carbon::parse($startDate)->startOfDay();
                $query->where('start_time', '>=', $startDateTime);
            } catch (\Exception $e) {
                // Invalid date format, ignore filter
            }
        }

        if ($endDate) {
            try {
                $endDateTime = \Carbon\Carbon::parse($endDate)->endOfDay();
                $query->where('start_time', '<=', $endDateTime);
            } catch (\Exception $e) {
                // Invalid date format, ignore filter
            }
        }

        return $query->paginate($perPage);
    }

    public static function getRecentCallLogs()
    {
        if (Gate::allows('is-admin')) {
            return self::orderBy('start_time', 'desc')
                ->limit(5)
                ->get();
        }

        $query = self::orderBy('start_time', 'desc')->limit(5);
        self::applyUserNameFilter($query);

        return $query->get();
    }

    public static function getDistinctFromNames()
    {
        $query = self::whereNotNull('from_name')
            ->where('from_name', '!=', '');

        // Apply user-based filtering for non-admin users
        if (! Gate::allows('is-admin')) {
            self::applyUserNameFilter($query);
        }

        $allNames = $query->pluck('from_name');

        // Clean names by removing phone numbers and get unique values
        $cleanedNames = $allNames->map(function ($name) {
            return self::cleanFromName($name);
        })->unique();

        // Separate shared call names from the rest using the helper method
        $shared = $cleanedNames->filter(function ($name) {
            return self::isSharedCallName($name);
        });

        $nonShared = $cleanedNames->reject(function ($name) {
            return self::isSharedCallName($name);
        })->sort()->values();

        // Merge non-shared (alphabetized) with shared (at the end)
        $result = $nonShared->merge($shared->values());

        return $result->values();
    }

    public static function cleanFromName($name)
    {
        // Remove phone numbers from the end (patterns like +1234567890, (123) 456-7890, etc.)
        $cleaned = preg_replace('/\s*[\+\(]?[\d\s\-\(\)\.]{10,}$/', '', $name);

        // Remove the English - prepended text
        $cleaned = preg_replace('/^English - /', '', $cleaned);

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
