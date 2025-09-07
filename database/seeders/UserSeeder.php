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
        $superuser = User::create([
            'name' => 'Super Admin',
            'email' => 'superadmin@hackathon.com',
            'password' => bcrypt('password'),
            'email_verified_at' => now(),
        ]);
        $superuser->assignRole('superuser');

        // Create system admin
        $systemAdmin = User::create([
            'name' => 'System Admin',
            'email' => 'system@hackathon.com',
            'password' => bcrypt('password'),
            'email_verified_at' => now(),
        ]);
        $systemAdmin->assignRole('system_admin');

        // Create hackathon admin
        $hackathonAdmin = User::create([
            'name' => 'Hackathon Admin',
            'email' => 'admin@hackathon.com',
            'password' => bcrypt('password'),
            'email_verified_at' => now(),
        ]);
        $hackathonAdmin->assignRole('hackathon_admin');

        // Create track supervisors
        $trackSupervisor1 = User::create([
            'name' => 'Dr. Sarah Johnson',
            'email' => 'sarah.johnson@hackathon.com',
            'password' => bcrypt('password'),
            'email_verified_at' => now(),
            'phone' => '+1234567890',
            'university' => 'MIT',
            'major' => 'Computer Science',
        ]);
        $trackSupervisor1->assignRole('track_supervisor');

        $trackSupervisor2 = User::create([
            'name' => 'Prof. Michael Chen',
            'email' => 'michael.chen@hackathon.com',
            'password' => bcrypt('password'),
            'email_verified_at' => now(),
            'phone' => '+1234567891',
            'university' => 'Stanford University',
            'major' => 'Artificial Intelligence',
        ]);
        $trackSupervisor2->assignRole('track_supervisor');

        // Create team leaders
        $teamLeader1 = User::create([
            'name' => 'Alice Williams',
            'email' => 'alice@team.com',
            'password' => bcrypt('password'),
            'email_verified_at' => now(),
            'phone' => '+1234567892',
            'university' => 'UC Berkeley',
            'major' => 'Software Engineering',
            'graduation_year' => 2025,
        ]);
        $teamLeader1->assignRole('team_leader');

        $teamLeader2 = User::create([
            'name' => 'Bob Martinez',
            'email' => 'bob@team.com',
            'password' => bcrypt('password'),
            'email_verified_at' => now(),
            'phone' => '+1234567893',
            'university' => 'Carnegie Mellon',
            'major' => 'Data Science',
            'graduation_year' => 2024,
        ]);
        $teamLeader2->assignRole('team_leader');

        // Create team members
        for ($i = 1; $i <= 10; $i++) {
            $member = User::create([
                'name' => "Team Member $i",
                'email' => "member$i@team.com",
                'password' => bcrypt('password'),
                'email_verified_at' => now(),
                'phone' => '+123456789' . str_pad($i, 1, '0', STR_PAD_LEFT),
                'university' => ['Harvard', 'Yale', 'Princeton', 'Columbia', 'Brown'][rand(0, 4)],
                'major' => ['Computer Science', 'Engineering', 'Mathematics', 'Physics', 'Data Science'][rand(0, 4)],
                'graduation_year' => rand(2024, 2027),
            ]);
            $member->assignRole('team_member');
        }

        // Create regular users
        for ($i = 1; $i <= 5; $i++) {
            $user = User::create([
                'name' => "User $i",
                'email' => "user$i@example.com",
                'password' => bcrypt('password'),
                'email_verified_at' => now(),
            ]);
            $user->assignRole('user');
        }
    }
}
