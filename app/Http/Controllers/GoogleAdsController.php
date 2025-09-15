<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class GoogleAdsController extends Controller
{
    public function webhook(Request $request)
    {
        $webhookKey = env('GOOGLE_ADS_WEBHOOK_KEY');

        if ($request->header('X-Goog-Channel-Token') !== $webhookKey) {
            return response()->json(['message' => 'Invalid webhook key'], 401);
        }

        // TODO: send the data to the Hubspot integration

        Log::info('Google Ads webhook received');
        Log::info($request->all());

        return response()->json(['message' => 'Google Ads webhook received']);
    }
}
