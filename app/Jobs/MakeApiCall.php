<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;

class MakeApiCall implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected array $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function handle()
    {
        // "Record ID - Contact","First Name","Last Name","Email","Phone Number"
        $id = $this->data[0];
        $firstName = $this->data[1];
        $lastName = $this->data[2];
        $email = $this->data[3];
        $phone = $this->data[4];
        $timezone = 'America/Edmonton';

        Http::post(env('OUTBOUND_CALLER_URL').'/outbound-call', [
            'number' => $phone,
            'firstName' => $firstName,
            'lastName' => $lastName,
            'email' => $email,
            'timezone' => $timezone,
            'id' => $id,
            'caller_api_key' => env('OUTBOUND_CALLER_API_KEY'),
        ]);
    }
}
