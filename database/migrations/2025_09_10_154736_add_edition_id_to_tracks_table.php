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
            // Add edition_id column
            $table->foreignId('edition_id')->nullable()->after('hackathon_id')->constrained('hackathon_editions')->onDelete('cascade');
            
            // Add index for edition_id
            $table->index(['edition_id', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tracks', function (Blueprint $table) {
            $table->dropForeign(['edition_id']);
            $table->dropIndex(['edition_id', 'is_active']);
            $table->dropColumn('edition_id');
        });
    }
};
