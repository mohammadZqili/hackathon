<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Add missing fields only if they don't exist
            if (!Schema::hasColumn('users', 'team_id')) {
                $table->foreignId('team_id')->nullable()->after('user_type')
                    ->constrained('teams')->nullOnDelete();
            }
            
            if (!Schema::hasColumn('users', 'edition_id')) {
                $table->foreignId('edition_id')->nullable()->after('team_id')
                    ->constrained('editions')->nullOnDelete();
            }
            
            if (!Schema::hasColumn('users', 'hackathon_edition_id')) {
                $table->foreignId('hackathon_edition_id')->nullable()->after('edition_id')
                    ->constrained('editions')->nullOnDelete();
            }
            
            // Add workshop_supervisor to user_type enum if needed
            \DB::statement("ALTER TABLE users MODIFY COLUMN user_type ENUM(
                'system_admin', 
                'hackathon_admin', 
                'track_supervisor',
                'workshop_supervisor',
                'team_leader', 
                'team_member', 
                'visitor'
            ) DEFAULT 'visitor'");
        });
        
        // Create supervisor relations tables if they don't exist
        if (!Schema::hasTable('track_supervisors')) {
            Schema::create('track_supervisors', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->cascadeOnDelete();
                $table->foreignId('track_id')->constrained()->cascadeOnDelete();
                $table->timestamps();
                
                $table->unique(['user_id', 'track_id']);
            });
        }
        
        if (!Schema::hasTable('workshop_supervisors')) {
            Schema::create('workshop_supervisors', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->cascadeOnDelete();
                $table->foreignId('workshop_id')->constrained()->cascadeOnDelete();
                $table->timestamps();
                
                $table->unique(['user_id', 'workshop_id']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('workshop_supervisors');
        Schema::dropIfExists('track_supervisors');
        
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['team_id']);
            $table->dropForeign(['edition_id']);
            $table->dropForeign(['hackathon_edition_id']);
            
            $table->dropColumn(['team_id', 'edition_id', 'hackathon_edition_id']);
        });
    }
};
