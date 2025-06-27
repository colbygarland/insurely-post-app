<?php

namespace App\Http\Controllers;

use App\Models\Conversation;
use Illuminate\Http\Request;

class ConversationController extends Controller
{
    private function checkApiKey(Request $request)
    {
        $apiKey = $request->caller_api_key;
        if ($apiKey !== env('CONVERSATION_API_KEY')) {
            throw new \Exception('Unauthorized');
        }
    }

    public function store(Request $request)
    {
        $this->checkApiKey($request);

        $request->validate([
            'message' => 'required',
            'phone' => 'required',
            'email' => 'required',
            'first_name' => 'required',
            'last_name' => 'required',
        ]);

        Conversation::create($request->all());

        return response()->json(['message' => 'Conversation created successfully']);
    }

    public function list()
    {
        $perPage = 25;
        $conversations = Conversation::orderBy('created_at', 'desc')->paginate($perPage);

        return response()->json([
            'data' => $conversations->items(),
            'pagination' => [
                'current_page' => $conversations->currentPage(),
                'last_page' => $conversations->lastPage(),
                'per_page' => $conversations->perPage(),
                'total' => $conversations->total(),
                'from' => $conversations->firstItem(),
                'to' => $conversations->lastItem(),
                'has_more_pages' => $conversations->hasMorePages(),
                'prev_page_url' => $conversations->previousPageUrl(),
                'next_page_url' => $conversations->nextPageUrl(),
            ],
        ]);
    }

    public function show(Conversation $conversation)
    {
        return view('conversation', [
            'conversation' => $conversation,
        ]);
    }

    public function destroy(Conversation $conversation)
    {
        $conversation->delete();

        return redirect()->route('ai.index')->with('successMessage', 'Conversation deleted successfully');
    }
}
