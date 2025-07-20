<?php

namespace App\Utils;

use App\Models\TranscriptionAnalysis;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

class OpenAi
{
    private static $baseUrl = 'https://generativelanguage.googleapis.com/v1beta/models';

    private static $apiKey;

    private static function apiKey()
    {
        if (self::$apiKey === null) {
            self::$apiKey = env('OPEN_AI_API_KEY');
        }

        return self::$apiKey;
    }

    // https://ai.google.dev/gemini-api/docs/text-generation
    private static function sendMessage($message, $model = 'gemini-1.5-flash')
    {
        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'X-goog-api-key' => self::apiKey(),
        ])->timeout(300)->post(self::$baseUrl.'/'.$model.':generateContent', [
            'contents' => [
                [
                    'parts' => [
                        [
                            'text' => $message,
                        ],
                    ],
                ],
            ],
            'generationConfig' => [
                'temperature' => 0.7,
                'maxOutputTokens' => 2048, // Increased for Pro model
            ],
        ]);

        if ($response->failed() || $response->status() !== 200) {
            Log::error('Gemini API Error: '.$response->body());
            Session::flash('errorMessage', 'AI API Error: '.$response->body());

            return null;
        }

        $responseData = $response->json();

        if (isset($responseData['candidates'][0]['content']['parts'][0]['text'])) {
            return $responseData['candidates'][0]['content']['parts'][0]['text'];
        }

        Log::error('Unexpected Gemini API response format: '.$response->body());

        return null;
    }

    // Method specifically for analyzing transcripts
    public static function analyzeTranscript($transcript, $analysisType = 'agent_performance')
    {
        $settings = TranscriptionAnalysis::getLatest();
        $prompts = [
            'agent_performance' => $settings->agent_performance."\n\nTranscript:\n{$transcript}",
            'general' => $settings->general."\n\nTranscript:\n{$transcript}",
            'sentiment' => $settings->sentiment."\n\nTranscript:\n{$transcript}",
            'summary' => $settings->summary."\n\nTranscript:\n{$transcript}",
            'keywords' => $settings->keywords."\n\nTranscript:\n{$transcript}",
            'action_items' => $settings->action_items."\n\nTranscript:\n{$transcript}",
            'agent_insights' => $settings->agent_insights."\n\nTranscript:\n{$transcript}",
        ];

        $prompt = $prompts[$analysisType] ?? $prompts['agent_performance'];

        return self::sendMessage($prompt);
    }

    // Available Gemini models (in order of capability/reliability):
    //
    // Most Capable (Slowest, Most Expensive):
    // - 'gemini-2.5-pro'           - Google's most intelligent model, best for complex analysis
    //
    // Balanced (Good reliability, moderate cost):
    // - 'gemini-1.5-pro-002'       - Default: Most reliable, 2M context window
    // - 'gemini-1.5-pro'           - Stable option, good for complex tasks
    //
    // Faster (Less reliable, cheaper):
    // - 'gemini-2.0-flash'         - Newer Flash model, more stable than 1.5
    // - 'gemini-1.5-flash'         - Original Flash model (can get overloaded)
    // - 'gemini-1.5-flash-8b'      - Lightweight version
}
