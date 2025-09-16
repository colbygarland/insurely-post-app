<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class GoogleAdsController extends Controller
{
    public function webhook(Request $request)
    {
        Log::info('[GoogleAdsController] Google Ads webhook received');
        Log::info($request->all());

        $webhookKey = env('GOOGLE_ADS_WEBHOOK_KEY');

        if ($request->input('google_key') !== $webhookKey) {
            return response()->json(['message' => 'Invalid webhook key'], 401);
        }

        Log::debug('[GoogleAdsController] Webhook key is valid');

        // TODO: send the data to the Hubspot integration
        /**
         * {
         * "lead_id": "TeSter-123-ABCDEFGHIJKLMNOPQRSTUVWXYZ-abcdefghijklmnopqrstuvwxyz-0123456789-AaBbCcDdEeFfGgHhIiJjKkLl",
         * "user_column_data": [
         * {
         * "column_name": "User Email",
         * "string_value": "test@example.com",
         * "column_id": "EMAIL"
         * },
         * {
         * "column_name": "User Phone",
         * "string_value": "+16505550123",
         * "column_id": "PHONE_NUMBER"
         * },
         * {
         * "column_name": "Region",
         * "string_value": "California",
         * "column_id": "REGION"
         * },
         * {
         * "column_name": "First Name",
         * "string_value": "FirstName",
         * "column_id": "FIRST_NAME"
         *  },
         * {
         * "column_name": "Last Name",
         * "string_value": "LastName",
         * "column_id": "LAST_NAME"
         * },
         * {
         * "string_value": "Condo",
         * "column_id": "what_type_of_insurance_are_you_looking_for?"
         * },
         * ],
         * "api_version": "1.0",
         * "form_id": 194054118492,
         * "campaign_id": 10000000000,
         * "google_key": "",
         * "is_test": true,
         * "gcl_id": "",
         * "adgroup_id": 20000000000,
         * "creative_id": 30000000000
         * }
         */

        return response()->json(['message' => 'Google Ads webhook received']);
    }
}
