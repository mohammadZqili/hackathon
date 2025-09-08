<?php

namespace Database\Seeders;

use App\Models\Workshop;
use App\Models\HackathonEdition;
use App\Models\User;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class WorkshopSeeder extends Seeder
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

        // Get the hackathon from the edition
        $hackathon = $currentEdition->hackathon ?? \App\Models\Hackathon::first();
        
        if (!$hackathon) {
            $this->command->warn('No hackathon found.');
            return;
        }

        $workshops = [
            [
                'hackathon_id' => $hackathon->id,
                'title' => 'Introduction to Machine Learning with TensorFlow',
                'slug' => 'introduction-to-machine-learning-tensorflow',
                'description' => 'Learn the fundamentals of machine learning and build your first neural network using TensorFlow. This hands-on workshop covers supervised learning, data preprocessing, and model evaluation.',
                'type' => 'workshop',
                'start_time' => Carbon::parse('2024-07-15 10:00:00'),
                'end_time' => Carbon::parse('2024-07-15 12:00:00'),
                'format' => 'hybrid',
                'location' => 'Workshop Room A / https://zoom.us/j/123456789',
                'max_attendees' => 50,
                'prerequisites' => 'Laptop with Python 3.8+ installed, basic Python knowledge',
                'materials' => ['github' => 'https://github.com/workshop/ml-tensorflow', 'level' => 'beginner'],
                'is_active' => true,
                'requires_registration' => true,
                'registration_deadline' => Carbon::parse('2024-07-14 23:59:59'),
                'settings' => ['speaker_name' => 'Dr. Sarah Johnson', 'speaker_bio' => 'AI Research Scientist at TechCorp', 'tags' => ['AI', 'Machine Learning', 'TensorFlow']]
            ],
            [
                'hackathon_id' => $hackathon->id,
                'title' => 'Building Smart Contracts on Ethereum',
                'slug' => 'building-smart-contracts-ethereum',
                'description' => 'Dive into blockchain development by creating and deploying smart contracts on the Ethereum network. Learn Solidity programming and best practices for secure contract development.',
                'type' => 'workshop',
                'start_time' => Carbon::parse('2024-07-15 14:00:00'),
                'end_time' => Carbon::parse('2024-07-15 17:00:00'),
                'format' => 'hybrid',
                'location' => 'Workshop Room B / https://zoom.us/j/987654321',
                'max_attendees' => 40,
                'prerequisites' => 'MetaMask wallet, basic JavaScript knowledge',
                'materials' => ['github' => 'https://github.com/workshop/ethereum-smart-contracts', 'level' => 'intermediate'],
                'is_active' => true,
                'requires_registration' => true,
                'registration_deadline' => Carbon::parse('2024-07-14 23:59:59'),
                'settings' => ['speaker_name' => 'Alex Chen', 'speaker_bio' => 'Blockchain Developer and Ethereum Foundation Contributor', 'tags' => ['Blockchain', 'Ethereum', 'Solidity']]
            ],
            [
                'hackathon_id' => $hackathon->id,
                'title' => 'IoT Development with Arduino and Raspberry Pi',
                'slug' => 'iot-development-arduino-raspberry-pi',
                'description' => 'Get hands-on experience building IoT devices using Arduino and Raspberry Pi. Learn sensor integration, data collection, and cloud connectivity.',
                'type' => 'workshop',
                'start_time' => Carbon::parse('2024-07-16 09:00:00'),
                'end_time' => Carbon::parse('2024-07-16 11:30:00'),
                'format' => 'offline',
                'location' => 'Hardware Lab',
                'max_attendees' => 30,
                'prerequisites' => 'Basic electronics knowledge, C/C++ programming basics',
                'materials' => ['github' => 'https://github.com/workshop/iot-arduino-rpi', 'level' => 'intermediate'],
                'is_active' => true,
                'requires_registration' => true,
                'registration_deadline' => Carbon::parse('2024-07-15 23:59:59'),
                'settings' => ['speaker_name' => 'Prof. Michael Brown', 'speaker_bio' => 'Hardware Engineer and IoT Solutions Architect', 'tags' => ['IoT', 'Arduino', 'Raspberry Pi']]
            ],
            [
                'hackathon_id' => $hackathon->id,
                'title' => 'UI/UX Design Principles for Developers',
                'slug' => 'ui-ux-design-principles-developers',
                'description' => 'Learn essential design principles to create user-friendly interfaces. Cover user research, wireframing, prototyping, and usability testing.',
                'type' => 'seminar',
                'start_time' => Carbon::parse('2024-07-16 13:00:00'),
                'end_time' => Carbon::parse('2024-07-16 14:30:00'),
                'format' => 'hybrid',
                'location' => 'Design Studio / https://zoom.us/j/555666777',
                'max_attendees' => 60,
                'prerequisites' => 'Figma account (free), no prior design experience needed',
                'materials' => ['github' => 'https://github.com/workshop/ux-design-basics', 'level' => 'beginner'],
                'is_active' => true,
                'requires_registration' => true,
                'registration_deadline' => Carbon::parse('2024-07-15 23:59:59'),
                'settings' => ['speaker_name' => 'Emma Wilson', 'speaker_bio' => 'Senior UX Designer at DesignHub', 'tags' => ['Design', 'UX', 'UI', 'Figma']]
            ]
        ];

        foreach ($workshops as $workshop) {
            Workshop::firstOrCreate(
                ['slug' => $workshop['slug']],
                $workshop
            );
        }
    }
}