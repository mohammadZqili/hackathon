<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // First, check if we need to do this migration
        // Get the current column type
        $columnType = DB::select("SELECT DATA_TYPE, CHARACTER_MAXIMUM_LENGTH 
                                  FROM INFORMATION_SCHEMA.COLUMNS 
                                  WHERE TABLE_SCHEMA = DATABASE() 
                                  AND TABLE_NAME = 'teams' 
                                  AND COLUMN_NAME = 'user_id'")[0] ?? null;
        
        if ($columnType && $columnType->DATA_TYPE === 'char' && $columnType->CHARACTER_MAXIMUM_LENGTH == 26) {
            // Column is already the correct type, skip migration
            return;
        }
        
        Schema::table('teams', function (Blueprint $table) {
            // First, check if any foreign key exists and drop it
            $foreignKeys = DB::select("SELECT CONSTRAINT_NAME 
                                      FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE 
                                      WHERE TABLE_SCHEMA = DATABASE() 
                                      AND TABLE_NAME = 'teams' 
                                      AND COLUMN_NAME = 'user_id' 
                                      AND REFERENCED_TABLE_NAME IS NOT NULL");
            
            foreach ($foreignKeys as $key) {
                $table->dropForeign($key->CONSTRAINT_NAME);
            }
            
            // Check if index exists and drop it
            $indexes = DB::select("SELECT INDEX_NAME 
                                  FROM INFORMATION_SCHEMA.STATISTICS 
                                  WHERE TABLE_SCHEMA = DATABASE() 
                                  AND TABLE_NAME = 'teams' 
                                  AND COLUMN_NAME = 'user_id'
                                  AND INDEX_NAME != 'PRIMARY'");
            
            foreach ($indexes as $index) {
                $table->dropIndex($index->INDEX_NAME);
            }
        });
        
        // Now modify the column
        Schema::table('teams', function (Blueprint $table) {
            // Change the column type to char(26) for ULIDs
            $table->char('user_id', 26)->change();
        });
        
        // Add back the foreign key and index
        Schema::table('teams', function (Blueprint $table) {
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->index('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('teams', function (Blueprint $table) {
            // Get foreign keys
            $foreignKeys = DB::select("SELECT CONSTRAINT_NAME 
                                      FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE 
                                      WHERE TABLE_SCHEMA = DATABASE() 
                                      AND TABLE_NAME = 'teams' 
                                      AND COLUMN_NAME = 'user_id' 
                                      AND REFERENCED_TABLE_NAME IS NOT NULL");
            
            foreach ($foreignKeys as $key) {
                $table->dropForeign($key->CONSTRAINT_NAME);
            }
            
            // Get indexes
            $indexes = DB::select("SELECT INDEX_NAME 
                                  FROM INFORMATION_SCHEMA.STATISTICS 
                                  WHERE TABLE_SCHEMA = DATABASE() 
                                  AND TABLE_NAME = 'teams' 
                                  AND COLUMN_NAME = 'user_id'
                                  AND INDEX_NAME != 'PRIMARY'");
            
            foreach ($indexes as $index) {
                $table->dropIndex($index->INDEX_NAME);
            }
            
            // Change back to bigint unsigned
            $table->unsignedBigInteger('user_id')->change();
            
            // Re-add the index (no foreign key since it won't work with mixed types)
            $table->index('user_id');
        });
    }
};
