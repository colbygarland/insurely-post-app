<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Env;
use Illuminate\Support\Facades\Log;
use Exception;
use App\Utils\LinkedInApi;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class LinkedInController extends Controller
{
    private $redirectUri;
    private $clientId;
    private $authUrl = "https://www.linkedin.com/oauth/v2/authorization";
    private $scopes;

    public function __construct()
    {
        $this->redirectUri = Env::get('APP_URL') . "/access-token";
        $this->clientId = Env::get('LINKEDIN_CLIENT_ID');
        $this->scopes = join('%20', [
            'w_organization_social',
        ]);
    }

    /**
     * Redirect to LinkedIn auth page.
     */
    public function redirectToAuth()
    {
        $responseType = "code";
        Log::debug('redirectToAuth() start');
        return redirect("$this->authUrl?response_type=$responseType&client_id=$this->clientId&scope=$this->scopes&redirect_uri=$this->redirectUri");
    }

    public function saveTokens(Request $request){
        // Get an access token and ID token from LinkedIn
        // Both of these will be used to get the URN, or the member's ID
        Log::debug('makePostToLinkedIn() start');
        Log::debug('Request: ' . json_encode($request->toArray()));
        $tokens = LinkedInApi::getAccessToken($request->query('code'));

        if ( !isset($tokens['access_token']) || !isset($tokens['refresh_token'])) {
            Log::error('No token in response: ' . json_encode($tokens));
            Session::flash('errorMessage', 'Something went wrong. Please try again.');
            return redirect('/');
        }

        $accessToken = $tokens['access_token'];
        $refreshToken = $tokens['refresh_token'];

        $user = $request->user();
        $user->linkedin_access_token = $accessToken;
        $user->linkedin_refresh_token = $refreshToken;
        $user->save();

        Log::debug('User updated: ' . json_encode($user));

        Session::flash('successMessage', 'Successfully authenticated with Linkedin.');
        return redirect('/');
    }

    public function makePostToLinkedIn(Request $request, int $postId)
    {
        // Get an access token and ID token from LinkedIn
        // Both of these will be used to get the URN, or the member's ID
        Log::debug('makePostToLinkedIn() start');
        $post = Post::find($postId);

        try {
            LinkedInApi::createSharePost($post);
        } catch(Exception $e){
            Log::error('Error creating post: ' . $e->getMessage());
            Session::flash('errorMessage', 'Something went wrong. Error: ' . $e->getMessage());
            return redirect('/posts');
        }

        // Mark the post as published
        $post->markPublished();

        Session::flash('successMessage', 'LinkedIn post successfully created.');
        return redirect('/posts');
    }

    public function companySearch(Request $request)
    {
        $company = $request->query('company');
        $user = Auth::user();

        try {
            $response = LinkedInApi::companySearch($user->linkedin_access_token, $company);
        } catch(Exception $e){
            Log::error('Error searching for company: ' . $e->getMessage());
            Session::flash('errorMessage', 'Something went wrong. Error: ' . $e->getMessage());
            return redirect('/');
        }

        return response()->json($response);
    }
}
