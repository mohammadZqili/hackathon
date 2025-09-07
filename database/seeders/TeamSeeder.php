<?php

namespace Database\Seeders;

use App\Models\Hackathon;
use App\Models\Team;
use App\Models\Track;
use App\Models\User;
use Illuminate\Database\Seeder;

class TeamSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get or create a current hackathon and tracks for testing
        $hackathon = Hackathon::where('is_current', true)->first() 
                    ?? Hackathon::factory()->create(['is_current' => true]);

        $tracks = $hackathon->tracks()->count() > 0 
                 ? $hackathon->tracks 
                 : Track::factory()->count(3)->for($hackathon)->create();

        // Create team leaders
        $leaders = User::factory()->count(10)->create([
            'user_type' => 'team_leader'
        ]);

        // Create teams in different statuses
        foreach ($leaders as $index => $leader) {
            $track = $tracks->random();
            
            $team = Team::factory()
                ->withLeader($leader)
                ->create([
                    'hackathon_id' => $hackathon->id,
                    'track_id' => $track->id,
                    'status' => match($index % 4) {
                        0 => 'draft',
                        1 => 'active', 
                        2 => 'submitted',
                        3 => 'accepted',
                    }
                ]);

            // Add the leader as a team member
            $team->addMember($leader, 'accepted');

            // Add random team members (1-3 additional members)
            $memberCount = fake()->numberBetween(1, 3);
            $members = User::factory()->count($memberCount)->create([
                'user_type' => 'team_member'
            ]);

            foreach ($members as $member) {
                $team->addMember($member, 'accepted');
            }
        }

        // Create some personal teams for Jetstream compatibility (if needed)
        User::factory()->count(3)->create()->each(function ($user) {
            Team::factory()->personal()->withLeader($user)->create();
        });

        $this->command->info('Created teams with different statuses for testing');
    }
}
