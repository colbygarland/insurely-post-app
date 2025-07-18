<?php

namespace App\Models;

use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class Conversation extends Model
{
    protected $fillable = [
        'message',
        'phone',
        'first_name',
        'last_name',
        'email',
        'conversation_id',
    ];

    public function getSummary()
    {
        $elevenLabsApiKey = env('ELEVENLABS_API_KEY');

        try {
            $response = Http::withHeaders([
                'xi-api-key' => $elevenLabsApiKey,
            ])->get('https://api.elevenlabs.io/v1/convai/conversations/'.$this->conversation_id);

            $data = $response->json();

            return $data['analysis']['transcript_summary'];
        } catch (Exception $e) {
            Log::error('Error getting summary: '.$e->getMessage());

            return null;
        }
    }
}
