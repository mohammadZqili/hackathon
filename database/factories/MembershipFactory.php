<?php

namespace Database\Factories;

use App\Models\Membership;
use Illuminate\Database\Eloquent\Factories\Factory;

class MembershipFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Membership::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => \App\Models\User::factory(), // Assuming UserFactory exists
            'team_id' => \App\Models\Team::factory(), // Assuming TeamFactory exists
            'role' => $this->faker->randomElement(['member', 'admin']),
        ];
    }
}
