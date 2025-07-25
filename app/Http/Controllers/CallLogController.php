<?php

namespace App\Http\Controllers;

use App\Models\CallLog;
use App\Models\SummaryPrompt;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CallLogController extends Controller
{
    public function list()
    {
        $callLogs = CallLog::list();

        return response()->json(['message' => 'Call logs', 'count' => $callLogs->count(), 'data' => $callLogs], 200);
    }

    public function generateTranscript(CallLog $callLog)
    {
        try {
            $transcript = $callLog->getTranscript();
            $callLog->getSummary();

            if (str_starts_with($transcript, 'Error:')) {
                return back()->with('errorMessage', $transcript);
            }

            return back()->with('successMessage', 'Transcript and summary generated successfully!');

        } catch (\Exception $e) {
            Log::error('Error generating transcript: '.$e->getMessage());

            return back()->with('errorMessage', 'An error occurred while generating the transcript');
        }
    }

    public function updateSummaryPrompt(Request $request)
    {
        $request->validate([
            'summary_prompt' => 'required|string|max:1000',
        ]);

        SummaryPrompt::create(['prompt' => $request->summary_prompt]);

        return back()->with('successMessage', 'Summary prompt updated successfully!');
    }
}
