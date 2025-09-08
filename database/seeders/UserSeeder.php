<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Create superuser
        $superuser = User::firstOrCreate(
            ['email' => 'superadmin@hackathon.com'],
            [
                'name' => 'Super Admin',
                'password' => bcrypt('password'),
                'email_verified_at' => now(),
            ]
        );
        if (!$superuser->hasRole('superuser')) {
            $superuser->assignRole('superuser');
        }

        // Create system admin
        $systemAdmin = User::firstOrCreate(
            ['email' => 'system@hackathon.com'],
            [
                'name' => 'System Admin',
                'password' => bcrypt('password'),
                'email_verified_at' => now(),
            ]
        );
        if (!$systemAdmin->hasRole('system_admin')) {
            $systemAdmin->assignRole('system_admin');
        }

        // Create hackathon admin
        $hackathonAdmin = User::firstOrCreate(
            ['email' => 'admin@hackathon.com'],
            [
                'name' => 'Hackathon Admin',
                'password' => bcrypt('password'),
                'email_verified_at' => now(),
            ]
        );
        if (!$hackathonAdmin->hasRole('hackathon_admin')) {
            $hackathonAdmin->assignRole('hackathon_admin');
        }

        // Create track supervisors
        $trackSupervisor1 = User::firstOrCreate(
            ['email' => 'sarah.johnson@hackathon.com'],
            [
                'name' => 'Dr. Sarah Johnson',
                'password' => bcrypt('password'),
                'email_verified_at' => now(),
                'phone' => '+1234567890',
                'university' => 'MIT',
                'major' => 'Computer Science',
            ]
        );
        if (!$trackSupervisor1->hasRole('track_supervisor')) {
            $trackSupervisor1->assignRole('track_supervisor');
        }

        $trackSupervisor2 = User::firstOrCreate(
            ['email' => 'michael.chen@hackathon.com'],
            [
                'name' => 'Prof. Michael Chen',
                'password' => bcrypt('password'),
                'email_verified_at' => now(),
                'phone' => '+1234567891',
                'university' => 'Stanford University',
                'major' => 'Artificial Intelligence',
            ]
        );
        if (!$trackSupervisor2->hasRole('track_supervisor')) {
            $trackSupervisor2->assignRole('track_supervisor');
        }

        // Create team leaders
        $teamLeader1 = User::firstOrCreate(
            ['email' => 'alice@team.com'],
            [
                'name' => 'Alice Williams',
                'password' => bcrypt('password'),
                'email_verified_at' => now(),
                'phone' => '+1234567892',
                'university' => 'UC Berkeley',
                'major' => 'Software Engineering',
                'graduation_year' => 2025,
            ]
        );
        if (!$teamLeader1->hasRole('team_leader')) {
            $teamLeader1->assignRole('team_leader');
        }

        $teamLeader2 = User::firstOrCreate(
            ['email' => 'bob@team.com'],
            [
                'name' => 'Bob Martinez',
                'password' => bcrypt('password'),
                'email_verified_at' => now(),
                'phone' => '+1234567893',
                'university' => 'Carnegie Mellon',
                'major' => 'Data Science',
                'graduation_year' => 2024,
            ]
        );
        if (!$teamLeader2->hasRole('team_leader')) {
            $teamLeader2->assignRole('team_leader');
        }

        // Create team members
        for ($i = 1; $i <= 10; $i++) {
            $member = User::firstOrCreate(
                ['email' => "member$i@team.com"],
                [
                    'name' => "Team Member $i",
                    'password' => bcrypt('password'),
                    'email_verified_at' => now(),
                    'phone' => '+123456789' . str_pad($i, 1, '0', STR_PAD_LEFT),
                    'university' => ['Harvard', 'Yale', 'Princeton', 'Columbia', 'Brown'][rand(0, 4)],
                    'major' => ['Computer Science', 'Engineering', 'Mathematics', 'Physics', 'Data Science'][rand(0, 4)],
                    'graduation_year' => rand(2024, 2027),
                ]
            );
            if (!$member->hasRole('team_member')) {
                $member->assignRole('team_member');
            }
        }

        // Create regular users
        for ($i = 1; $i <= 5; $i++) {
            $user = User::firstOrCreate(
                ['email' => "user$i@example.com"],
                [
                    'name' => "User $i",
                    'password' => bcrypt('password'),
                    'email_verified_at' => now(),
                ]
            );
            if (!$user->hasRole('user')) {
                $user->assignRole('user');
            }
        }
    }
}
