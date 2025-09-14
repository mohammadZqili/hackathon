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
            if (!Schema::hasColumn('teams', 'slug')) {
                $table->string('slug')->nullable()->after('name');
            }
            if (!Schema::hasColumn('teams', 'description')) {
                $table->text('description')->nullable()->after('slug');
            }
            if (!Schema::hasColumn('teams', 'hackathon_id')) {
                $table->foreignId('hackathon_id')->nullable();
            }
            if (!Schema::hasColumn('teams', 'leader_id')) {
                $table->char('leader_id', 26)->nullable()->after('hackathon_id');
                $table->foreign('leader_id')->references('id')->on('users')->onDelete('set null');
            }
            if (!Schema::hasColumn('teams', 'track_id')) {
                $table->foreignId('track_id')->nullable();
            }
            if (!Schema::hasColumn('teams', 'invite_code')) {
                $table->string('invite_code', 8)->unique()->nullable()->after('track_id');
            }
            if (!Schema::hasColumn('teams', 'max_members')) {
                $table->integer('max_members')->default(5)->after('invite_code');
            }
            if (!Schema::hasColumn('teams', 'status')) {
                $table->enum('status', ['draft', 'active', 'submitted', 'accepted', 'rejected', 'disqualified'])->default('draft')->after('max_members');
            }
            if (!Schema::hasColumn('teams', 'submitted_at')) {
                $table->timestamp('submitted_at')->nullable()->after('status');
            }

            // Add indexes for performance (only if columns were added)
            if (Schema::hasColumn('teams', 'hackathon_id') && Schema::hasColumn('teams', 'status')) {
                $table->index(['hackathon_id', 'status']);
            }
            if (Schema::hasColumn('teams', 'track_id') && Schema::hasColumn('teams', 'status')) {
                $table->index(['track_id', 'status']);
            }
            if (Schema::hasColumn('teams', 'leader_id')) {
                $table->index('leader_id');
            }
            if (Schema::hasColumn('teams', 'status')) {
                $table->index('status');
            }
            if (Schema::hasColumn('teams', 'invite_code')) {
                $table->index('invite_code');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('teams', function (Blueprint $table) {
            // Check if foreign key constraints exist before dropping them
            $foreignKeys = $this->getForeignKeys('teams');
            
            if (in_array('teams_hackathon_id_foreign', $foreignKeys)) {
                $table->dropForeign(['hackathon_id']);
            }
            if (in_array('teams_leader_id_foreign', $foreignKeys)) {
                $table->dropForeign(['leader_id']);
            }
            if (in_array('teams_track_id_foreign', $foreignKeys)) {
                $table->dropForeign(['track_id']);
            }

            // Drop indexes if they exist
            $indexes = $this->getIndexes('teams');
            
            if (in_array('teams_hackathon_id_status_index', $indexes)) {
                $table->dropIndex(['hackathon_id', 'status']);
            }
            if (in_array('teams_track_id_status_index', $indexes)) {
                $table->dropIndex(['track_id', 'status']);
            }
            if (in_array('teams_leader_id_index', $indexes)) {
                $table->dropIndex(['leader_id']);
            }
            if (in_array('teams_status_index', $indexes)) {
                $table->dropIndex(['status']);
            }
            if (in_array('teams_invite_code_index', $indexes)) {
                $table->dropIndex(['invite_code']);
            }

            // Drop columns if they exist
            $columnsToCheck = [
                'slug', 'description', 'hackathon_id', 'leader_id', 
                'track_id', 'invite_code', 'max_members', 'status', 'submitted_at'
            ];
            
            foreach ($columnsToCheck as $column) {
                if (Schema::hasColumn('teams', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }

    /**
     * Get foreign key constraint names for a table
     */
    private function getForeignKeys(string $table): array
    {
        $databaseName = config('database.connections.' . config('database.default') . '.database');
        
        $foreignKeys = \DB::select("
            SELECT CONSTRAINT_NAME 
            FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE 
            WHERE TABLE_NAME = ? 
            AND TABLE_SCHEMA = ? 
            AND CONSTRAINT_NAME LIKE '%_foreign'
        ", [$table, $databaseName]);
        
        return array_column($foreignKeys, 'CONSTRAINT_NAME');
    }

    /**
     * Get index names for a table
     */
    private function getIndexes(string $table): array
    {
        $databaseName = config('database.connections.' . config('database.default') . '.database');
        
        $indexes = \DB::select("
            SELECT INDEX_NAME 
            FROM INFORMATION_SCHEMA.STATISTICS 
            WHERE TABLE_NAME = ? 
            AND TABLE_SCHEMA = ? 
            AND INDEX_NAME != 'PRIMARY'
        ", [$table, $databaseName]);
        
        return array_column($indexes, 'INDEX_NAME');
    }
};
