<?php

namespace App\Http\Controllers;

use App\Models\CallLog;

class CallLogController extends Controller
{
    public function list()
    {
        $callLogs = CallLog::orderBy('start_time', 'desc')->get();

        return response()->json(['message' => 'Call logs', 'count' => $callLogs->count(), 'data' => $callLogs], 200);
    }
}
