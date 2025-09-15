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
        Schema::table('hackathon_editions', function (Blueprint $table) {
            $table->string('admin_id')->nullable()->after('created_by');
            $table->foreign('admin_id')->references('id')->on('users')->onDelete('set null');
            $table->index('admin_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('hackathon_editions', function (Blueprint $table) {
            $table->dropForeign(['admin_id']);
            $table->dropIndex(['admin_id']);
            $table->dropColumn('admin_id');
        });
    }
};
