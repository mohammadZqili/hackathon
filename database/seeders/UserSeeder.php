<?php

namespace Database\Seeders;

use App\Enums\UserType;
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
                'user_type' => UserType::ADMIN->value,
            ]
        );

        if (!$superuser->hasRole(UserType::ADMIN->value)) {
            $superuser->assignRole(UserType::ADMIN->value);
        }

        // Create system admin
        $systemAdmin = User::firstOrCreate(
            ['email' => 'system@hackathon.com'],
            [
                'name' => 'System Admin',
                'password' => bcrypt('password'),
                'email_verified_at' => now(),
                'user_type' => UserType::ADMIN->value,
            ]
        );

        if (!$systemAdmin->hasRole(UserType::ADMIN->value)) {
            $systemAdmin->assignRole(UserType::ADMIN->value);
        }

        // Create hackathon admin
        $hackathonAdmin = User::firstOrCreate(
            ['email' => 'admin@hackathon.com'],
            [
                'name' => 'Hackathon Admin',
                'password' => bcrypt('password'),
                'email_verified_at' => now(),
                'user_type' => UserType::HACKATHON_ADMIN->value,
            ]
        );

        if (!$hackathonAdmin->hasRole(UserType::HACKATHON_ADMIN->value)) {
            $hackathonAdmin->assignRole(UserType::HACKATHON_ADMIN->value);
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
                'user_type' => UserType::TRACK_SUPERVISOR->value,
            ]
        );

        if (!$trackSupervisor1->hasRole(UserType::TRACK_SUPERVISOR->value)) {
            $trackSupervisor1->assignRole(UserType::TRACK_SUPERVISOR->value);
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
                'user_type' => UserType::TRACK_SUPERVISOR->value,
            ]
        );
;
        if (!$trackSupervisor2->hasRole(UserType::TRACK_SUPERVISOR->value)) {
            $trackSupervisor2->assignRole(UserType::TRACK_SUPERVISOR->value);
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
                'user_type' => UserType::TEAM_LEADER->value,
            ]
        );

        if (!$teamLeader1->hasRole(UserType::TEAM_LEADER->value)) {
            $teamLeader1->assignRole(UserType::TEAM_LEADER->value);
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
                'user_type' => UserType::TEAM_LEADER->value,
            ]
        );
        if (!$teamLeader2->hasRole(UserType::TEAM_LEADER->value)) {
            $teamLeader2->assignRole(UserType::TEAM_LEADER->value);
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
                    'user_type' => UserType::TEAM_MEMBER->value,
                ]
            );
            if (!$member->hasRole(UserType::TEAM_MEMBER->value)) {
                $member->assignRole(UserType::TEAM_MEMBER->value);
            }
        }

        // Create regular users (visitors)
        for ($i = 1; $i <= 5; $i++) {
            $user = User::firstOrCreate(
                ['email' => "user$i@example.com"],
                [
                    'name' => "User $i",
                    'password' => bcrypt('password'),
                    'email_verified_at' => now(),
                    'user_type' => UserType::VISITOR->value,
                ]
            );
            if (!$user->hasRole(UserType::VISITOR->value)) {
                $user->assignRole(UserType::VISITOR->value);
            }
        }
    }
}
