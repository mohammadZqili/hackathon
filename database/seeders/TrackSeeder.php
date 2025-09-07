<?php

namespace Database\Seeders;

use App\Models\Track;
use App\Models\HackathonEdition;
use Illuminate\Database\Seeder;

class TrackSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $currentEdition = HackathonEdition::where('is_current', true)->first();
        
        if (!$currentEdition) {
            $this->command->warn('No current hackathon edition found. Please run HackathonEditionSeeder first.');
            return;
        }

        $tracks = [
            [
                'name' => 'AI & Machine Learning',
                'description' => 'Develop innovative AI solutions for real-world problems using machine learning, deep learning, and neural networks.',
                'hackathon_edition_id' => $currentEdition->id,
                'max_teams' => 20,
                'is_active' => true,
                'color' => '#8B5CF6',
                'icon' => 'brain',
                'requirements' => 'Basic understanding of Python, ML frameworks (TensorFlow/PyTorch), and data science concepts.'
            ],
            [
                'name' => 'Web3 & Blockchain',
                'description' => 'Build decentralized applications, smart contracts, and blockchain-based solutions for transparency and security.',
                'hackathon_edition_id' => $currentEdition->id,
                'max_teams' => 15,
                'is_active' => true,
                'color' => '#F59E0B',
                'icon' => 'cube',
                'requirements' => 'Knowledge of blockchain concepts, smart contracts, and Web3 technologies.'
            ],
            [
                'name' => 'IoT & Smart Cities',
                'description' => 'Create connected devices and systems that make cities smarter, more efficient, and sustainable.',
                'hackathon_edition_id' => $currentEdition->id,
                'max_teams' => 18,
                'is_active' => true,
                'color' => '#10B981',
                'icon' => 'wifi',
                'requirements' => 'Experience with IoT platforms, sensors, and embedded systems programming.'
            ],
            [
                'name' => 'FinTech Innovation',
                'description' => 'Revolutionize financial services with innovative payment solutions, banking apps, and investment platforms.',
                'hackathon_edition_id' => $currentEdition->id,
                'max_teams' => 12,
                'is_active' => true,
                'color' => '#3B82F6',
                'icon' => 'currency-dollar',
                'requirements' => 'Understanding of financial systems, APIs, and security best practices.'
            ],
            [
                'name' => 'HealthTech & Biomedical',
                'description' => 'Develop technology solutions for healthcare, medical diagnosis, patient care, and health monitoring.',
                'hackathon_edition_id' => $currentEdition->id,
                'max_teams' => 15,
                'is_active' => true,
                'color' => '#EF4444',
                'icon' => 'heart',
                'requirements' => 'Interest in healthcare technology, data privacy, and medical device integration.'
            ],
            [
                'name' => 'EdTech & E-Learning',
                'description' => 'Transform education with innovative learning platforms, tools, and technologies.',
                'hackathon_edition_id' => $currentEdition->id,
                'max_teams' => 10,
                'is_active' => true,
                'color' => '#06B6D4',
                'icon' => 'academic-cap',
                'requirements' => 'Experience in educational technology, UX design, and learning management systems.'
            ],
            [
                'name' => 'Sustainability & GreenTech',
                'description' => 'Build solutions for environmental challenges, renewable energy, and sustainable living.',
                'hackathon_edition_id' => $currentEdition->id,
                'max_teams' => 20,
                'is_active' => true,
                'color' => '#84CC16',
                'icon' => 'globe',
                'requirements' => 'Passion for environmental solutions and sustainable technology.'
            ]
        ];

        foreach ($tracks as $track) {
            Track::create($track);
        }
    }
}
