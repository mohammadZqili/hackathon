<?php

namespace Database\Seeders;

use App\Models\Hackathon;
use App\Models\User;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class HackathonSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get the first user to use as creator
        $user = User::first();
        if (!$user) {
            // If no user exists, create a default admin user
            $user = User::create([
                'id' => \Illuminate\Support\Str::ulid(),
                'name' => 'Admin User',
                'email' => 'admin@hackathon.com',
                'password' => bcrypt('password'),
                'email_verified_at' => now(),
            ]);
        }

        // Create past hackathon
        Hackathon::create([
            'name' => 'Hackathon 2023',
            'description' => 'The inaugural hackathon focused on AI and sustainability solutions.',
            'theme' => 'AI for Good',
            'year' => 2023,
            'registration_start_date' => Carbon::parse('2023-05-01'),
            'registration_end_date' => Carbon::parse('2023-06-01'),
            'idea_submission_start_date' => Carbon::parse('2023-06-02'),
            'idea_submission_end_date' => Carbon::parse('2023-06-10'),
            'event_start_date' => Carbon::parse('2023-06-15'),
            'event_end_date' => Carbon::parse('2023-06-17'),
            'location' => 'Tech Innovation Center, Downtown',
            'is_active' => false,
            'is_current' => false,
            'settings' => [
                'max_teams' => 50,
                'max_members_per_team' => 5,
                'allow_remote' => false,
                'prize_pool' => 50000,
                'prizes' => [
                    'first' => 25000,
                    'second' => 15000,
                    'third' => 10000
                ],
                'rules' => [
                    'Teams must have 3-5 members',
                    'All code must be written during the event',
                    'Projects must align with the theme'
                ]
            ],
            'created_by' => $user->id,
        ]);

        // Create current hackathon
        Hackathon::create([
            'name' => 'Hackathon 2024',
            'description' => 'Annual hackathon bringing together innovators to solve real-world challenges using cutting-edge technology.',
            'theme' => 'Building Tomorrow: Smart Cities & Sustainable Tech',
            'year' => 2024,
            'registration_start_date' => Carbon::parse('2024-06-01'),
            'registration_end_date' => Carbon::parse('2024-07-10'),
            'idea_submission_start_date' => Carbon::parse('2024-07-11'),
            'idea_submission_end_date' => Carbon::parse('2024-07-14'),
            'event_start_date' => Carbon::parse('2024-07-15'),
            'event_end_date' => Carbon::parse('2024-07-17'),
            'location' => 'Innovation Hub & Virtual Platform',
            'is_active' => true,
            'is_current' => true,
            'settings' => [
                'max_teams' => 100,
                'max_members_per_team' => 5,
                'allow_remote' => true,
                'prize_pool' => 100000,
                'prizes' => [
                    'first' => 50000,
                    'second' => 30000,
                    'third' => 20000
                ],
                'rules' => [
                    'Teams must have 3-5 members',
                    'Remote participation allowed',
                    'All code must be written during the event',
                    'Projects must align with the theme',
                    'Open source tools only'
                ]
            ],
            'created_by' => $user->id,
        ]);

        // Create upcoming hackathon
        Hackathon::create([
            'name' => 'Hackathon 2025',
            'description' => 'Next year\'s hackathon focusing on quantum computing and blockchain innovations.',
            'theme' => 'Quantum Leap: Future of Computing',
            'year' => 2025,
            'registration_start_date' => Carbon::parse('2025-07-01'),
            'registration_end_date' => Carbon::parse('2025-08-15'),
            'idea_submission_start_date' => Carbon::parse('2025-08-16'),
            'idea_submission_end_date' => Carbon::parse('2025-08-19'),
            'event_start_date' => Carbon::parse('2025-08-20'),
            'event_end_date' => Carbon::parse('2025-08-22'),
            'location' => 'Global Tech Arena',
            'is_active' => false,
            'is_current' => false,
            'settings' => [
                'max_teams' => 150,
                'max_members_per_team' => 6,
                'allow_remote' => true,
                'prize_pool' => 150000,
                'prizes' => [
                    'first' => 75000,
                    'second' => 45000,
                    'third' => 30000
                ],
                'rules' => [
                    'Teams must have 3-6 members',
                    'Remote participation allowed',
                    'All code must be written during the event',
                    'Projects must align with the theme'
                ]
            ],
            'created_by' => $user->id,
        ]);
    }
}
