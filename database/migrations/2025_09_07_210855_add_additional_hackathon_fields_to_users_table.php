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
            // Educational information
            $table->string('university')->nullable()->after('job_title');
            $table->string('major')->nullable()->after('university');
            $table->integer('graduation_year')->nullable()->after('major');
            
            // Profile information
            $table->string('github_username')->nullable()->after('graduation_year');
            $table->string('linkedin_url')->nullable()->after('github_username');
            $table->text('bio')->nullable()->after('linkedin_url');
            $table->json('skills')->nullable()->after('bio');
            $table->json('interests')->nullable()->after('skills');
            
            // Event-specific information
            $table->string('dietary_restrictions')->nullable()->after('interests');
            $table->string('tshirt_size', 10)->nullable()->after('dietary_restrictions');
            $table->string('emergency_contact_name')->nullable()->after('tshirt_size');
            $table->string('emergency_contact_phone')->nullable()->after('emergency_contact_name');
            
            // Participation history
            $table->boolean('participated_before')->default(false)->after('emergency_contact_phone');
            $table->integer('previous_hackathons')->default(0)->after('participated_before');
            
            // Communication
            $table->string('discord_username')->nullable()->after('previous_hackathons');
            
            // Indexes for better performance
            $table->index('university');
            $table->index('major');
            $table->index('graduation_year');
            $table->index('github_username');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['university']);
            $table->dropIndex(['major']);
            $table->dropIndex(['graduation_year']);
            $table->dropIndex(['github_username']);
            
            $table->dropColumn([
                'university',
                'major',
                'graduation_year',
                'github_username',
                'linkedin_url',
                'bio',
                'skills',
                'interests',
                'dietary_restrictions',
                'tshirt_size',
                'emergency_contact_name',
                'emergency_contact_phone',
                'participated_before',
                'previous_hackathons',
                'discord_username'
            ]);
        });
    }
};