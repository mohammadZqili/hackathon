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
        Schema::create('workshop_speakers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('workshop_id');
            $table->foreignId('speaker_id');
            $table->enum('role', ['main_speaker', 'co_speaker', 'moderator', 'panelist'])->default('main_speaker');
            $table->integer('order')->default(0); // Speaking order
            $table->timestamps();

            $table->unique(['workshop_id', 'speaker_id']);
            $table->index(['workshop_id', 'order']);
            $table->index('role');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('workshop_speakers');
    }
};
