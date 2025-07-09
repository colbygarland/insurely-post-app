<?php

namespace App\Utils;

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
        $prompts = [
            'agent_performance' => "Please analyze the following transcript between an AI agent (denoted by [Agent]) and a human (denoted by [User]). Focus on evaluating the AI agent's performance, including:\n\n- Communication effectiveness\n- Response quality and relevance\n- Problem-solving approach\n- Professionalism and tone\n- Areas for improvement\n- Overall call success\n\nTranscript:\n{$transcript}",
            'general' => "Please analyze the following transcript and provide insights about the key topics, sentiment, and main points discussed:\n\n{$transcript}",
            'sentiment' => "Please analyze the sentiment of the following transcript, identifying positive, negative, and neutral elements:\n\n{$transcript}",
            'summary' => "Please provide a concise summary of the following transcript, highlighting the main points and key takeaways:\n\n{$transcript}",
            'keywords' => "Please extract the key topics, themes, and important keywords from the following transcript:\n\n{$transcript}",
            'action_items' => "Please identify any action items, decisions, or follow-up tasks mentioned in the following transcript:\n\n{$transcript}",
            'agent_insights' => "Please analyze the AI agent's responses in the following transcript (marked as [Agent]) and provide insights on:\n\n- Response accuracy and helpfulness\n- Communication style and clarity\n- Understanding of user needs\n- Technical competence\n- Suggestions for improvement\n\nTranscript:\n{$transcript}",
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
