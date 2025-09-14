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
        Schema::table('speakers', function (Blueprint $table) {
            if (!Schema::hasColumn('speakers', 'organization_id')) {
                $table->foreignId('organization_id')->nullable()->after('phone');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('speakers', function (Blueprint $table) {
            // Check if foreign key constraint exists before dropping
            $foreignKeys = $this->getForeignKeys('speakers');
            
            if (in_array('speakers_organization_id_foreign', $foreignKeys)) {
                $table->dropForeign(['organization_id']);
            }
            
            // Drop column if it exists
            if (Schema::hasColumn('speakers', 'organization_id')) {
                $table->dropColumn('organization_id');
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
