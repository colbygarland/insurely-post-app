<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class GoogleAdsController extends Controller
{
    public function webhook(Request $request)
    {
        Log::info('Google Ads webhook received');
        Log::info($request->all());

        return response()->json(['message' => 'Google Ads webhook received']);
    }
}
