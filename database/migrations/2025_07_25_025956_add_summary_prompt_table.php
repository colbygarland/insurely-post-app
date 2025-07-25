<?php

use App\Models\SummaryPrompt;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('summary_prompt', function (Blueprint $table) {
            $table->id();
            $table->longText('prompt');
            $table->timestamps();
        });

        SummaryPrompt::create([
            'prompt' => 'Please provide a concise summary of this phone call transcript in a single paragraph. IMPORTANT: Keep the summary under 200 words and focus only on the most essential information. Include the main purpose of the call, key discussion points, any decisions made or actions taken, and the outcome. Be direct and factual, omitting small talk and focusing on business-relevant content. Here is the transcript:',
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
