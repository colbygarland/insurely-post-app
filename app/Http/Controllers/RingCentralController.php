<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class RingCentralController extends Controller
{
    public function index()
    {
        return view('ring-central');
    }

    public function webhook(Request $request)
    {
        Log::debug('RingCentral webhook received');
        Log::debug($request->all());

        $validationToken = $request->header('validation-token');

        return response('', 200)->header('Validation-Token', $validationToken)->header('Content-Type', 'application/json');
    }

    public function createWebhook()
    {
        Log::debug('Creating webhook');

        // Get an auth token first
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

        $accessToken = $authTokenResponse->json()['access_token'];

        $response = Http::withHeaders(['Authorization' => 'Bearer '.$accessToken])->post('https://platform.ringcentral.com/restapi/v1.0/subscription', [
            'eventFilters' => ['/restapi/v1.0/account/{accountId}/telephony/sessions'],
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
}
