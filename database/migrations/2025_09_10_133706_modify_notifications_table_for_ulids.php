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
        Schema::table('notifications', function (Blueprint $table) {
            // Drop the existing morphs columns
            $table->dropMorphs('notifiable');
        });
        
        Schema::table('notifications', function (Blueprint $table) {
            // Recreate with string type for ULID support
            $table->string('notifiable_type')->after('type');
            $table->string('notifiable_id')->after('notifiable_type');
            $table->index(['notifiable_type', 'notifiable_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('notifications', function (Blueprint $table) {
            $table->dropColumn(['notifiable_type', 'notifiable_id']);
        });
        
        Schema::table('notifications', function (Blueprint $table) {
            $table->morphs('notifiable');
        });
    }
};