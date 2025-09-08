<?php

namespace Database\Factories;

use App\Models\TeamInvitation;
use Illuminate\Database\Eloquent\Factories\Factory;

class TeamInvitationFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = TeamInvitation::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'team_id' => \App\Models\Team::factory(), // Assuming TeamFactory exists
            'email' => 'invite.' . $this->faker->firstName() . '.' . $this->faker->randomNumber(4) . '@' . $this->faker->domainName(),
            'role' => $this->faker->randomElement(['member', 'admin']),
        ];
    }
}
