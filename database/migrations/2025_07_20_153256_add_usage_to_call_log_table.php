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
        Schema::table('call_log', function (Blueprint $table) {
            $table->integer('usage_prompt_token_count')->nullable();
            $table->integer('usage_candidates_token_count')->nullable();
            $table->integer('usage_total_token_count')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {}
};
