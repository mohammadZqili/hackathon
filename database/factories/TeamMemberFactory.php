<?php

namespace Database\Factories;

use App\Models\TeamMember;
use Illuminate\Database\Eloquent\Factories\Factory;

class TeamMemberFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = TeamMember::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $status = $this->faker->randomElement(['pending', 'accepted', 'rejected', 'removed']);
        $invitedAt = $this->faker->dateTimeBetween('-1 month', 'now');
        $joinedAt = null;
        $invitedBy = null;
        $invitationMessage = null;

        if ($status === 'accepted') {
            $joinedAt = $this->faker->dateTimeBetween($invitedAt, 'now');
        }

        if ($status === 'pending') {
            $invitedBy = \App\Models\User::factory();
            $invitationMessage = $this->faker->sentence();
        }

        return [
            'team_id' => \App\Models\Team::factory(),
            'user_id' => \App\Models\User::factory(),
            'status' => $status,
            'role' => $this->faker->randomElement(['leader', 'member']),
            'joined_at' => $joinedAt,
            'invited_at' => $invitedAt,
            'invited_by' => $invitedBy,
            'invitation_message' => $invitationMessage,
        ];
    }
}