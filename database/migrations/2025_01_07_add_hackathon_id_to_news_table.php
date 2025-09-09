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
        Schema::table('news', function (Blueprint $table) {
            // Add hackathon_id column after author_id
            $table->after('author_id', function ($table) {
                $table->char('hackathon_id', 26)->nullable();
                $table->foreign('hackathon_id')->references('id')->on('hackathons')->onDelete('cascade');
                $table->index('hackathon_id');
            });
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('news', function (Blueprint $table) {
            $table->dropForeign(['hackathon_id']);
            $table->dropColumn('hackathon_id');
        });
    }
};
