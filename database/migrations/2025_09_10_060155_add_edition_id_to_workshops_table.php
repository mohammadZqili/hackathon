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
        Schema::table('workshops', function (Blueprint $table) {
            // Check if column doesn't exist before adding
            if (!Schema::hasColumn('workshops', 'hackathon_edition_id')) {
                $table->unsignedBigInteger('hackathon_edition_id')->nullable()->after('id');
                $table->index('hackathon_edition_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('workshops', function (Blueprint $table) {
            if (Schema::hasColumn('workshops', 'hackathon_edition_id')) {
                $table->dropIndex(['hackathon_edition_id']);
                $table->dropColumn('hackathon_edition_id');
            }
        });
    }
};