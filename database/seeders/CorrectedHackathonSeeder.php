<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Edition;
use App\Models\Team;
use App\Models\Track;
use App\Models\Workshop;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class CorrectedHackathonSeeder extends Seeder
{
    public function run()
    {
        echo "========================================\n";
        echo "Creating test data for all 7 roles...\n";
        echo "========================================\n\n";
        
        // Create or get test edition
        $edition = Edition::firstOrCreate(
            ['name' => '2025 Hackathon'],
            [
                'year' => 2025,
                'is_current' => true,
                'registration_start_date' => now(),
                'registration_end_date' => now()->addDays(20),
                'hackathon_start_date' => now()->addDays(30),
                'hackathon_end_date' => now()->addDays(33),
                'description' => 'Test hackathon edition for development',
                'location' => 'Test Location',
                'is_active' => true
            ]
        );
        echo "✓ Edition created: {$edition->name}\n";
        
        // Create test tracks
        $track1 = Track::firstOrCreate(
            ['name' => 'Environment Track', 'edition_id' => $edition->id],
            [
                'description' => 'Environmental solutions and sustainability'
            ]
        );
        
        $track2 = Track::firstOrCreate(
            ['name' => 'Technology Track', 'edition_id' => $edition->id],
            [
                'description' => 'Tech innovations and digital solutions'
            ]
        );
        echo "✓ Tracks created\n";
        
        // Create test workshops
        $workshop1 = Workshop::firstOrCreate(
            ['title' => 'Introduction to AI', 'edition_id' => $edition->id],
            [
                'description' => 'Learn AI basics and machine learning fundamentals',
                'date' => now()->addDays(5),
                'start_time' => '10:00:00',
                'end_time' => '12:00:00',
                'location' => 'Room A101',
                'max_capacity' => 50,
                'is_public' => true
            ]
        );
        
        $workshop2 = Workshop::firstOrCreate(
            ['title' => 'Web Development Workshop', 'edition_id' => $edition->id],
            [
                'description' => 'Modern web development with latest technologies',
                'date' => now()->addDays(7),
                'start_time' => '14:00:00',
                'end_time' => '16:00:00',
                'location' => 'Room B202',
                'max_capacity' => 30,
                'is_public' => true
            ]
        );
        echo "✓ Workshops created\n\n";
        
        // Create users for each role
        echo "Creating test users:\n";
        echo "-------------------\n";
        
        // System Admin
        $systemAdmin = User::updateOrCreate(
            ['email' => 'system@test.com'],
            [
                'name' => 'System Admin',
                'password' => Hash::make('password'),
                'user_type' => 'system_admin',
                'email_verified_at' => now()
            ]
        );
        echo "✓ System Admin: system@test.com\n";
        
        // Hackathon Admin
        $hackathonAdmin = User::updateOrCreate(
            ['email' => 'hackathon@test.com'],
            [
                'name' => 'Hackathon Admin',
                'password' => Hash::make('password'),
                'user_type' => 'hackathon_admin',
                'edition_id' => $edition->id,
                'hackathon_edition_id' => $edition->id,
                'email_verified_at' => now()
            ]
        );
        echo "✓ Hackathon Admin: hackathon@test.com\n";
        
        // Track Supervisor
        $trackSupervisor = User::updateOrCreate(
            ['email' => 'track@test.com'],
            [
                'name' => 'Track Supervisor',
                'password' => Hash::make('password'),
                'user_type' => 'track_supervisor',
                'email_verified_at' => now()
            ]
        );
        
        // Assign tracks to supervisor
        if (Schema::hasTable('track_supervisors')) {
            DB::table('track_supervisors')->updateOrInsert(
                ['user_id' => $trackSupervisor->id, 'track_id' => $track1->id],
                ['created_at' => now(), 'updated_at' => now()]
            );
            DB::table('track_supervisors')->updateOrInsert(
                ['user_id' => $trackSupervisor->id, 'track_id' => $track2->id],
                ['created_at' => now(), 'updated_at' => now()]
            );
        }
        echo "✓ Track Supervisor: track@test.com (supervises 2 tracks)\n";
        
        // Workshop Supervisor
        $workshopSupervisor = User::updateOrCreate(
            ['email' => 'workshop@test.com'],
            [
                'name' => 'Workshop Supervisor',
                'password' => Hash::make('password'),
                'user_type' => 'workshop_supervisor',
                'email_verified_at' => now()
            ]
        );
        
        // Assign workshops to supervisor
        if (Schema::hasTable('workshop_supervisors')) {
            DB::table('workshop_supervisors')->updateOrInsert(
                ['user_id' => $workshopSupervisor->id, 'workshop_id' => $workshop1->id],
                ['created_at' => now(), 'updated_at' => now()]
            );
            DB::table('workshop_supervisors')->updateOrInsert(
                ['user_id' => $workshopSupervisor->id, 'workshop_id' => $workshop2->id],
                ['created_at' => now(), 'updated_at' => now()]
            );
        }
        echo "✓ Workshop Supervisor: workshop@test.com (supervises 2 workshops)\n";
        
        // Team Leader
        $teamLeader = User::updateOrCreate(
            ['email' => 'leader@test.com'],
            [
                'name' => 'Team Leader',
                'password' => Hash::make('password'),
                'user_type' => 'team_leader',
                'email_verified_at' => now()
            ]
        );
        echo "✓ Team Leader: leader@test.com\n";
        
        // Team Member
        $teamMember = User::updateOrCreate(
            ['email' => 'member@test.com'],
            [
                'name' => 'Team Member',
                'password' => Hash::make('password'),
                'user_type' => 'team_member',
                'email_verified_at' => now()
            ]
        );
        echo "✓ Team Member: member@test.com\n";
        
        // Visitor
        $visitor = User::updateOrCreate(
            ['email' => 'visitor@test.com'],
            [
                'name' => 'Visitor',
                'password' => Hash::make('password'),
                'user_type' => 'visitor',
                'email_verified_at' => now()
            ]
        );
        echo "✓ Visitor: visitor@test.com\n\n";
        
        // Create a test team
        $team = Team::firstOrCreate(
            ['name' => 'Test Team Alpha'],
            [
                'edition_id' => $edition->id,
                'track_id' => $track1->id,
                'leader_id' => $teamLeader->id,
                'description' => 'Test team for demo purposes',
                'max_members' => 5,
                'status' => 'active'
            ]
        );
        
        // Update team_id for users
        $teamLeader->update(['team_id' => $team->id]);
        $teamMember->update(['team_id' => $team->id]);
        
        echo "✓ Team created: {$team->name}\n";
        echo "  - Leader: {$teamLeader->name}\n";
        echo "  - Member: {$teamMember->name}\n";
        
        echo "\n========================================\n";
        echo "✅ ALL TEST DATA CREATED SUCCESSFULLY!\n";
        echo "========================================\n\n";
        echo "Test Credentials (all passwords: 'password'):\n";
        echo "---------------------------------------------\n";
        echo "System Admin:        system@test.com\n";
        echo "Hackathon Admin:     hackathon@test.com\n";
        echo "Track Supervisor:    track@test.com\n";
        echo "Workshop Supervisor: workshop@test.com\n";
        echo "Team Leader:         leader@test.com\n";
        echo "Team Member:         member@test.com\n";
        echo "Visitor:             visitor@test.com\n";
        echo "========================================\n";
    }
}