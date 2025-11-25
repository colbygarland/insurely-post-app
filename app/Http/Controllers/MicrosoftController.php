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

    private $MICROSOFT_API_URL = 'https://graph.microsoft.com/v1.0';

    private $FILE_PATH = 'General/Partnership Folder (Bible)/';

    // Obtained by running a GET on https://graph.microsoft.com/v1.0/sites/$siteId/drives
    private $DRIVE_ID = 'b!cL5ei3so5k6eRDsPoZ3RZ9WrF49ax-FAqGBuj6ScBAR6GrxICGvJTIJ29bDZH7Ft';

    private $ACCESS_TOKEN = '';

    // Used to choose which tab from the Excel sheet we want to use (some have multiple)
    private $WORKSHEET_TAB_MAPPING = [
        'Partnership Doc' => 'Partner Codes',
    ];

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
     * Get the data from a worksheet on an Excel file.
     * https://learn.microsoft.com/en-us/graph/api/worksheet-usedrange?view=graph-rest-1.0&tabs=http
     */
    public function getDataFromWorksheet(Request $request)
    {
        $fileName = $request->get('fileName');
        $fileId = $this->getDriveItemID($fileName);
        $worksheetId = $this->getWorksheetFromWorkbook($fileName, $fileId);
        $accessToken = $this->getAccessToken();
        $url = "$this->MICROSOFT_API_URL/drives/$this->DRIVE_ID/items/$fileId/workbook/worksheets/$worksheetId/usedRange";

        $range = Http::withToken($accessToken)->get($url)->json();
        dd($range['values']);
    }

    /**
     * Used to get an access token.
     * https://learn.microsoft.com/en-us/graph/auth-v2-service?tabs=http#step-3-request-an-access-token
     */
    private function getAccessToken()
    {
        if ($this->ACCESS_TOKEN) {
            return $this->ACCESS_TOKEN;
        }

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

        // Save the access token to be used later
        $this->ACCESS_TOKEN = $body['access_token'];

        return $this->ACCESS_TOKEN;
    }

    /**
     * Get the Site ID to then access files or Excel worksheets.
     * https://learn.microsoft.com/en-us/graph/api/site-get?view=graph-rest-1.0&tabs=http
     */
    private function getSharePointSiteID()
    {
        Log::debug('getSharePointSiteID(): begin');

        $accessToken = $this->getAccessToken();
        $url = "$this->MICROSOFT_API_URL/sites/$this->HOST_NAME:/$this->SITE_PATH";

        $siteResponse = Http::withToken($accessToken)
            ->get($url);

        if ($siteResponse->successful()) {
            // Get the correct site ID from the full site ID
            $siteIds = explode(',', $siteResponse['id']);
            // The drive's site ID is the second one (the first is the sharepoint URL)
            $siteId = $siteIds[1];

            return $siteId;
        }

        Log::error('getSharePointSiteID(): error getting sharepoint site ID');

        return null;
    }

    /**
     * Get the drive item ID for the file.
     * https://learn.microsoft.com/en-us/graph/api/driveitem-get?view=graph-rest-1.0&tabs=http
     */
    private function getDriveItemID(string $fileName)
    {
        $siteId = $this->getSharePointSiteID();
        $accessToken = $this->getAccessToken();
        $url = "$this->MICROSOFT_API_URL/sites/$siteId/drives/$this->DRIVE_ID/root:/$this->FILE_PATH/$fileName.xlsx";

        $fileResponse = Http::withToken($accessToken)
            ->get($url);

        if ($fileResponse->successful()) {
            return $fileResponse['id'];
        }

        return null;
    }

    /**
     * Get the active worksheet in a workbook.
     * https://learn.microsoft.com/en-us/graph/api/worksheet-list?view=graph-rest-1.0&tabs=http
     */
    private function getWorksheetFromWorkbook(string $fileName, string $fileId)
    {
        $accessToken = $this->getAccessToken();
        $url = "$this->MICROSOFT_API_URL/drives/$this->DRIVE_ID/items/$fileId/workbook/worksheets";

        $worksheets = Http::withToken($accessToken)->get($url);

        if ($worksheets->successful()) {
            $selectedSheet = collect($worksheets['value'])->firstWhere('name', $this->WORKSHEET_TAB_MAPPING[$fileName]);

            return $selectedSheet['name'];
        }

        return null;
    }

    public function callback(Request $request) {}
}
