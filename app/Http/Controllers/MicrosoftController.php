<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class MicrosoftController extends Controller
{
    /**
     * Used to request admin consent for the Insight app.
     * Needs to be ran by an administrator account in Microsoft Entra.
     * Needs only to be done once (or, each time permissions are updated)
     * More details here: https://learn.microsoft.com/en-us/graph/auth-v2-service?tabs=http
     */
    public function getAdminConsent()
    {
        $tenant = env('MICROSOFT_ENTRA_TENANT_ID');
        $clientId = env('MICROSOFT_ENTRA_APPLICATION_ID');
        $redirectUri = env('MICROSOFT_ENTRA_REDIRECT_URI');
        $adminConsentResponse = Http::get("https://login.microsoftonline.com/$tenant/adminconsent?client_id=$clientId&redirect_uri=$redirectUri");

        if (! $adminConsentResponse->successful()) {
            Log::error('Error getting admin consent to Microsoft');

            return 500;
        }

        Log::debug('Success getting admin consent to Microsoft');
        $adminConsentData = $adminConsentResponse->body();
        Log::debug("adminConsentData = $adminConsentData");

        return 200;
    }

    public function callback(Request $request) {}
}
