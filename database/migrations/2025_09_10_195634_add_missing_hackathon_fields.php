<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Add missing fields only if they don't exist
            if (!Schema::hasColumn('users', 'team_id')) {
                $table->foreignId('team_id')->nullable()->after('user_type');
            }

            if (!Schema::hasColumn('users', 'edition_id')) {
                $table->foreignId('edition_id')->nullable()->after('team_id');
            }

            if (!Schema::hasColumn('users', 'hackathon_edition_id')) {
                $table->foreignId('hackathon_edition_id')->nullable()->after('edition_id');
            }
        });

        // Create supervisor relations tables if they don't exist
        if (!Schema::hasTable('track_supervisors')) {
            Schema::create('track_supervisors', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id');
                $table->foreignId('track_id');
                $table->timestamps();

                $table->unique(['user_id', 'track_id']);
            });
        }

        if (!Schema::hasTable('workshop_supervisors')) {
            Schema::create('workshop_supervisors', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id');
                $table->foreignId('workshop_id');
                $table->timestamps();

                $table->unique(['user_id', 'workshop_id']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('workshop_supervisors');
        Schema::dropIfExists('track_supervisors');

        Schema::table('users', function (Blueprint $table) {
            // Check if foreign key constraints exist before dropping them
            $foreignKeys = $this->getForeignKeys('users');
            
            if (in_array('users_team_id_foreign', $foreignKeys)) {
                $table->dropForeign(['team_id']);
            }
            if (in_array('users_edition_id_foreign', $foreignKeys)) {
                $table->dropForeign(['edition_id']);
            }
            if (in_array('users_hackathon_edition_id_foreign', $foreignKeys)) {
                $table->dropForeign(['hackathon_edition_id']);
            }

            // Drop columns if they exist
            if (Schema::hasColumn('users', 'hackathon_edition_id')) {
                $table->dropColumn('hackathon_edition_id');
            }
            if (Schema::hasColumn('users', 'edition_id')) {
                $table->dropColumn('edition_id');
            }
            if (Schema::hasColumn('users', 'team_id')) {
                $table->dropColumn('team_id');
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
};
