<?php

namespace App\Console\Commands;

use App\Http\Controllers\CallLogController;
use App\Http\Controllers\RingCentralController;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class AutoGenerateTranscripts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:auto-generate-transcripts';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Automatically generate transcripts for all call logs that have not been processed';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        echo "AutoGenerateTranscripts - Getting call log\n";
        Log::debug('AutoGenerateTranscripts - Getting call log');
        RingCentralController::getCallLog();
        Log::debug('AutoGenerateTranscripts - Generating transcripts');
        echo 'AutoGenerateTranscripts - Generating transcripts';
        CallLogController::autoGenerateTranscripts();

        return 0;
    }
}
