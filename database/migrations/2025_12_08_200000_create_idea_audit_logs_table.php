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
        Schema::create('idea_audit_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('idea_id');
            $table->char('user_id', 26)->nullable();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
            $table->string('action'); // created, updated, submitted, status_changed, etc.
            $table->string('field_name')->nullable(); // which field was changed
            $table->text('old_value')->nullable(); // previous value
            $table->text('new_value')->nullable(); // new value
            $table->text('notes')->nullable(); // additional notes/comments
            $table->json('metadata')->nullable(); // extra data like scores
            $table->ipAddress('ip_address')->nullable();
            $table->string('user_agent', 1023)->nullable();
            $table->timestamps();

            $table->index(['idea_id', 'action']);
            $table->index(['idea_id', 'created_at']);
            $table->index('action');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('idea_audit_logs');
    }
};
