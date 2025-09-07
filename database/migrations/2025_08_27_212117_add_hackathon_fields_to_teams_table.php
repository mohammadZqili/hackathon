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
        Schema::table('teams', function (Blueprint $table) {
            // Add hackathon-specific fields after existing columns
            $table->string('slug')->nullable()->after('name');
            $table->text('description')->nullable()->after('slug');
            $table->foreignId('hackathon_id')->nullable()->constrained()->onDelete('cascade')->after('description');
            $table->char('leader_id', 26)->nullable()->after('hackathon_id');
            $table->foreign('leader_id')->references('id')->on('users')->onDelete('set null');
            $table->foreignId('track_id')->nullable()->constrained()->after('leader_id');
            $table->string('invite_code', 8)->unique()->nullable()->after('track_id');
            $table->integer('max_members')->default(5)->after('invite_code');
            $table->enum('status', ['draft', 'active', 'submitted', 'accepted', 'rejected', 'disqualified'])->default('draft')->after('max_members');
            $table->timestamp('submitted_at')->nullable()->after('status');

            // Add indexes for performance
            $table->index(['hackathon_id', 'status']);
            $table->index(['track_id', 'status']);
            $table->index('leader_id');
            $table->index('status');
            $table->index('invite_code');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('teams', function (Blueprint $table) {
            // Drop indexes first
//            $table->dropIndex(['hackathon_id', 'status']);
//            $table->dropIndex(['track_id', 'status']);
//            $table->dropIndex(['leader_id']);
//            $table->dropIndex(['status']);
//            $table->dropIndex(['invite_code']);

            // Drop foreign key constraints
            $table->dropForeign(['hackathon_id']);
            $table->dropForeign(['leader_id']);
            $table->dropForeign(['track_id']);

            // Drop columns
            $table->dropColumn([
                'slug',
                'description',
                'hackathon_id',
                'leader_id',
                'track_id',
                'invite_code',
                'max_members',
                'status',
                'submitted_at'
            ]);
        });
    }
};
