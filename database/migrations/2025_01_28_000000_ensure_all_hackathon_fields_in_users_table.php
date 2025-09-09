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
        Schema::table('users', function (Blueprint $table) {
            // Check and add missing columns
            if (!Schema::hasColumn('users', 'date_of_birth')) {
                $table->date('date_of_birth')->nullable()->after('email');
            }
            
            if (!Schema::hasColumn('users', 'phone')) {
                $table->string('phone', 20)->nullable()->after('date_of_birth');
            }
            
            if (!Schema::hasColumn('users', 'national_id')) {
                $table->string('national_id', 20)->nullable()->unique()->after('phone');
            }
            
            if (!Schema::hasColumn('users', 'user_type')) {
                $table->enum('user_type', ['team_leader', 'team_member', 'visitor', 'admin'])->default('team_member')->after('national_id');
            }
            
            if (!Schema::hasColumn('users', 'occupation')) {
                $table->enum('occupation', ['student', 'employee'])->nullable()->after('user_type');
            }
            
            if (!Schema::hasColumn('users', 'job_title')) {
                $table->string('job_title')->nullable()->after('occupation');
            }
            
            if (!Schema::hasColumn('users', 'is_active')) {
                $table->boolean('is_active')->default(true)->after('job_title');
            }
            
            if (!Schema::hasColumn('users', 'last_login_at')) {
                $table->timestamp('last_login_at')->nullable()->after('is_active');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Drop columns if they exist
            $columnsToCheck = [
                'date_of_birth',
                'phone',
                'national_id',
                'user_type',
                'occupation',
                'job_title',
                'is_active',
                'last_login_at'
            ];
            
            foreach ($columnsToCheck as $column) {
                if (Schema::hasColumn('users', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
