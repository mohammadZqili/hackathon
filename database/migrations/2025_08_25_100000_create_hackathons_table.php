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
        Schema::create('hackathons', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('theme')->nullable();
            $table->integer('year');
            $table->date('registration_start_date');
            $table->date('registration_end_date');
            $table->date('idea_submission_start_date');
            $table->date('idea_submission_end_date');
            $table->date('event_start_date');
            $table->date('event_end_date');
            $table->string('location')->nullable();
            $table->boolean('is_active')->default(false);
            $table->boolean('is_current')->default(false);
            $table->json('settings')->nullable(); // Additional settings
            $table->char('created_by', 26);
            $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');
            $table->timestamps();
            
            $table->index('year');
            $table->index('is_active');
            $table->index('is_current');
            $table->index(['registration_start_date', 'registration_end_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hackathons');
    }
};
