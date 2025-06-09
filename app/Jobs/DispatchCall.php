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

            if (! Storage::exists('public/uploads/ocd.csv')) {
                throw new Exception('CSV file not found');
            }

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

            if (empty($rows)) {
                Log::warning('DispatchCall: No valid rows found in CSV file');
                Storage::delete('public/uploads/ocd.csv');

                return;
            }

            $totalRows = count($rows);
            $delayBetweenJobs = ($this->minutes * 60) / $totalRows;
            Log::info('DispatchCall: Will process '.$totalRows.' rows over '.$this->minutes.' minutes');

            $processedRows = 0;
            foreach ($rows as $index => $row) {
                if (count($row) >= 5) {  // Make sure we have all required fields
                    $id = $row[0];
                    $firstName = $row[1];
                    $lastName = $row[2];
                    $email = $row[3];
                    $phone = $row[4];

                    // Basic validation
                    if (! filter_var($email, FILTER_VALIDATE_EMAIL)) {
                        Log::warning("DispatchCall: Invalid email format for row {$index}: {$email}");

                        continue;
                    }

                    $data = [$id, $firstName, $lastName, $email, $phone];
                    MakeApiCall::dispatch($data)->delay(now()->addSeconds($index * $delayBetweenJobs));
                    $processedRows++;
                }
            }

            Log::info("DispatchCall: Successfully dispatched {$processedRows} API calls");

            // Only delete the file if we successfully processed all rows
            if ($processedRows === $totalRows) {
                Storage::delete('public/uploads/ocd.csv');
                Log::info('DispatchCall: CSV file deleted after successful processing');
            } else {
                Log::warning("DispatchCall: Some rows were skipped. File not deleted. Processed {$processedRows} of {$totalRows} rows");
            }
        } catch (Exception $e) {
            Log::error('DispatchCall: Error processing CSV file: '.$e->getMessage());
            Log::error('DispatchCall: Stack trace: '.$e->getTraceAsString());
            throw $e;
        }
    }
}
