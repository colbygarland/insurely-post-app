<?php

namespace App\Http\Controllers;

use App\Models\CallLog;

class CallLogController extends Controller
{
    public function list()
    {
        $callLogs = CallLog::list();

        return response()->json(['message' => 'Call logs', 'count' => $callLogs->count(), 'data' => $callLogs], 200);
    }
}
