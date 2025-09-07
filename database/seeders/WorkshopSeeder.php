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

        // Get a speaker user or create one
        $speaker = User::whereHas('roles', function($q) {
            $q->where('name', 'track_supervisor');
        })->first();

        if (!$speaker) {
            $speaker = User::first();
        }

        $workshops = [
            [
                'title' => 'Introduction to Machine Learning with TensorFlow',
                'description' => 'Learn the fundamentals of machine learning and build your first neural network using TensorFlow. This hands-on workshop covers supervised learning, data preprocessing, and model evaluation.',
                'speaker_name' => 'Dr. Sarah Johnson',
                'speaker_bio' => 'AI Research Scientist at TechCorp with 10+ years of experience in deep learning',
                'hackathon_edition_id' => $currentEdition->id,
                'date' => Carbon::parse('2024-07-15 10:00:00'),
                'duration' => 120,
                'location' => 'Workshop Room A',
                'capacity' => 50,
                'is_online' => true,
                'meeting_link' => 'https://zoom.us/j/123456789',
                'requirements' => 'Laptop with Python 3.8+ installed, basic Python knowledge',
                'materials_link' => 'https://github.com/workshop/ml-tensorflow',
                'level' => 'beginner',
                'tags' => json_encode(['AI', 'Machine Learning', 'TensorFlow', 'Python']),
                'status' => 'scheduled'
            ],
            [
                'title' => 'Building Smart Contracts on Ethereum',
                'description' => 'Dive into blockchain development by creating and deploying smart contracts on the Ethereum network. Learn Solidity programming and best practices for secure contract development.',
                'speaker_name' => 'Alex Chen',
                'speaker_bio' => 'Blockchain Developer and Ethereum Foundation Contributor',
                'hackathon_edition_id' => $currentEdition->id,
                'date' => Carbon::parse('2024-07-15 14:00:00'),
                'duration' => 180,
                'location' => 'Workshop Room B',
                'capacity' => 40,
                'is_online' => true,
                'meeting_link' => 'https://zoom.us/j/987654321',
                'requirements' => 'MetaMask wallet, basic JavaScript knowledge',
                'materials_link' => 'https://github.com/workshop/ethereum-smart-contracts',
                'level' => 'intermediate',
                'tags' => json_encode(['Blockchain', 'Ethereum', 'Solidity', 'Web3']),
                'status' => 'scheduled'
            ],
            [
                'title' => 'IoT Development with Arduino and Raspberry Pi',
                'description' => 'Get hands-on experience building IoT devices using Arduino and Raspberry Pi. Learn sensor integration, data collection, and cloud connectivity.',
                'speaker_name' => 'Prof. Michael Brown',
                'speaker_bio' => 'Hardware Engineer and IoT Solutions Architect',
                'hackathon_edition_id' => $currentEdition->id,
                'date' => Carbon::parse('2024-07-16 09:00:00'),
                'duration' => 150,
                'location' => 'Hardware Lab',
                'capacity' => 30,
                'is_online' => false,
                'meeting_link' => null,
                'requirements' => 'Basic electronics knowledge, C/C++ programming basics',
                'materials_link' => 'https://github.com/workshop/iot-arduino-rpi',
                'level' => 'intermediate',
                'tags' => json_encode(['IoT', 'Arduino', 'Raspberry Pi', 'Hardware']),
                'status' => 'scheduled'
            ],
            [
                'title' => 'UI/UX Design Principles for Developers',
                'description' => 'Learn essential design principles to create user-friendly interfaces. Cover user research, wireframing, prototyping, and usability testing.',
                'speaker_name' => 'Emma Wilson',
                'speaker_bio' => 'Senior UX Designer at DesignHub, Google Design Expert',
                'hackathon_edition_id' => $currentEdition->id,
                'date' => Carbon::parse('2024-07-16 13:00:00'),
                'duration' => 90,
                'location' => 'Design Studio',
                'capacity' => 60,
                'is_online' => true,
                'meeting_link' => 'https://zoom.us/j/555666777',
                'requirements' => 'Figma account (free), no prior design experience needed',
                'materials_link' => 'https://github.com/workshop/ux-design-basics',
                'level' => 'beginner',
                'tags' => json_encode(['Design', 'UX', 'UI', 'Figma', 'Prototyping']),
                'status' => 'scheduled'
            ],
            [
                'title' => 'API Development with FastAPI and Python',
                'description' => 'Build modern, fast web APIs with Python using FastAPI framework. Learn about async programming, data validation, and API documentation.',
                'speaker_name' => 'Carlos Rodriguez',
                'speaker_bio' => 'Backend Engineer at APIFirst, FastAPI Core Contributor',
                'hackathon_edition_id' => $currentEdition->id,
                'date' => Carbon::parse('2024-07-16 16:00:00'),
                'duration' => 120,
                'location' => 'Workshop Room A',
                'capacity' => 45,
                'is_online' => true,
                'meeting_link' => 'https://zoom.us/j/111222333',
                'requirements' => 'Python 3.7+, basic REST API knowledge',
                'materials_link' => 'https://github.com/workshop/fastapi-tutorial',
                'level' => 'intermediate',
                'tags' => json_encode(['Python', 'FastAPI', 'REST', 'Backend', 'APIs']),
                'status' => 'scheduled'
            ],
            [
                'title' => 'DevOps Essentials: Docker & Kubernetes',
                'description' => 'Master containerization with Docker and orchestration with Kubernetes. Deploy and scale applications in cloud environments.',
                'speaker_name' => 'David Kim',
                'speaker_bio' => 'DevOps Lead at CloudScale, CNCF Ambassador',
                'hackathon_edition_id' => $currentEdition->id,
                'date' => Carbon::parse('2024-07-17 10:00:00'),
                'duration' => 180,
                'location' => 'Cloud Lab',
                'capacity' => 35,
                'is_online' => true,
                'meeting_link' => 'https://zoom.us/j/999888777',
                'requirements' => 'Docker Desktop installed, basic Linux knowledge',
                'materials_link' => 'https://github.com/workshop/docker-k8s-intro',
                'level' => 'intermediate',
                'tags' => json_encode(['DevOps', 'Docker', 'Kubernetes', 'Cloud', 'Containers']),
                'status' => 'scheduled'
            ],
            [
                'title' => 'Pitch Perfect: Presenting Your Hackathon Project',
                'description' => 'Learn how to effectively pitch your hackathon project to judges. Cover storytelling, demo techniques, and handling Q&A sessions.',
                'speaker_name' => 'Lisa Anderson',
                'speaker_bio' => 'Startup Coach and Venture Partner at InnoVentures',
                'hackathon_edition_id' => $currentEdition->id,
                'date' => Carbon::parse('2024-07-17 14:00:00'),
                'duration' => 60,
                'location' => 'Main Auditorium',
                'capacity' => 100,
                'is_online' => true,
                'meeting_link' => 'https://zoom.us/j/777666555',
                'requirements' => 'None - bring your enthusiasm!',
                'materials_link' => 'https://github.com/workshop/pitch-deck-template',
                'level' => 'beginner',
                'tags' => json_encode(['Pitching', 'Presentation', 'Soft Skills', 'Communication']),
                'status' => 'scheduled'
            ]
        ];

        foreach ($workshops as $workshop) {
            Workshop::create($workshop);
        }
    }
}