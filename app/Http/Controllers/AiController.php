<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;

class AiController extends Controller
{
    public function index()
    {
        return view('outbound-call');
    }

    public function outboundCall(Request $request)
    {
        $number = $request->number;
        $firstName = $request->firstName;
        $lastName = $request->lastName;
        $email = $request->email;
        $timezone = $request->timezone;
        $isWebUI = $request->isWebUI;

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

        try {
            $response = Http::post(env('OUTBOUND_CALLER_URL').'/outbound-call', [
                'number' => $number,
                'firstName' => $firstName,
                'lastName' => $lastName,
                'email' => $email,
                'timezone' => $timezone,
                'caller_api_key' => env('OUTBOUND_CALLER_API_KEY'),
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
}
