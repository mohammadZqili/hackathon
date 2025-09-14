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
        Schema::table('tracks', function (Blueprint $table) {
            // Add edition_id column only if it doesn't exist
            if (!Schema::hasColumn('tracks', 'edition_id')) {
                $table->foreignId('edition_id')->nullable()->after('hackathon_id');
                
                // Add index for edition_id
                $table->index(['edition_id', 'is_active']);
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tracks', function (Blueprint $table) {
            // Check if foreign key constraint exists before dropping
            $foreignKeys = $this->getForeignKeys('tracks');
            
            if (in_array('tracks_edition_id_foreign', $foreignKeys)) {
                $table->dropForeign(['edition_id']);
            }
            
            // Check if index exists before dropping
            $indexes = $this->getIndexes('tracks');
            $indexName = 'tracks_edition_id_is_active_index';
            
            if (in_array($indexName, $indexes)) {
                $table->dropIndex(['edition_id', 'is_active']);
            }
            
            // Drop column if it exists
            if (Schema::hasColumn('tracks', 'edition_id')) {
                $table->dropColumn('edition_id');
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
