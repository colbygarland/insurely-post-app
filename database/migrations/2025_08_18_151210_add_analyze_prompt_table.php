<?php

use App\Models\AnalyzePrompt;
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
        Schema::create('analyze_prompt', function (Blueprint $table) {
            $table->id();
            $table->longText('prompt');
            $table->timestamps();
        });

        AnalyzePrompt::create([
            'prompt' => 'example prompt',
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
