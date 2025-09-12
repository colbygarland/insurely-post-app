<?php

namespace App\Console\Commands;

use App\Models\AnalyzePrompt;
use Illuminate\Console\Command; // Make sure to use your model

class AddAnalyzePromptRecord extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:add-analyze-prompt';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Adds a new record to the analyze_prompt table.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        try {
            AnalyzePrompt::create([
                'prompt' => 'Your new prompt goes here.',
            ]);

            $this->info('Record successfully added to the analyze_prompt table.');

            return 0;
        } catch (\Exception $e) {
            $this->error('Failed to add record: '.$e->getMessage());

            return 1;
        }
    }
}
