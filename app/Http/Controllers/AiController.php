<?php

namespace App\Http\Controllers;

use App\Jobs\DispatchCall;
use App\Models\Conversation;
use App\Utils\OpenAi;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;

class AiController extends Controller
{
    private $FILE_NAME = 'ocd.csv';

    public function index()
    {
        $perPage = 25;
        $conversations = Conversation::orderBy('created_at', 'desc')->paginate($perPage);

        return view('outbound-call', [
            'conversations' => $conversations,
        ]);
    }

    public function outboundCall(Request $request)
    {
        $number = $request->number;
        $firstName = $request->firstName;
        $lastName = $request->lastName;
        $email = $request->email;
        $timezone = $request->timezone;
        $id = $request->id;
        $isWebUI = $request->isWebUI;
        $callType = $request->callType;
        if (! $number) {
            return response()->json(['error' => 'Number is required'], 400);
        }
        if (! $firstName) {
            return response()->json(['error' => 'First name is required'], 400);
        }
        if (! $lastName) {
            return response()->json(['error' => 'Last name is required'], 400);
        }
        if (! $email) {
            return response()->json(['error' => 'Email is required'], 400);
        }
        if (! $timezone) {
            return response()->json(['error' => 'Timezone is required'], 400);
        }

        if (! $callType) {
            return response()->json(['error' => 'Call type is required'], 400);
        }

        try {
            $response = Http::post(env('OUTBOUND_CALLER_URL').'/outbound-call', [
                'number' => $number,
                'firstName' => $firstName,
                'lastName' => $lastName,
                'email' => $email,
                'timezone' => $timezone,
                'caller_api_key' => env('OUTBOUND_CALLER_API_KEY'),
                'call_type' => $callType,
            ]);
            $json = $response->json();

            if ($response->successful()) {
                if ($isWebUI) {
                    Session::flash('successMessage', 'Outbound call initiated successfully');

                    return redirect()->route('ai.index');
                } else {
                    return response()->json(['message' => 'Outbound call initiated successfully', 'meta' => $json], 200);
                }

            } else {
                if ($isWebUI) {
                    Session::flash('errorMessage', $json);

                    return redirect()->route('ai.index');
                } else {
                    return response()->json(['message' => 'Failed to initiate outbound call', 'error' => $json], 500);
                }

            }
        } catch (Exception $e) {
            if ($isWebUI) {
                Session::flash('errorMessage', $e->getMessage());

                return redirect()->route('ai.index');
            } else {
                return response()->json(['message' => 'Failed to make outbound call', 'error' => $e->getMessage()], 500);
            }

        }
    }

    public function upload(Request $request)
    {
        $file = $request->file('file');
        // Intentionally overwrite the file each time so there is only one
        $file->storeAs('public/uploads', $this->FILE_NAME);
        $fileUrl = Storage::url($this->FILE_NAME);

        Session::flash('successMessage', 'File uploaded successfully. File url: '.$fileUrl);

        return redirect()->route('ai.index');
    }

    public function process(Request $request)
    {
        $isWebUI = $request->isWebUI;
        $minutes = $request->minutes;

        // Get the full path to the file
        $file = Storage::get('public/uploads/'.$this->FILE_NAME);

        // Check if file exists
        if (! $file) {
            if ($isWebUI) {
                Session::flash('errorMessage', 'File not found. Upload a CSV first.');

                return redirect()->route('ai.index');
            } else {
                return response()->json(['error' => 'File not found. Upload a CSV first.'], 404);
            }
        }

        Log::info('Calling DispatchCall::dispatch()');
        DispatchCall::dispatch($minutes);

        if ($isWebUI) {
            Session::flash('successMessage', 'Job queued.');

            return redirect()->route('ai.index');
        } else {
            return response()->json(['message' => 'Job queued.']);
        }
    }

    public function pushToAiForReview(Conversation $conversation)
    {
        $aiResponse = OpenAi::analyzeTranscript($conversation->message);

        // Store the combined text content as the analyze result
        $conversation->analyze_result = $aiResponse;
        $conversation->save();

        return redirect()->route('ai.conversation.show', $conversation->id);
    }

    public function elevenlabsWebhook(Request $request)
    {
        Log::info('ElevenLabs webhook received');
        Log::info($request->all());

        try {
            $summary = $request->data->analysis->transcript_summary;
        } catch (Exception $e) {
            Log::error('Error parsing ElevenLabs webhook: '.$e->getMessage());

            return response()->json(['error' => 'Error parsing ElevenLabs webhook'], 500);
        }

        return response()->json(['summary' => $summary]);
    }
}
