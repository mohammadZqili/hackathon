<?php

namespace Database\Seeders;

use App\Models\Idea;
use App\Models\Team;
use App\Models\Track;
use App\Models\User;
use App\Models\HackathonEdition;
use Illuminate\Database\Seeder;

class IdeaSeeder extends Seeder
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

        // Get teams from current edition
        $teams = Team::where('hackathon_id', $currentEdition->id)->get();
        
        if ($teams->isEmpty()) {
            $this->command->warn('No teams found for current edition. Please run TeamSeeder first.');
            return;
        }

        // Get tracks from current hackathon
        $tracks = Track::whereHas('hackathon', function($q) {
            $q->where('is_current', true);
        })->get();
        
        if ($tracks->isEmpty()) {
            $this->command->warn('No tracks found for current hackathon. Please run TrackSeeder first.');
            return;
        }

        // Get track supervisors
        $supervisors = User::role('track_supervisor')->get();

        $ideaTemplates = [
            [
                'title' => 'Smart Healthcare Monitoring System',
                'description' => 'An IoT-based system that continuously monitors patient vital signs and alerts healthcare providers in real-time for critical conditions.',
                'problem_statement' => 'Traditional patient monitoring requires constant manual checks, leading to delayed responses in emergency situations and inefficient resource allocation.',
                'solution_approach' => 'Develop a wearable device integrated with cloud-based analytics that provides real-time monitoring, predictive alerts, and automated emergency notifications.',
                'expected_impact' => 'Reduce emergency response time by 60%, improve patient outcomes, and optimize healthcare resource allocation.',
                'technologies' => ["IoT sensors", "Machine Learning", "Cloud Computing", "Mobile Apps"],
                'status' => 'submitted'
            ],
            [
                'title' => 'AI-Powered Traffic Management',
                'description' => 'An intelligent traffic control system that uses computer vision and AI to optimize traffic flow and reduce congestion in urban areas.',
                'problem_statement' => 'Urban traffic congestion leads to increased pollution, fuel consumption, and economic losses due to time delays.',
                'solution_approach' => 'Implement computer vision cameras at intersections with AI algorithms that analyze traffic patterns and dynamically adjust signal timing.',
                'expected_impact' => 'Reduce traffic congestion by 40%, decrease emissions by 25%, and save commuters an average of 30 minutes daily.',
                'technologies' => ["Computer Vision", "Artificial Intelligence", "Edge Computing", "Real-time Analytics"],
                'status' => 'under_review'
            ],
            [
                'title' => 'Sustainable Agriculture Platform',
                'description' => 'A comprehensive platform that helps farmers optimize crop yields while minimizing environmental impact through data-driven insights.',
                'problem_statement' => 'Farmers lack access to real-time data about soil conditions, weather patterns, and crop health, leading to suboptimal farming decisions.',
                'solution_approach' => 'Create a platform combining satellite imagery, soil sensors, and weather data with AI recommendations for irrigation, fertilization, and pest control.',
                'expected_impact' => 'Increase crop yields by 30%, reduce water usage by 40%, and decrease pesticide use by 50%.',
                'technologies' => ["Satellite Imagery", "IoT Sensors", "Machine Learning", "Mobile Platform"],
                'status' => 'accepted'
            ],
            [
                'title' => 'Blockchain-Based Supply Chain Transparency',
                'description' => 'A blockchain solution that provides end-to-end traceability and transparency in supply chains for food and pharmaceutical products.',
                'problem_statement' => 'Consumers lack visibility into product origins, manufacturing processes, and supply chain integrity, especially for critical items like food and medicine.',
                'solution_approach' => 'Implement a blockchain-based tracking system that records every step of the supply chain with immutable records and QR code scanning for consumers.',
                'expected_impact' => 'Reduce counterfeit products by 80%, improve consumer trust, and enable rapid response to contamination incidents.',
                'technologies' => ["Blockchain", "Smart Contracts", "QR Codes", "Web3"],
                'status' => 'needs_revision'
            ],
            [
                'title' => 'Mental Health Support Chatbot',
                'description' => 'An AI-powered chatbot that provides 24/7 mental health support, crisis intervention, and connects users with appropriate resources.',
                'problem_statement' => 'Mental health support is often inaccessible due to cost, stigma, and limited availability of qualified professionals.',
                'solution_approach' => 'Develop a conversational AI trained on mental health protocols that can provide immediate support, assess risk levels, and guide users to professional help when needed.',
                'expected_impact' => 'Provide immediate support to 10,000+ users monthly, reduce crisis response time, and bridge the gap to professional mental health services.',
                'technologies' => ["Natural Language Processing", "Machine Learning", "Crisis Detection", "Mobile Apps"],
                'status' => 'draft'
            ],
            [
                'title' => 'Smart Energy Grid Optimization',
                'description' => 'An intelligent system that optimizes energy distribution in smart grids by predicting demand patterns and integrating renewable energy sources.',
                'problem_statement' => 'Traditional energy grids struggle to efficiently integrate renewable energy sources and respond to fluctuating demand patterns.',
                'solution_approach' => 'Create a predictive system that uses machine learning to forecast energy demand and automatically balance load distribution across multiple energy sources.',
                'expected_impact' => 'Improve energy efficiency by 35%, increase renewable energy integration by 50%, and reduce operational costs by 20%.',
                'technologies' => ["Machine Learning", "IoT Integration", "Predictive Analytics", "Smart Grid Technology"],
                'status' => 'rejected'
            ]
        ];

        foreach ($ideaTemplates as $index => $ideaData) {
            // Randomly assign to teams and tracks
            $team = $teams->random();
            $track = $tracks->random();
            $supervisor = $supervisors->isNotEmpty() ? $supervisors->random() : null;
            
            $idea = Idea::create([
                'team_id' => $team->id,
                'track_id' => $track->id,
                'title' => $ideaData['title'],
                'description' => $ideaData['description'],
                'problem_statement' => $ideaData['problem_statement'],
                'solution_approach' => $ideaData['solution_approach'],
                'expected_impact' => $ideaData['expected_impact'],
                'technologies' => json_encode($ideaData['technologies']),
                'status' => $ideaData['status'],
                'submitted_at' => in_array($ideaData['status'], ['submitted', 'under_review', 'accepted', 'rejected', 'needs_revision']) 
                    ? now()->subDays(rand(1, 30)) 
                    : null,
                'reviewed_at' => in_array($ideaData['status'], ['under_review', 'accepted', 'rejected', 'needs_revision']) 
                    ? now()->subDays(rand(1, 15)) 
                    : null,
                'reviewed_by' => in_array($ideaData['status'], ['under_review', 'accepted', 'rejected', 'needs_revision']) 
                    ? $supervisor?->id 
                    : null,
                'score' => in_array($ideaData['status'], ['accepted']) 
                    ? rand(70, 95) 
                    : (in_array($ideaData['status'], ['rejected']) ? rand(20, 50) : null),
                'feedback' => in_array($ideaData['status'], ['accepted', 'rejected', 'needs_revision']) 
                    ? ($ideaData['status'] === 'accepted' 
                        ? 'Excellent concept with strong technical approach and clear implementation path. This idea shows great potential for real-world impact.' 
                        : ($ideaData['status'] === 'rejected' 
                            ? 'While the concept has merit, the technical approach needs significant refinement and the implementation timeline is unrealistic for the hackathon scope.' 
                            : 'Good foundation but requires clarification on technical implementation details and market validation approach.'))
                    : null,
            ]);

            $this->command->info("Created idea: {$idea->title} for team: {$team->name}");
        }

        $this->command->info('Idea seeding completed successfully!');
    }
}
