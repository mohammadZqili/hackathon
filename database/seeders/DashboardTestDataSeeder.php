<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\HackathonEdition;
use App\Models\Team;
use App\Models\Idea;
use App\Models\Track;

class DashboardTestDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('🌱 Seeding dashboard test data...');

        // Create current hackathon edition if none exists
        $currentEdition = HackathonEdition::where('is_current', true)->first();
        
        if (!$currentEdition) {
            $currentEdition = HackathonEdition::create([
                'name' => 'Hackathon البيئة 2025',
                'year' => 2025,
                'theme' => 'البيئة والاستدامة',
                'description' => 'هاكاثون مخصص لحلول البيئة والاستدامة',
                'status' => 'active',
                'is_current' => true,
                'registration_start_date' => now()->subDays(30),
                'registration_end_date' => now()->addDays(30),
                'idea_submission_start_date' => now()->subDays(20),
                'idea_submission_end_date' => now()->addDays(40),
                'max_team_size' => 5,
                'max_teams' => 100,
            ]);
            
            $this->command->info("✅ Created current hackathon edition: {$currentEdition->name}");
        }

        // Create default tracks
        $tracks = [
            ['name' => 'الطاقة المتجددة', 'description' => 'حلول للطاقة المتجددة والنظيفة'],
            ['name' => 'إدارة النفايات', 'description' => 'حلول ذكية لإدارة وإعادة تدوير النفايات'],
            ['name' => 'الزراعة المستدامة', 'description' => 'تقنيات حديثة للزراعة المستدامة'],
            ['name' => 'المياه والحفاظ عليها', 'description' => 'حلول لترشيد استهلاك المياه'],
        ];

        foreach ($tracks as $trackData) {
            Track::firstOrCreate(
                ['name' => $trackData['name'], 'hackathon_id' => $currentEdition->id],
                [
                    'description' => $trackData['description'],
                    'max_teams' => 25,
                    'is_active' => true,
                ]
            );
        }

        // Create test users with different roles
        $adminUser = User::firstOrCreate(
            ['email' => 'admin@ruman.sa'],
            [
                'name' => 'مدير النظام',
                'national_id' => '1234567890',
                'phone' => '+966501234567',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );
        $adminUser->assignRole('hackathon_admin');

        $teamLeaderUsers = [];
        for ($i = 1; $i <= 5; $i++) {
            $user = User::firstOrCreate(
                ['email' => "leader{$i}@ruman.sa"],
                [
                    'name' => "قائد الفريق {$i}",
                    'national_id' => "123456789{$i}",
                    'phone' => "+96650123456{$i}",
                    'password' => Hash::make('password'),
                    'email_verified_at' => now(),
                ]
            );
            $user->assignRole('team_leader');
            $teamLeaderUsers[] = $user;
        }

        // Create test teams with valid leaders
        $trackIds = Track::where('hackathon_id', $currentEdition->id)->pluck('id')->toArray();
        
        for ($i = 1; $i <= 5; $i++) {
            $team = Team::firstOrCreate(
                ['name' => "فريق البيئة {$i}"],
                [
                    'slug' => "team-environment-{$i}",
                    'description' => "فريق متخصص في حلول البيئة رقم {$i}",
                    'user_id' => $teamLeaderUsers[$i-1]->id,  // Required field
                    'personal_team' => false,  // Required field
                    'hackathon_id' => $currentEdition->id,
                    'leader_id' => $teamLeaderUsers[$i-1]->id,
                    'track_id' => $trackIds[array_rand($trackIds)],
                    'max_members' => 5,
                    'status' => 'active',
                    'invite_code' => strtoupper(\Illuminate\Support\Str::random(8)),
                ]
            );

            // Create idea for the team
            Idea::firstOrCreate(
                ['title' => "فكرة مشروع الفريق {$i}"],
                [
                    'description' => "وصف مفصل لفكرة المشروع الخاص بالفريق رقم {$i} في مجال البيئة والاستدامة",
                    'problem_statement' => "مشكلة بيئية تحتاج لحل مبتكر",
                    'solution_approach' => "حل مقترح باستخدام التكنولوجيا الحديثة",  // Fixed column name
                    'expected_impact' => "تأثير إيجابي على المجتمع والمؤسسات البيئية",  // Fixed column name
                    'technologies' => json_encode(['React', 'Laravel', 'IoT', 'AI']),
                    'team_id' => $team->id,
                    'track_id' => $team->track_id,
                    'status' => ['draft', 'submitted', 'under_review', 'accepted'][array_rand(['draft', 'submitted', 'under_review', 'accepted'])],
                    'submitted_at' => now()->subDays(rand(1, 15)),
                ]
            );
        }

        $this->command->info('✅ Dashboard test data seeded successfully!');
        $this->command->info('📊 Created:');
        $this->command->info("   - 1 Hackathon Edition: {$currentEdition->name}");
        $this->command->info("   - " . count($tracks) . " Tracks");
        $this->command->info("   - " . (count($teamLeaderUsers) + 1) . " Users (1 admin + " . count($teamLeaderUsers) . " team leaders)");
        $this->command->info("   - 5 Teams with valid leaders");
        $this->command->info("   - 5 Ideas");
    }
}
