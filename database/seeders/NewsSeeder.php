<?php

namespace Database\Seeders;

use App\Models\News;
use App\Models\HackathonEdition;
use App\Models\User;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class NewsSeeder extends Seeder
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

        $author = User::whereHas('roles', function($q) {
            $q->where('name', 'hackathon_admin');
        })->first() ?? User::first();

        $newsItems = [
            [
                'title' => 'Hackathon 2024 Registration Now Open!',
                'slug' => 'hackathon-2024-registration-open',
                'content' => 'We are thrilled to announce that registration for Hackathon 2024 is officially open! Join us for an exciting 48-hour coding marathon where innovation meets opportunity. This year\'s theme "Building Tomorrow: Smart Cities & Sustainable Tech" challenges participants to create solutions that will shape our future. Early bird registration is available until June 15th with exclusive perks including priority workshop seats and special swag bags.',
                'excerpt' => 'Registration for Hackathon 2024 is now open! Join us for 48 hours of innovation and coding.',
                'category' => 'announcement',
                'author_id' => $author->id,
                'hackathon_edition_id' => $currentEdition->id,
                'is_published' => true,
                'is_featured' => true,
                'published_at' => Carbon::now()->subDays(30),
                'views' => 1250,
                'tags' => json_encode(['registration', 'announcement', 'hackathon2024'])
            ],
            [
                'title' => 'Introducing Our Distinguished Panel of Judges',
                'slug' => 'meet-the-judges-2024',
                'content' => 'We\'re honored to introduce our panel of distinguished judges for Hackathon 2024. Leading the panel is Dr. Jane Smith, CTO of TechGiant Corp, joined by venture capitalist John Doe from Innovation Capital, and startup founder Maria Garcia who successfully exited her AI company last year. Each judge brings unique expertise and will evaluate projects based on innovation, technical implementation, business viability, and social impact.',
                'excerpt' => 'Meet the industry leaders who will be judging your innovative projects at Hackathon 2024.',
                'category' => 'announcement',
                'author_id' => $author->id,
                'hackathon_edition_id' => $currentEdition->id,
                'is_published' => true,
                'is_featured' => false,
                'published_at' => Carbon::now()->subDays(20),
                'views' => 856,
                'tags' => json_encode(['judges', 'panel', 'evaluation'])
            ],
            [
                'title' => 'Workshop Schedule Released - Register Now!',
                'slug' => 'workshop-schedule-2024',
                'content' => 'The complete workshop schedule for Hackathon 2024 is now available! We have curated an impressive lineup of 15+ workshops covering everything from AI/ML fundamentals to blockchain development, IoT solutions, and pitch preparation. All workshops are free for registered participants. Space is limited, so secure your spot today. Both in-person and virtual attendance options are available for most sessions.',
                'excerpt' => 'Check out our exciting workshop lineup and reserve your spots before they fill up!',
                'category' => 'workshop',
                'author_id' => $author->id,
                'hackathon_edition_id' => $currentEdition->id,
                'is_published' => true,
                'is_featured' => true,
                'published_at' => Carbon::now()->subDays(15),
                'views' => 2103,
                'tags' => json_encode(['workshops', 'schedule', 'learning'])
            ],
            [
                'title' => '$100,000 Prize Pool Announced!',
                'slug' => '100k-prize-pool-announcement',
                'content' => 'This year\'s hackathon features our largest prize pool ever - $100,000 in cash and prizes! The grand prize winner will receive $25,000, with category winners taking home $10,000 each. Additionally, we have special prizes for best use of sponsor APIs, most innovative solution, and people\'s choice award. Every participating team that submits a working project will also receive exclusive swag and certificates.',
                'excerpt' => 'Compete for your share of $100,000 in prizes at Hackathon 2024!',
                'category' => 'announcement',
                'author_id' => $author->id,
                'hackathon_edition_id' => $currentEdition->id,
                'is_published' => true,
                'is_featured' => true,
                'published_at' => Carbon::now()->subDays(12),
                'views' => 3421,
                'tags' => json_encode(['prizes', 'rewards', 'competition'])
            ],
            [
                'title' => 'Team Formation Mixer Event - July 10th',
                'slug' => 'team-formation-mixer',
                'content' => 'Don\'t have a team yet? Join us for our Team Formation Mixer on July 10th! This networking event is designed to help solo participants find teammates with complementary skills. We\'ll have structured ice-breakers, skill-matching activities, and informal networking time. Light refreshments will be served. The event will be held both in-person at the Innovation Hub and virtually via our Discord server.',
                'excerpt' => 'Find your perfect team at our networking mixer event before the hackathon begins.',
                'category' => 'event',
                'author_id' => $author->id,
                'hackathon_edition_id' => $currentEdition->id,
                'is_published' => true,
                'is_featured' => false,
                'published_at' => Carbon::now()->subDays(10),
                'views' => 1567,
                'tags' => json_encode(['networking', 'teams', 'mixer'])
            ],
            [
                'title' => 'Deadline Reminder: Registration Closes July 10th',
                'slug' => 'registration-deadline-reminder',
                'content' => 'This is your final reminder that registration for Hackathon 2024 closes on July 10th at 11:59 PM. We\'ve already reached 80% capacity with teams from 25+ universities and companies. Don\'t miss your chance to be part of this incredible event. Make sure your team profile is complete and all members have verified their email addresses before the deadline.',
                'excerpt' => 'Last chance to register! Only a few days left before registration closes.',
                'category' => 'deadline',
                'author_id' => $author->id,
                'hackathon_edition_id' => $currentEdition->id,
                'is_published' => true,
                'is_featured' => false,
                'published_at' => Carbon::now()->subDays(7),
                'views' => 2890,
                'tags' => json_encode(['deadline', 'registration', 'reminder'])
            ],
            [
                'title' => 'Sponsor Spotlight: TechCorp\'s API Challenge',
                'slug' => 'sponsor-techcorp-api-challenge',
                'content' => 'Our platinum sponsor TechCorp is offering a special $5,000 prize for the best use of their new AI Vision API! They\'ll also provide free API credits for all participants and dedicated technical support during the hackathon. Join their pre-hackathon webinar on July 12th to learn about the API capabilities and get implementation tips from their engineering team.',
                'excerpt' => 'TechCorp announces special prize for best use of their AI Vision API.',
                'category' => 'sponsor',
                'author_id' => $author->id,
                'hackathon_edition_id' => $currentEdition->id,
                'is_published' => true,
                'is_featured' => false,
                'published_at' => Carbon::now()->subDays(5),
                'views' => 1234,
                'tags' => json_encode(['sponsor', 'api', 'challenge', 'techcorp'])
            ],
            [
                'title' => 'Hackathon Survival Guide: Tips from Past Winners',
                'slug' => 'hackathon-survival-guide',
                'content' => 'We interviewed last year\'s winning teams to compile the ultimate hackathon survival guide! Key tips include: plan your project scope realistically, assign clear roles to team members, take regular breaks, prepare your pitch early, and always have a working demo. The guide also covers time management strategies, debugging tips, and how to handle last-minute pivots. Download the full PDF guide from our resources section.',
                'excerpt' => 'Learn from past winners with our comprehensive hackathon survival guide.',
                'category' => 'guide',
                'author_id' => $author->id,
                'hackathon_edition_id' => $currentEdition->id,
                'is_published' => true,
                'is_featured' => false,
                'published_at' => Carbon::now()->subDays(3),
                'views' => 4567,
                'tags' => json_encode(['tips', 'guide', 'strategy', 'winners'])
            ],
            [
                'title' => 'Live Streaming Schedule for Remote Participants',
                'slug' => 'live-streaming-schedule',
                'content' => 'For our remote participants, we\'ve arranged comprehensive live streaming coverage of all major events! The opening ceremony, all workshops, mentorship sessions, and the final presentations will be streamed on our YouTube channel and Twitch. Interactive Q&A will be available through our Discord server. Schedule and streaming links are now available on the website.',
                'excerpt' => 'Stay connected with live streaming options for all remote participants.',
                'category' => 'announcement',
                'author_id' => $author->id,
                'hackathon_edition_id' => $currentEdition->id,
                'is_published' => true,
                'is_featured' => false,
                'published_at' => Carbon::now()->subDays(2),
                'views' => 1890,
                'tags' => json_encode(['streaming', 'remote', 'virtual', 'schedule'])
            ],
            [
                'title' => 'Mentorship Program: Connect with Industry Experts',
                'slug' => 'mentorship-program-announcement',
                'content' => 'We\'re excited to announce our mentorship program featuring 50+ industry experts! Mentors from leading tech companies will be available throughout the hackathon to provide technical guidance, business advice, and project feedback. Book 30-minute sessions through our mentor matching platform. Topics covered include cloud architecture, mobile development, data science, product design, and business strategy.',
                'excerpt' => 'Get guidance from 50+ industry experts through our mentorship program.',
                'category' => 'announcement',
                'author_id' => $author->id,
                'hackathon_edition_id' => $currentEdition->id,
                'is_published' => true,
                'is_featured' => false,
                'published_at' => Carbon::now()->subDays(1),
                'views' => 2345,
                'tags' => json_encode(['mentorship', 'experts', 'guidance', 'support'])
            ]
        ];

        foreach ($newsItems as $news) {
            News::create($news);
        }
    }
}
