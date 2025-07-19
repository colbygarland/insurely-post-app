<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class CallLogSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $callLogs = [
            [
                'from_name' => 'John Doe',
                'to' => '1234567890',
                'duration' => 30,
            ],
        ];
    }
}
