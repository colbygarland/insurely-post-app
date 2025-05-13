<?php

namespace App\Jobs;

use Exception;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class DispatchCall implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    private $minutes;

    /**
     * Create a new job instance.
     */
    public function __construct($minutes)
    {
        Log::info('DispatchCall: __construct() started');
        $this->minutes = $minutes;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Log::info('DispatchCall: handle() started');
        try {
            Log::info('DispatchCall: About to read CSV file');
            $content = Storage::get('public/uploads/ocd.csv');
            Log::info('DispatchCall: Successfully read CSV file');

            // Convert content to array of rows
            $rows = array_map('str_getcsv', explode("\n", $content));

            // Remove header row
            array_shift($rows);

            // Remove any empty rows
            $rows = array_filter($rows, function ($row) {
                return ! empty(array_filter($row));
            });

            Log::info('DispatchCall: Processed '.count($rows).' rows from CSV');

            $totalRows = count($rows);
            $delayBetweenJobs = ($this->minutes * 60) / $totalRows;
            Log::info('DispatchCall: Will process '.$totalRows.' rows over '.$this->minutes.' minutes');

            foreach ($rows as $index => $row) {
                if (count($row) >= 5) {  // Make sure we have all required fields
                    $id = $row[0];
                    $firstName = $row[1];
                    $lastName = $row[2];
                    $email = $row[3];
                    $phone = $row[4];
                    $data = [$id, $firstName, $lastName, $email, $phone];
                    MakeApiCall::dispatch($data)->delay(now()->addSeconds($index * $delayBetweenJobs));
                }
            }
            Log::info('DispatchCall: Successfully dispatched all API calls');
        } catch (Exception $e) {
            Log::error('DispatchCall: Error processing CSV file: '.$e->getMessage());
            Log::error('DispatchCall: Stack trace: '.$e->getTraceAsString());
            throw $e;
        }
    }
}
