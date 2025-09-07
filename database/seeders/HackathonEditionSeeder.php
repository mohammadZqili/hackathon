<?php

namespace Database\Seeders;

use App\Models\HackathonEdition;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class HackathonEditionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create past edition
        HackathonEdition::create([
            'name' => 'Hackathon 2023',
            'year' => 2023,
            'registration_start_date' => Carbon::parse('2023-06-01'),
            'registration_end_date' => Carbon::parse('2023-06-03'),
            'description' => 'The inaugural hackathon focused on AI and sustainability solutions.',
            'is_current' => false,
            'max_teams' => 50,
            'max_members_per_team' => 5,
            'allow_remote' => false,
            'venue' => 'Tech Innovation Center, Downtown',
            'prize_pool' => 50000,
            'theme' => 'AI for Good',
            'status' => 'completed'
        ]);

        // Create current edition
        HackathonEdition::create([
            'name' => 'Hackathon 2024',
            'year' => 2024,
            'start_date' => Carbon::parse('2024-07-15'),
            'end_date' => Carbon::parse('2024-07-17'),
            'description' => 'Annual hackathon bringing together innovators to solve real-world challenges using cutting-edge technology.',
            'is_current' => true,
            'max_teams' => 100,
            'max_members_per_team' => 5,
            'registration_start' => Carbon::parse('2024-06-01'),
            'registration_end' => Carbon::parse('2024-07-10'),
            'allow_remote' => true,
            'venue' => 'Innovation Hub & Virtual Platform',
            'prize_pool' => 100000,
            'theme' => 'Building Tomorrow: Smart Cities & Sustainable Tech',
            'status' => 'active'
        ]);

        // Create upcoming edition
        HackathonEdition::create([
            'name' => 'Hackathon 2025',
            'year' => 2025,
            'start_date' => Carbon::parse('2025-08-20'),
            'end_date' => Carbon::parse('2025-08-22'),
            'description' => 'Next year\'s hackathon focusing on quantum computing and blockchain innovations.',
            'is_current' => false,
            'max_teams' => 150,
            'max_members_per_team' => 6,
            'registration_start' => Carbon::parse('2025-07-01'),
            'registration_end' => Carbon::parse('2025-08-15'),
            'allow_remote' => true,
            'venue' => 'Global Tech Arena',
            'prize_pool' => 150000,
            'theme' => 'Quantum Leap: Future of Computing',
            'status' => 'planning'
        ]);
    }
}
