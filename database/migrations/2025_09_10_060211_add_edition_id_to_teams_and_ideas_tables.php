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
        // Add edition_id to teams table
        Schema::table('teams', function (Blueprint $table) {
            if (!Schema::hasColumn('teams', 'edition_id')) {
                $table->unsignedBigInteger('edition_id')->nullable()->after('id');
                $table->index('edition_id');
            }
        });

        // Add edition_id to ideas table
        Schema::table('ideas', function (Blueprint $table) {
            if (!Schema::hasColumn('ideas', 'edition_id')) {
                $table->unsignedBigInteger('edition_id')->nullable()->after('id');
                $table->index('edition_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('teams', function (Blueprint $table) {
            if (Schema::hasColumn('teams', 'edition_id')) {
                $table->dropIndex(['edition_id']);
                $table->dropColumn('edition_id');
            }
        });

        Schema::table('ideas', function (Blueprint $table) {
            if (Schema::hasColumn('ideas', 'edition_id')) {
                $table->dropIndex(['edition_id']);
                $table->dropColumn('edition_id');
            }
        });
    }
};
