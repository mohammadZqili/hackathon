<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Edition;
use App\Models\Track;
use App\Models\Team;
use App\Models\Idea;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class TestUserSeeder extends Seeder
{
    public function run()
    {
        // Create roles if they don't exist
        $systemAdminRole = Role::firstOrCreate(['name' => 'system_admin', 'guard_name' => 'web']);
        $hackathonAdminRole = Role::firstOrCreate(['name' => 'hackathon_admin', 'guard_name' => 'web']);
        $trackSupervisorRole = Role::firstOrCreate(['name' => 'track_supervisor', 'guard_name' => 'web']);

        // 1. System Admin User
        $systemAdmin = User::firstOrCreate(
            ['email' => 'system@admin.com'],
            [
                'name' => 'System Admin',
                'password' => Hash::make('password123'),
                'user_type' => 'system_admin',
                'email_verified_at' => now(),
            ]
        );
        $systemAdmin->assignRole($systemAdminRole);

        // 2. Hackathon Admin 1
        $hackathonAdmin1 = User::firstOrCreate(
            ['email' => 'hackathon1@admin.com'],
            [
                'name' => 'Hackathon Admin 1',
                'password' => Hash::make('password123'),
                'user_type' => 'hackathon_admin',
                'email_verified_at' => now(),
            ]
        );
        $hackathonAdmin1->assignRole($hackathonAdminRole);

        // 3. Hackathon Admin 2
        $hackathonAdmin2 = User::firstOrCreate(
            ['email' => 'hackathon2@admin.com'],
            [
                'name' => 'Hackathon Admin 2',
                'password' => Hash::make('password123'),
                'user_type' => 'hackathon_admin',
                'email_verified_at' => now(),
            ]
        );
        $hackathonAdmin2->assignRole($hackathonAdminRole);

        // 4. Track Supervisor 1
        $trackSupervisor1 = User::firstOrCreate(
            ['email' => 'track1@supervisor.com'],
            [
                'name' => 'Track Supervisor 1',
                'password' => Hash::make('password123'),
                'user_type' => 'track_supervisor',
                'email_verified_at' => now(),
            ]
        );
        $trackSupervisor1->assignRole($trackSupervisorRole);

        // 5. Track Supervisor 2
        $trackSupervisor2 = User::firstOrCreate(
            ['email' => 'track2@supervisor.com'],
            [
                'name' => 'Track Supervisor 2',
                'password' => Hash::make('password123'),
                'user_type' => 'track_supervisor',
                'email_verified_at' => now(),
            ]
        );
        $trackSupervisor2->assignRole($trackSupervisorRole);

        // 6. Track Supervisor 3
        $trackSupervisor3 = User::firstOrCreate(
            ['email' => 'track3@supervisor.com'],
            [
                'name' => 'Track Supervisor 3',
                'password' => Hash::make('password123'),
                'user_type' => 'track_supervisor',
                'email_verified_at' => now(),
            ]
        );
        $trackSupervisor3->assignRole($trackSupervisorRole);

        // Create Editions
        $edition1 = Edition::create([
            'name' => 'Hackathon 2024 - Spring Edition',
            'year' => 2024,
            'registration_start_date' => '2024-01-01',
            'registration_end_date' => '2024-02-01',
            'hackathon_start_date' => '2024-03-01',
            'hackathon_end_date' => '2024-03-03',
            'admin_id' => $hackathonAdmin1->id,
            'description' => 'Spring edition of our annual hackathon',
            'location' => 'Riyadh, Saudi Arabia',
            'max_teams' => 50,
            'max_team_members' => 5,
            'is_active' => true,
        ]);

        $edition2 = Edition::create([
            'name' => 'Hackathon 2024 - Summer Edition',
            'year' => 2024,
            'registration_start_date' => '2024-06-01',
            'registration_end_date' => '2024-07-01',
            'hackathon_start_date' => '2024-08-01',
            'hackathon_end_date' => '2024-08-03',
            'admin_id' => $hackathonAdmin2->id,
            'description' => 'Summer edition of our annual hackathon',
            'location' => 'Jeddah, Saudi Arabia',
            'max_teams' => 40,
            'max_team_members' => 4,
            'is_active' => true,
        ]);

        // Create Tracks for Edition 1
        $track1 = Track::create([
            'name' => 'AI & Machine Learning',
            'description' => 'Track for AI and ML projects',
            'hackathon_id' => $edition1->id, // Adding hackathon_id
            'edition_id' => $edition1->id,
            'max_teams' => 20,
            'is_active' => true,
        ]);

        $track2 = Track::create([
            'name' => 'Web Development',
            'description' => 'Track for web-based projects',
            'hackathon_id' => $edition1->id, // Adding hackathon_id
            'edition_id' => $edition1->id,
            'max_teams' => 15,
            'is_active' => true,
        ]);

        $track3 = Track::create([
            'name' => 'Mobile Apps',
            'description' => 'Track for mobile applications',
            'hackathon_id' => $edition1->id, // Adding hackathon_id
            'edition_id' => $edition1->id,
            'max_teams' => 15,
            'is_active' => true,
        ]);

        // Create Tracks for Edition 2
        $track4 = Track::create([
            'name' => 'Blockchain',
            'description' => 'Track for blockchain projects',
            'hackathon_id' => $edition2->id, // Adding hackathon_id
            'edition_id' => $edition2->id,
            'max_teams' => 10,
            'is_active' => true,
        ]);

        $track5 = Track::create([
            'name' => 'IoT Solutions',
            'description' => 'Track for Internet of Things',
            'hackathon_id' => $edition2->id, // Adding hackathon_id
            'edition_id' => $edition2->id,
            'max_teams' => 15,
            'is_active' => true,
        ]);

        $track6 = Track::create([
            'name' => 'Cybersecurity',
            'description' => 'Track for security solutions',
            'hackathon_id' => $edition2->id, // Adding hackathon_id
            'edition_id' => $edition2->id,
            'max_teams' => 15,
            'is_active' => true,
        ]);

        // Assign Track Supervisors
        $trackSupervisor1->supervisedTracks()->attach($track1);
        $trackSupervisor2->supervisedTracks()->attach($track2);
        $trackSupervisor3->supervisedTracks()->attach($track4);

        // Create Teams for Edition 1
        $team1 = Team::create([
            'name' => 'AI Innovators',
            'description' => 'Working on AI solution',
            'edition_id' => $edition1->id,
            'track_id' => $track1->id,
            'leader_id' => $trackSupervisor1->id,
            'status' => 'active',
        ]);

        $team2 = Team::create([
            'name' => 'ML Masters',
            'description' => 'Machine learning project',
            'edition_id' => $edition1->id,
            'track_id' => $track1->id,
            'leader_id' => $trackSupervisor1->id,
            'status' => 'active',
        ]);

        $team3 = Team::create([
            'name' => 'Web Warriors',
            'description' => 'Web platform development',
            'edition_id' => $edition1->id,
            'track_id' => $track2->id,
            'leader_id' => $trackSupervisor2->id,
            'status' => 'active',
        ]);

        // Create Teams for Edition 2
        $team4 = Team::create([
            'name' => 'Crypto Coders',
            'description' => 'Blockchain solution',
            'edition_id' => $edition2->id,
            'track_id' => $track4->id,
            'leader_id' => $trackSupervisor3->id,
            'status' => 'active',
        ]);

        $team5 = Team::create([
            'name' => 'Chain Builders',
            'description' => 'DeFi platform',
            'edition_id' => $edition2->id,
            'track_id' => $track4->id,
            'leader_id' => $trackSupervisor3->id,
            'status' => 'active',
        ]);

        // Create Ideas
        Idea::create([
            'title' => 'AI-Powered Healthcare Assistant',
            'description' => 'Using AI to improve healthcare',
            'team_id' => $team1->id,
            'status' => 'submitted',
        ]);

        Idea::create([
            'title' => 'ML Stock Predictor',
            'description' => 'Machine learning for stock prediction',
            'team_id' => $team2->id,
            'status' => 'under_review',
        ]);

        Idea::create([
            'title' => 'E-Learning Platform',
            'description' => 'Innovative web-based learning',
            'team_id' => $team3->id,
            'status' => 'accepted',
        ]);

        Idea::create([
            'title' => 'Decentralized Identity System',
            'description' => 'Blockchain identity management',
            'team_id' => $team4->id,
            'status' => 'submitted',
        ]);

        Idea::create([
            'title' => 'Smart Contract Marketplace',
            'description' => 'DeFi marketplace platform',
            'team_id' => $team5->id,
            'status' => 'under_review',
        ]);

        $this->command->info('Test users created successfully!');
        $this->command->info('');
        $this->command->info('=== TEST USER CREDENTIALS ===');
        $this->command->info('Password for all users: password123');
        $this->command->info('');
        $this->command->info('1. System Admin: system@admin.com');
        $this->command->info('   - Full access to all editions, tracks, teams, and ideas');
        $this->command->info('');
        $this->command->info('2. Hackathon Admin 1: hackathon1@admin.com');
        $this->command->info('   - Access to Edition 1 (Spring) only');
        $this->command->info('   - Can manage tracks: AI & ML, Web Development, Mobile Apps');
        $this->command->info('');
        $this->command->info('3. Hackathon Admin 2: hackathon2@admin.com');
        $this->command->info('   - Access to Edition 2 (Summer) only');
        $this->command->info('   - Can manage tracks: Blockchain, IoT, Cybersecurity');
        $this->command->info('');
        $this->command->info('4. Track Supervisor 1: track1@supervisor.com');
        $this->command->info('   - Access to AI & Machine Learning track in Edition 1');
        $this->command->info('');
        $this->command->info('5. Track Supervisor 2: track2@supervisor.com');
        $this->command->info('   - Access to Web Development track in Edition 1');
        $this->command->info('');
        $this->command->info('6. Track Supervisor 3: track3@supervisor.com');
        $this->command->info('   - Access to Blockchain track in Edition 2');
    }
}