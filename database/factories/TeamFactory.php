<?php

namespace Database\Factories;

use App\Models\Hackathon;
use App\Models\Track;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Team>
 */
class TeamFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // Generate team names without exhausting unique combinations
        $teamPrefixes = [
            'Alpha', 'Beta', 'Gamma', 'Delta', 'Epsilon', 'Zeta', 'Theta', 'Lambda',
            'Phoenix', 'Titan', 'Nova', 'Apex', 'Vector', 'Matrix', 'Nexus', 'Vertex',
            'Quantum', 'Cosmic', 'Digital', 'Cyber', 'Neural', 'Binary', 'Logic', 'Code',
            'Storm', 'Lightning', 'Thunder', 'Blaze', 'Flash', 'Spark', 'Flame', 'Forge',
            'Pioneer', 'Explorer', 'Voyager', 'Navigator', 'Ranger', 'Scout', 'Hunter', 'Seeker',
            'Elite', 'Prime', 'Ultra', 'Mega', 'Super', 'Hyper', 'Turbo', 'Max',
            'Iron', 'Steel', 'Chrome', 'Silver', 'Gold', 'Platinum', 'Diamond', 'Crystal'
        ];
        
        $teamSuffixes = [
            'Squad', 'Team', 'Crew', 'Force', 'Unit', 'Group', 'Guild', 'Alliance',
            'Labs', 'Works', 'Tech', 'Systems', 'Solutions', 'Innovations', 'Dynamics',
            'Collective', 'Assembly', 'Coalition', 'Federation', 'Consortium', 'Network',
            'Hub', 'Core', 'Base', 'Station', 'Center', 'Zone', 'Sector', 'Division'
        ];

        $name = $this->faker->randomElement($teamPrefixes) . ' ' . $this->faker->randomElement($teamSuffixes);
        
        // Add random number if needed for additional uniqueness
        if ($this->faker->boolean(30)) { // 30% chance to add number
            $name .= ' ' . $this->faker->numberBetween(1, 999);
        }
        
        return [
            'name' => $name,
            'slug' => Str::slug($name),
            'description' => $this->faker->paragraph(3),
            'user_id' => User::factory(), // Keep this for Jetstream compatibility
            'personal_team' => false, // Most teams won't be personal teams
            'hackathon_id' => Hackathon::factory(),
            'leader_id' => User::factory(),
            'track_id' => Track::factory(),
            'invite_code' => strtoupper(Str::random(8)),
            'max_members' => $this->faker->numberBetween(3, 8),
            'status' => $this->faker->randomElement(['draft', 'active', 'submitted']),
            'submitted_at' => function (array $attributes) {
                return $attributes['status'] === 'submitted' ? $this->faker->dateTimeBetween('-1 month', 'now') : null;
            },
        ];
    }

    /**
     * Indicate that the team is a personal team.
     */
    public function personal(): static
    {
        return $this->state(fn (array $attributes) => [
            'personal_team' => true,
            'hackathon_id' => null,
            'track_id' => null,
            'invite_code' => null,
            'description' => null,
            'status' => 'active',
        ]);
    }

    /**
     * Indicate that the team has submitted their idea.
     */
    public function submitted(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'submitted',
            'submitted_at' => $this->faker->dateTimeBetween('-1 month', 'now'),
        ]);
    }

    /**
     * Indicate that the team is accepted.
     */
    public function accepted(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'accepted',
            'submitted_at' => $this->faker->dateTimeBetween('-2 months', '-1 month'),
        ]);
    }

    /**
     * Indicate that the team is in draft status.
     */
    public function draft(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'draft',
            'submitted_at' => null,
        ]);
    }

    /**
     * Configure the team to belong to a specific hackathon.
     */
    public function forHackathon(Hackathon $hackathon): static
    {
        return $this->state(fn (array $attributes) => [
            'hackathon_id' => $hackathon->id,
            'track_id' => Track::factory()->for($hackathon),
        ]);
    }

    /**
     * Configure the team with a specific leader.
     */
    public function withLeader(User $leader): static
    {
        return $this->state(fn (array $attributes) => [
            'leader_id' => $leader->id,
            'user_id' => $leader->id, // Keep Jetstream compatibility
        ]);
    }
}
