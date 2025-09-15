<?php

namespace App\Http\Controllers;

use App\Models\AnalyzePrompt;
use App\Models\CallLog;
use App\Models\SummaryPrompt;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CallLogController extends Controller
{
    public static $minimumDuration = 90; // 1.5 minutes

    public static $maximumDuration = 1500; // 25 minutes

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
        // Process records in smaller chunks to reduce memory usage
        $chunkSize = 3; // Reduced from 10 to 3 for better memory management
        $processedCount = 0;
        $maxRecords = 10; // Total limit

        Log::debug('[autoGenerateTranscripts] Starting at: '.now()->format('Y-m-d H:i:s'));

        do {
            // Get a small chunk of records using chunked iteration
            $unTranscribedCalls = CallLog::where(function ($query) {
                $query->whereNull('transcription')
                    ->orWhereNull('summary')
                    ->orWhereNull('analysis');
            })
                ->where('duration', '<', self::$maximumDuration)
                ->where('duration', '>', self::$minimumDuration)
                ->where('created_at', '>=', '2025-08-25T17:55:42.000000Z')
                ->orderBy('created_at', 'desc')
                ->limit($chunkSize)
                ->get();

            if ($unTranscribedCalls->isEmpty()) {
                break; // No more records to process
            }

            $callLogIds = $unTranscribedCalls->pluck('id')->toArray();
            Log::debug('[autoGenerateTranscripts] Processing chunk: '.implode(', ', $callLogIds));

            // Process each record individually with memory cleanup
            foreach ($unTranscribedCalls as $callLog) {
                try {
                    // Process transcript, summary, and analysis
                    $callLog->getTranscript();
                    $callLog->getSummary();
                    $callLog->getAnalysis();

                    $processedCount++;
                    Log::debug('[autoGenerateTranscripts] Processed call log ID: '.$callLog->id);

                } catch (\Exception $e) {
                    Log::error('[autoGenerateTranscripts] Error processing call log ID '.$callLog->id.': '.$e->getMessage());
                    // Continue processing other records even if one fails
                }

                // Explicit memory cleanup after each record
                unset($callLog);
            }

            // Clear the collection and force garbage collection
            $unTranscribedCalls = null;
            unset($unTranscribedCalls);

            // Force garbage collection to free memory
            if (function_exists('gc_collect_cycles')) {
                gc_collect_cycles();
            }

            // Add a small delay to prevent overwhelming the API
            sleep(1);

        } while ($processedCount < $maxRecords);

        Log::debug('[autoGenerateTranscripts] Completed at: '.now()->format('Y-m-d H:i:s').'. Total processed: '.$processedCount);

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

    /**
     * Ultra memory-efficient version that processes one record at a time directly from database
     * Use this if you're still experiencing memory issues with the chunked version
     */
    public static function autoGenerateTranscriptsMemoryOptimized()
    {
        $processedCount = 0;
        $maxRecords = 10;

        Log::debug('[autoGenerateTranscriptsMemoryOptimized] Starting at: '.now()->format('Y-m-d H:i:s'));

        // Process records one at a time using cursor pagination
        $query = CallLog::where(function ($query) {
            $query->whereNull('transcription')
                ->orWhereNull('summary')
                ->orWhereNull('analysis');
        })
            ->where('duration', '<', self::$maximumDuration)
            ->where('duration', '>', self::$minimumDuration)
            ->where('created_at', '>=', '2025-08-25T17:55:42.000000Z')
            ->orderBy('created_at', 'desc');

        // Use cursor to process records without loading them all into memory
        $query->chunk(1, function ($callLogs) use (&$processedCount, $maxRecords) {
            if ($processedCount >= $maxRecords) {
                return false; // Stop processing
            }

            $callLog = $callLogs->first();

            try {
                Log::debug('[autoGenerateTranscriptsMemoryOptimized] Processing call log ID: '.$callLog->id);

                $callLog->getTranscript();
                $callLog->getSummary();
                $callLog->getAnalysis();

                $processedCount++;

            } catch (\Exception $e) {
                Log::error('[autoGenerateTranscriptsMemoryOptimized] Error processing call log ID '.$callLog->id.': '.$e->getMessage());
            }

            // Explicit cleanup
            unset($callLog, $callLogs);

            // Force garbage collection after each record
            if (function_exists('gc_collect_cycles')) {
                gc_collect_cycles();
            }

            // Small delay to prevent API rate limiting
            sleep(1);

            return $processedCount < $maxRecords; // Continue if under limit
        });

        Log::debug('[autoGenerateTranscriptsMemoryOptimized] Completed at: '.now()->format('Y-m-d H:i:s').'. Total processed: '.$processedCount);

        return true;
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
