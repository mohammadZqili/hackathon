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
        Schema::create('team_members', function (Blueprint $table) {
            $table->id();
            $table->foreignId('team_id')->constrained()->onDelete('cascade');
            $table->char('user_id', 26);
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->enum('status', ['pending', 'accepted', 'rejected', 'removed'])->default('pending');
            $table->enum('role', ['leader', 'member'])->default('member');
            $table->timestamp('joined_at')->nullable();
            $table->timestamp('invited_at')->nullable();
            $table->char('invited_by', 26)->nullable();
            $table->foreign('invited_by')->references('id')->on('users')->onDelete('set null');
            $table->text('invitation_message')->nullable();
            $table->timestamps();
            
            $table->unique(['team_id', 'user_id']);
            $table->index(['team_id', 'status']);
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('team_members');
    }
};
