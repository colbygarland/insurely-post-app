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
        Schema::create('posts', function (Blueprint $table) {
            $table->id();
            $table->string('wordpress_id');
            $table->string('date');
            $table->string('date_gmt');
            $table->string('modified');
            $table->string('modified_gmt');
            $table->string('slug');
            $table->string('status');
            $table->string('type');
            $table->string('link');
            $table->string('title');
            $table->string('summary');
            $table->string('thumbnail_url');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('posts');
    }
};
