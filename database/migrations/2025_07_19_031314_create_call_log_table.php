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
        Schema::create('call_log', function (Blueprint $table) {
            $table->id();
            $table->string('ringcentral_id');
            $table->string('session_id');
            $table->integer('duration');
            $table->string('direction');
            $table->string('result');
            $table->string('url');
            $table->string('from');
            $table->string('to');
            $table->string('start_time');
            $table->string('party_id');
            $table->string('telephony_session_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('call_log');
    }
};
