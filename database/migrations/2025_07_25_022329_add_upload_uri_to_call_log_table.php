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
            $table->string('upload_uri')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {}
};
