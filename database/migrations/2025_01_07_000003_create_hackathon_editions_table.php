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
        Schema::create('hackathon_editions', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->integer('year');
            $table->text('description')->nullable();
            $table->string('theme')->nullable();
            $table->date('registration_start_date');
            $table->date('registration_end_date');
            $table->date('idea_submission_start_date');
            $table->date('idea_submission_end_date');
            $table->date('event_start_date');
            $table->date('event_end_date');
            $table->string('location')->nullable();
            $table->enum('status', ['draft', 'active', 'completed', 'archived'])->default('draft');
            $table->boolean('is_current')->default(false);
            $table->json('settings')->nullable(); // prizes, rules, etc
            $table->json('statistics')->nullable(); // cached stats
            $table->char('created_by', 26);
            $table->timestamps();
            
            $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');
            $table->index(['year', 'status']);
            $table->index('is_current');
            $table->index('slug');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hackathon_editions');
    }
};