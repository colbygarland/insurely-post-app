<?php

namespace App\Utils;

use App\Models\Post;
use App\Models\User;
use Carbon\CarbonImmutable;
use Exception;
use Illuminate\Support\Env;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class LinkedInApi
{
    private static $API_VERSION = '202411';

    private static $ORG_ID = '38093924';

    private static $URN = 'urn:li:organization:38093924';

    public static function companySearch(string $accessToken, string $companyName)
    {
        Log::debug('Attempting to look up a company page');

        $urlEncodedName = urlencode($companyName);

        $response = self::get($accessToken, "https://api.linkedin.com/v2/organizations?q=vanityName&vanityName=$urlEncodedName");
        $json = $response->json();

        self::logResponse('Company search', $json);

        return $json;
    }

    public static function getOrganization(string $accessToken)
    {
        Log::debug('Getting organization info');

        $response = self::get($accessToken, 'https://api.linkedin.com/rest/organizations/'.self::$ORG_ID);
        $json = $response->json();

        self::logResponse('Get organization', $json);

        return $json;
    }

    public static function getAccessToken(string $code)
    {
        $redirectUri = Env::get('APP_URL').'/access-token';
        $clientId = Env::get('LINKEDIN_CLIENT_ID');
        $clientSecret = Env::get('LINKEDIN_CLIENT_SECRET');

        Log::debug('Getting an access token from LinkedIn');

        $response = Http::asForm()->post('https://www.linkedin.com/oauth/v2/accessToken', [
            'grant_type' => 'authorization_code',
            'code' => $code,
            'redirect_uri' => $redirectUri,
            'client_id' => $clientId,
            'client_secret' => $clientSecret,
        ]);

        $json = $response->json();

        self::logResponse('Access token', $json);

        return $json;
    }

    public static function createSharePost(Post $post)
    {
        Log::debug('Creating a post on LinkedIn');
        $user = Auth::user() ?? User::where('email', 'kateb@insurely.ca')->first();

        // Create the image first
        // TODO: Ensure the post has an image with it as well
        $image = self::uploadImage($user->linkedin_access_token, $post);

        Log::debug('image: '.json_encode($image));
        Log::debug('Post: '.json_encode($post->toArray()));

        // Then create the post
        // Sending via the connector because the server keeps getting blocked for some reason..
        $response = self::post($user->linkedin_access_token, env('CONNECTOR_URL'), [
            /** Needed if using the connector */
            'accessToken' => $user->linkedin_access_token,
            'linkedin_version' => self::$API_VERSION,
            'restli' => '2.0.0',
            /** end  */
            'author' => self::$URN,
            'lifecycleState' => 'PUBLISHED',
            'commentary' => $post->getSummary(),
            'content' => [
                'media' => [
                    'title' => $post->title,
                    'id' => $image,
                ],
                // If we want an "article" instead of the big image:
                // 'article' => [
                //     'source' => $post->link,
                //     'thumbnail' => $image,
                //     'title' => $post->title,
                //     'description' => $post->title
                // ]
            ],
            'contentLandingPage' => $post->link,
            'visibility' => 'PUBLIC',
            'distribution' => [
                'feedDistribution' => 'MAIN_FEED',
                'targetEntities' => [],
                'thirdPartyDistributionChannels' => [],
            ],
            'isReshareDisabledByAuthor' => false,
        ]);

        if ($response->status() != 201) {
            Log::error('Post did not create');
            throw new Exception($response);
        }

        return true;
    }

    // https://learn.microsoft.com/en-us/linkedin/marketing/community-management/shares/images-api?view=li-lms-2025-01&tabs=http#managing-image-asset
    private static function uploadImage(string $accessToken, Post $post)
    {
        Log::debug('Starting uploadImage()');

        // Register the upload first
        try {
            $registerResponse = self::post($accessToken, 'https://api.linkedin.com/rest/images?action=initializeUpload', [
                'initializeUploadRequest' => [
                    'owner' => self::$URN,
                ],
            ]);
        } catch (\Exception $e) {
            Log::error('Error registering upload: '.$e->getMessage());

            return;
        }

        $registerJson = $registerResponse->json();
        self::logResponse('Register image upload', $registerJson);

        // TODO: handle errors here

        // Upload the URL
        $uploadUrl = $registerJson['value']['uploadUrl'];
        $image = $registerJson['value']['image'];

        if (empty($uploadUrl)) {
            Log::error('Error registering upload: '.json_encode($registerJson));

            return;
        }

        Log::debug("uploadUrl: $uploadUrl");

        // Download the post's image
        $now = CarbonImmutable::now()->toISOString();
        $fileName = $post->id.'_'.$now.'.png';
        /** @disregard */
        Storage::disk('local')->put($fileName, file_get_contents($post->thumbnail_url));
        $path = Storage::path($fileName);

        Log::debug("fileName: $fileName");
        Log::debug("path: $path");

        // Upload the image to Linkedin
        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL => $uploadUrl,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST => 'PUT',
            CURLOPT_HTTPHEADER => [
                "Authorization: Bearer $accessToken",
                'X-Restli-Protocol-Version: 2.0.0',
            ],
            CURLOPT_INFILE => fopen($path, 'r'),
            CURLOPT_INFILESIZE => filesize($path),
            CURLOPT_UPLOAD => true,
        ]);

        $response = curl_exec($curl);
        $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);

        Log::debug('curl response: '.json_encode($response));

        // Remove asset from storage
        Storage::delete($fileName);

        if (curl_errno($curl)) {
            Log::error('cURL Error: '.curl_error($curl));
            throw new Exception('Curl Error');
        } elseif ($httpCode != 201) {
            Log::error('Non 201 status received: '.$httpCode.'\nResponse: '.$response);
            throw new Exception('Non-201 status');
        } else {
            Log::debug('Response: '.$response);
        }
        curl_close($curl);

        return $image; // the image's urn
    }

    private static function logResponse(string $responseName, array $response): void
    {
        Log::debug($responseName.' response: '.json_encode($response, JSON_PRETTY_PRINT));
    }

    private static function post(string $accessToken, string $url, array $data)
    {
        Log::debug("Posting to $url");

        $response = Http::withHeaders([
            'Authorization' => 'Bearer '.$accessToken,
            'X-Restli-Protocol-Version' => '2.0.0',
            'LinkedIn-Version' => self::$API_VERSION,
            'Content-Type' => 'application/json',
        ])->post($url, $data);

        return $response;
    }

    private static function get(string $accessToken, string $url)
    {
        Log::debug("GET from $url");

        $response = Http::withHeaders([
            'Authorization' => 'Bearer '.$accessToken,
            'X-Restli-Protocol-Version' => '2.0.0',
            'LinkedIn-Version' => self::$API_VERSION,
            'Content-Type' => 'application/json',
        ])->get($url);

        return $response;
    }

    private static function put(string $accessToken, string $url, string $data)
    {
        Log::debug("PUT from $url");

        $response = Http::withHeaders([
            'Authorization' => 'Bearer '.$accessToken,
            'X-Restli-Protocol-Version' => '2.0.0',
            'LinkedIn-Version' => self::$API_VERSION,
            'Content-Type' => 'application/json',
        ])->put($url, $data);

        return $response;
    }
}
