<?php

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
        Schema::create('analyze_transcript_settings', function (Blueprint $table) {
            $table->id();
            $table->string('agent_performance');
            $table->string('general');
            $table->string('sentiment');
            $table->string('summary');
            $table->string('keywords');
            $table->string('action_items');
            $table->string('agent_insights');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('analyze_transcript_settings');
    }
};
