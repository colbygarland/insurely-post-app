<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class MicrosoftController extends Controller
{
    // The typo is intentional
    private $HOST_NAME = 'insurley.sharepoint.com';

    private $SITE_PATH = 'sites/InsurelyInc';

    private $MICROSOFT_API_URL = 'https://graph.microsoft.com/v1.0/sites';

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

    /**
     * Used to get an access token.
     * https://learn.microsoft.com/en-us/graph/auth-v2-service?tabs=http#step-3-request-an-access-token
     */
    private function getAccessToken()
    {
        $tenant = env('MICROSOFT_ENTRA_TENANT_ID');
        $response = Http::asForm()->post("https://login.microsoftonline.com/$tenant/oauth2/v2.0/token", [
            'client_id' => env('MICROSOFT_ENTRA_APPLICATION_ID'),
            'scope' => 'https://graph.microsoft.com/.default',
            'client_secret' => env('MICROSOFT_ENTRA_VALUE'),
            'grant_type' => 'client_credentials',
        ]);

        $body = $response->json();

        if (! $response->successful()) {
            Log::error('Error getting access token from Microsoft');

            return $body;
        }

        Log::debug('Success getting access token from Microsoft');

        return $body['access_token'];
    }

    /**
     * Used to get a session
     * https://learn.microsoft.com/en-us/graph/api/resources/excel?view=graph-rest-1.0#api-call-to-get-a-session
     */
    public function getWorkbookSession() {}

    /**
     * Get the Site ID to then access files or Excel worksheets.
     * https://learn.microsoft.com/en-us/graph/api/site-get?view=graph-rest-1.0&tabs=http
     */
    private function getSharePointSiteID()
    {
        Log::debug('getSharePointSiteID(): begin');

        $accessToken = $this->getAccessToken();
        $url = "$this->MICROSOFT_API_URL/$this->HOST_NAME:/$this->SITE_PATH";

        $siteResponse = Http::withToken($accessToken)
            ->get($url);

        if ($siteResponse->successful()) {
            return $siteResponse['id'];
        }

        Log::error('getSharePointSiteID(): error getting sharepoint site ID');

        return null;
    }

    public function callback(Request $request) {}
}
