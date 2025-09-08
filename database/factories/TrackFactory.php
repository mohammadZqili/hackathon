<?php

namespace Database\Factories;

use App\Models\Track;
use Illuminate\Database\Eloquent\Factories\Factory;

class TrackFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Track::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // Generate unique track names without exhausting faker's unique generator
        $trackTypes = [
            'AI & Machine Learning',
            'Web Development',
            'Mobile Apps',
            'IoT & Hardware',
            'Blockchain',
            'Data Science',
            'Cybersecurity',
            'Gaming',
            'HealthTech',
            'EdTech',
            'FinTech',
            'GreenTech',
            'Social Impact',
            'Enterprise Solutions',
            'DevOps & Cloud',
            'AR/VR',
            'Robotics',
            'Open Source',
            'API & Integration',
            'Digital Marketing',
        ];

        $trackName = $this->faker->randomElement($trackTypes);
        // Add random suffix to ensure uniqueness without using faker->unique()
        $trackName .= ' ' . $this->faker->randomElement(['Track', 'Challenge', 'Competition', 'Stream']);
        
        return [
            'hackathon_id' => \App\Models\Hackathon::factory(),
            'name' => $trackName,
            'description' => $this->faker->paragraph(3),
            'icon' => 'track_icons/' . $this->faker->uuid() . '.svg',
            'color' => $this->faker->hexColor(),
            'max_teams' => $this->faker->numberBetween(10, 50),
            'evaluation_criteria' => [
                'innovation' => ['weight' => 30, 'description' => 'Originality and creativity of the solution'],
                'feasibility' => ['weight' => 30, 'description' => 'Technical viability and implementation quality'],
                'presentation' => ['weight' => 20, 'description' => 'Quality of demonstration and communication'],
                'impact' => ['weight' => 20, 'description' => 'Potential real-world impact and usefulness'],
            ],
            'is_active' => $this->faker->boolean(85), // 85% chance of being active
            'sort_order' => $this->faker->numberBetween(1, 20),
        ];
    }

    /**
     * Indicate that the track is active.
     */
    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => true,
        ]);
    }

    /**
     * Indicate that the track is inactive.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }

    /**
     * Set a specific track name.
     */
    public function withName(string $name): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => $name,
        ]);
    }

    /**
     * Set a specific color.
     */
    public function withColor(string $color): static
    {
        return $this->state(fn (array $attributes) => [
            'color' => $color,
        ]);
    }

    /**
     * Create a track with unlimited teams.
     */
    public function unlimited(): static
    {
        return $this->state(fn (array $attributes) => [
            'max_teams' => null,
        ]);
    }
}
