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
        Schema::create('workshops', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hackathon_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('description');
            $table->enum('type', ['workshop', 'seminar', 'lecture', 'panel'])->default('workshop');
            $table->datetime('start_time');
            $table->datetime('end_time');
            $table->enum('format', ['online', 'offline', 'hybrid'])->default('offline');
            $table->string('location')->nullable(); // Physical location or online link
            $table->integer('max_attendees')->nullable();
            $table->integer('current_attendees')->default(0);
            $table->text('prerequisites')->nullable();
            $table->json('materials')->nullable(); // Links to materials, resources
            $table->string('thumbnail_path')->nullable();
            $table->boolean('is_active')->default(true);
            $table->boolean('requires_registration')->default(true);
            $table->datetime('registration_deadline')->nullable();
            $table->json('settings')->nullable(); // Additional settings
            $table->timestamps();
            
            $table->index(['hackathon_id', 'is_active']);
            $table->index(['start_time', 'end_time']);
            $table->index('type');
            $table->index('slug');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('workshops');
    }
};
