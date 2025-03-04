<?php

namespace App\Http\Controllers;

use Aloha\Twilio\Twilio;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;

class AiController extends Controller
{
    private $twilioClient;

    public function __construct()
    {
        // Ensure env variables are set
        if (! env('ELEVENLABS_API_KEY')) {
            throw new Exception('Missing required environment variables.');
        }

        $this->twilioClient = new Twilio(env('TWILIO_ACCOUNT_SID'), env('TWILIO_AUTH_TOKEN'), env('TWILIO_PHONE_NUMBER'));
    }

    public function outboundCall(Request $request)
    {
        $host = $request->headers->get('host');
        $number = $request->input('number');
        $prompt = urlencode($request->input('prompt', ''));
        $firstMessage = urlencode($request->input('first_message', ''));

        $twimlUrl = url("/ai/outbound-call-twiml?prompt={$prompt}&first_message={$firstMessage}");

        try {
            $call = $this->twilioClient->call($number, $twimlUrl, [
                'from' => env('TWILIO_PHONE_NUMBER')]);
            Log::debug('Initiated outbound call: '.$call);

            return response()->json(['success' => true, 'callSid' => $call], Response::HTTP_OK);
        } catch (Exception $exception) {
            Log::error('Error initiating outbound call: '.$exception->getMessage());

            return response()->json(['error' => 'Failed to initiate call.', 'success' => false], Response::HTTP_BAD_REQUEST);
        }
    }

    public function outboundCallTwiml(Request $request)
    {
        $prompt = $request->query('prompt', '');
        $firstMessage = $request->query('first_message', '');

        $twimlResponse = '<?xml version="1.0" encoding="UTF-8"?>
    <Response>
        <Connect>
            <Stream url="wss://'.$request->getHost().'/outbound-media-stream">
                <Parameter name="prompt" value="'.htmlspecialchars($prompt, ENT_QUOTES, 'UTF-8').'" />
                <Parameter name="first_message" value="'.htmlspecialchars($firstMessage, ENT_QUOTES, 'UTF-8').'" />
            </Stream>
        </Connect>
    </Response>';

        return new Response($twimlResponse, 200, ['Content-Type' => 'text/xml']);
    }
}
