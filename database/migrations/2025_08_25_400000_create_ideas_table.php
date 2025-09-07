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
        Schema::create('ideas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('team_id')->constrained()->onDelete('cascade');
            $table->foreignId('track_id')->constrained();
            $table->string('title');
            $table->text('description');
            $table->text('problem_statement')->nullable();
            $table->text('solution_approach')->nullable();
            $table->text('expected_impact')->nullable();
            $table->json('technologies')->nullable();
            $table->enum('status', ['draft', 'submitted', 'under_review', 'needs_revision', 'accepted', 'rejected'])->default('draft');
            $table->decimal('score', 5, 2)->nullable();
            $table->text('feedback')->nullable();
            $table->char('reviewed_by', 26)->nullable();
            $table->foreign('reviewed_by')->references('id')->on('users')->onDelete('set null');
            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('reviewed_at')->nullable();
            $table->json('evaluation_scores')->nullable(); // Detailed scoring breakdown
            $table->timestamps();
            
            $table->index(['team_id', 'status']);
            $table->index(['track_id', 'status']);
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ideas');
    }
};
