<?php

namespace App\Http\Controllers;

use App\Models\AnalyzePrompt;
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
            $callLog->getAnalysis();

            if (str_starts_with($transcript, 'Error:')) {
                return back()->with('errorMessage', $transcript);
            }

            return back()->with('successMessage', 'Transcript, summary, and analysis generated successfully!');

        } catch (\Exception $e) {
            Log::error('Error generating transcript: '.$e->getMessage());

            return back()->with('errorMessage', 'An error occurred while generating the transcript');
        }
    }

    public static function autoGenerateTranscripts()
    {
        $unTranscribedCalls = CallLog::whereNull('transcription')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        // Generate the transcripts
        foreach ($unTranscribedCalls as $callLog) {
            $callLog->getTranscript();
        }

        // Generate the summaries
        foreach ($unTranscribedCalls as $callLog) {
            $callLog->getSummary();
        }

        // Generate the analyses
        foreach ($unTranscribedCalls as $callLog) {
            $callLog->getAnalysis();
        }

        return true;
    }

    public function generateTranscriptOnly(CallLog $callLog)
    {
        try {
            $transcript = $callLog->getTranscript();

            if (str_starts_with($transcript, 'Error:')) {
                return back()->with('errorMessage', $transcript);
            }

            return back()->with('successMessage', 'Transcript generated successfully!');

        } catch (\Exception $e) {
            Log::error('Error generating transcript: '.$e->getMessage());

            return back()->with('errorMessage', 'An error occurred while generating the transcript');
        }
    }

    public function generateSummaryAndAnalysis(CallLog $callLog)
    {
        try {
            // Check if transcript exists first
            if (! $callLog->transcription) {
                return back()->with('errorMessage', 'Transcript must be generated first before creating summary and analysis.');
            }

            $callLog->getSummary();
            $callLog->getAnalysis();

            return back()->with('successMessage', 'Summary and analysis generated successfully!');

        } catch (\Exception $e) {
            Log::error('Error generating summary and analysis: '.$e->getMessage());

            return back()->with('errorMessage', 'An error occurred while generating the summary and analysis');
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

    public function updateAnalyzePrompt(Request $request)
    {
        $request->validate([
            'analyze_prompt' => 'required|string|max:5000',
        ]);

        AnalyzePrompt::create(['prompt' => $request->analyze_prompt]);

        return back()->with('successMessage', 'Analyze prompt updated successfully!');
    }
}
