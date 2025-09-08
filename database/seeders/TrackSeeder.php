<?php

namespace Database\Seeders;

use App\Models\Track;
use App\Models\Hackathon;
use Illuminate\Database\Seeder;

class TrackSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $currentHackathon = Hackathon::where('is_current', true)->first();

        if (!$currentHackathon) {
            $this->command->warn('No current hackathon found. Please run HackathonSeeder first.');
            return;
        }

        $tracks = [
            [
                'name' => 'AI & Machine Learning',
                'description' => 'Develop innovative AI solutions for real-world problems using machine learning, deep learning, and neural networks.',
                'hackathon_id' => $currentHackathon->id,
                'max_teams' => 20,
                'is_active' => true,
                'color' => '#8B5CF6',
                'icon' => 'brain',
                'evaluation_criteria' => [
                    'innovation' => 30,
                    'technical_implementation' => 25,
                    'scalability' => 20,
                    'presentation' => 15,
                    'business_value' => 10
                ],
                'sort_order' => 1
            ],
            [
                'name' => 'Web3 & Blockchain',
                'description' => 'Build decentralized applications, smart contracts, and blockchain-based solutions for transparency and security.',
                'hackathon_id' => $currentHackathon->id,
                'max_teams' => 15,
                'is_active' => true,
                'color' => '#F59E0B',
                'icon' => 'cube',
                'evaluation_criteria' => [
                    'innovation' => 30,
                    'technical_implementation' => 25,
                    'security' => 20,
                    'user_experience' => 15,
                    'market_potential' => 10
                ],
                'sort_order' => 2
            ],
            [
                'name' => 'IoT & Smart Cities',
                'description' => 'Create connected devices and systems that make cities smarter, more efficient, and sustainable.',
                'hackathon_id' => $currentHackathon->id,
                'max_teams' => 18,
                'is_active' => true,
                'color' => '#10B981',
                'icon' => 'wifi',
                'evaluation_criteria' => [
                    'innovation' => 25,
                    'technical_implementation' => 25,
                    'real_world_applicability' => 20,
                    'sustainability' => 20,
                    'presentation' => 10
                ],
                'sort_order' => 3
            ],
            [
                'name' => 'FinTech Innovation',
                'description' => 'Revolutionize financial services with innovative payment solutions, banking apps, and investment platforms.',
                'hackathon_id' => $currentHackathon->id,
                'max_teams' => 12,
                'is_active' => true,
                'color' => '#3B82F6',
                'icon' => 'currency-dollar',
                'evaluation_criteria' => [
                    'innovation' => 25,
                    'security' => 30,
                    'user_experience' => 20,
                    'regulatory_compliance' => 15,
                    'market_potential' => 10
                ],
                'sort_order' => 4
            ],
            [
                'name' => 'HealthTech & Biomedical',
                'description' => 'Develop technology solutions for healthcare, medical diagnosis, patient care, and health monitoring.',
                'hackathon_id' => $currentHackathon->id,
                'max_teams' => 15,
                'is_active' => true,
                'color' => '#EF4444',
                'icon' => 'heart',
                'evaluation_criteria' => [
                    'innovation' => 25,
                    'clinical_relevance' => 25,
                    'technical_implementation' => 20,
                    'user_experience' => 15,
                    'regulatory_feasibility' => 15
                ],
                'sort_order' => 5
            ],
            [
                'name' => 'EdTech & E-Learning',
                'description' => 'Transform education with innovative learning platforms, tools, and technologies.',
                'hackathon_id' => $currentHackathon->id,
                'max_teams' => 10,
                'is_active' => true,
                'color' => '#06B6D4',
                'icon' => 'academic-cap',
                'evaluation_criteria' => [
                    'innovation' => 25,
                    'educational_impact' => 30,
                    'user_experience' => 20,
                    'scalability' => 15,
                    'accessibility' => 10
                ],
                'sort_order' => 6
            ],
            [
                'name' => 'Sustainability & GreenTech',
                'description' => 'Build solutions for environmental challenges, renewable energy, and sustainable living.',
                'hackathon_id' => $currentHackathon->id,
                'max_teams' => 20,
                'is_active' => true,
                'color' => '#84CC16',
                'icon' => 'globe',
                'evaluation_criteria' => [
                    'environmental_impact' => 30,
                    'innovation' => 25,
                    'feasibility' => 20,
                    'scalability' => 15,
                    'cost_effectiveness' => 10
                ],
                'sort_order' => 7
            ]
        ];

        foreach ($tracks as $track) {
            Track::create($track);
        }
    }
}
