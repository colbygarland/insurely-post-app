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
        Schema::table('analyze_transcript_settings', function (Blueprint $table) {
            $table->longText('agent_performance')->change();
            $table->longText('general')->change();
            $table->longText('sentiment')->change();
            $table->longText('summary')->change();
            $table->longText('keywords')->change();
            $table->longText('action_items')->change();
            $table->longText('agent_insights')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
