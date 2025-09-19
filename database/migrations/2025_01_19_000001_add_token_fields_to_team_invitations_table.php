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
        Schema::table('team_invitations', function (Blueprint $table) {
            $table->string('token', 64)->unique()->after('email');
            $table->enum('status', ['pending', 'accepted', 'expired'])->default('pending')->after('role');
            $table->timestamp('expires_at')->nullable()->after('status');
            $table->timestamp('accepted_at')->nullable()->after('expires_at');

            // Add indexes for better performance
            $table->index('token');
            $table->index(['email', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('team_invitations', function (Blueprint $table) {
            $table->dropIndex(['email', 'status']);
            $table->dropIndex(['token']);

            $table->dropColumn(['token', 'status', 'expires_at', 'accepted_at']);
        });
    }
};