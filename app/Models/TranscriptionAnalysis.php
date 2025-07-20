<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TranscriptionAnalysis extends Model
{
    protected $table = 'analyze_transcript_settings';

    protected $fillable = [
        'agent_performance',
        'general',
        'sentiment',
        'summary',
        'keywords',
        'action_items',
        'agent_insights',
    ];

    public static function getLatest()
    {
        $analyzeTranscriptSettings = TranscriptionAnalysis::latest()->first();

        if (! $analyzeTranscriptSettings) {
            $analyzeTranscriptSettings = TranscriptionAnalysis::create([
                'agent_performance' => "Please analyze the following transcript between an AI agent (denoted by [Agent]) and a human (denoted by [User]). Focus on evaluating the AI agent's performance, including:\n\n- Communication effectiveness\n- Response quality and relevance\n- Problem-solving approach\n- Professionalism and tone\n- Areas for improvement\n- Overall call success\n\nTranscript:\n",
                'general' => "Please analyze the following transcript and provide insights about the key topics, sentiment, and main points discussed:\n\n",
                'sentiment' => "Please analyze the sentiment of the following transcript, identifying positive, negative, and neutral elements:\n\n",
                'summary' => "Please provide a concise summary of the following transcript, highlighting the main points and key takeaways:\n\n",
                'keywords' => "Please extract the key topics, themes, and important keywords from the following transcript:\n\n",
                'action_items' => "Please identify any action items, decisions, or follow-up tasks mentioned in the following transcript:\n\n",
                'agent_insights' => "Please analyze the AI agent's responses in the following transcript (marked as [Agent]) and provide insights on:\n\n- Response accuracy and helpfulness\n- Communication style and clarity\n- Understanding of user needs\n- Technical competence\n- Suggestions for improvement\n\nTranscript:\n",
            ]);
        }

        return $analyzeTranscriptSettings;
    }
}
