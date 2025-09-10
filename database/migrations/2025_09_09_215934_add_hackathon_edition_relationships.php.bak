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
        // Add hackathon_edition_id to workshops table
        Schema::table('workshops', function (Blueprint $table) {
            $table->foreignId('hackathon_edition_id')->nullable()->after('hackathon_id')
                ->constrained('hackathon_editions')->onDelete('cascade');
            $table->index('hackathon_edition_id');
        });

        // Add hackathon_edition_id to teams table
        Schema::table('teams', function (Blueprint $table) {
            $table->foreignId('hackathon_edition_id')->nullable()->after('hackathon_id')
                ->constrained('hackathon_editions')->onDelete('cascade');
            $table->index('hackathon_edition_id');
        });

        // Add hackathon_edition_id to tracks table
        Schema::table('tracks', function (Blueprint $table) {
            $table->foreignId('hackathon_edition_id')->nullable()->after('hackathon_id')
                ->constrained('hackathon_editions')->onDelete('cascade');
            $table->index('hackathon_edition_id');
        });

        // Add hackathon_edition_id to news table
        Schema::table('news', function (Blueprint $table) {
            $table->foreignId('hackathon_edition_id')->nullable()->after('hackathon_id')
                ->constrained('hackathon_editions')->onDelete('cascade');
            $table->index('hackathon_edition_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('workshops', function (Blueprint $table) {
            $table->dropForeign(['hackathon_edition_id']);
            $table->dropColumn('hackathon_edition_id');
        });

        Schema::table('teams', function (Blueprint $table) {
            $table->dropForeign(['hackathon_edition_id']);
            $table->dropColumn('hackathon_edition_id');
        });

        Schema::table('tracks', function (Blueprint $table) {
            $table->dropForeign(['hackathon_edition_id']);
            $table->dropColumn('hackathon_edition_id');
        });

        Schema::table('news', function (Blueprint $table) {
            $table->dropForeign(['hackathon_edition_id']);
            $table->dropColumn('hackathon_edition_id');
        });
    }
};