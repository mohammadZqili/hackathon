<?php

namespace Database\Factories;

use App\Models\Hackathon;
use Illuminate\Database\Eloquent\Factories\Factory;

class HackathonFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Hackathon::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $registrationStartDate = $this->faker->dateTimeBetween('now', '+1 month');
        $registrationEndDate = $this->faker->dateTimeBetween($registrationStartDate, (clone $registrationStartDate)->modify('+2 weeks'));
        $ideaSubmissionStartDate = $this->faker->dateTimeBetween($registrationEndDate, (clone $registrationEndDate)->modify('+1 week'));
        $ideaSubmissionEndDate = $this->faker->dateTimeBetween($ideaSubmissionStartDate, (clone $ideaSubmissionStartDate)->modify('+2 weeks'));
        $eventStartDate = $this->faker->dateTimeBetween($ideaSubmissionEndDate, (clone $ideaSubmissionEndDate)->modify('+1 week'));
        $eventEndDate = $this->faker->dateTimeBetween($eventStartDate, (clone $eventStartDate)->modify('+3 days'));

        return [
            'name' => $this->faker->sentence(3),
            'description' => $this->faker->paragraph(),
            'theme' => $this->faker->word() . ' Theme',
            'year' => $this->faker->year(),
            'registration_start_date' => $registrationStartDate,
            'registration_end_date' => $registrationEndDate,
            'idea_submission_start_date' => $ideaSubmissionStartDate,
            'idea_submission_end_date' => $ideaSubmissionEndDate,
            'event_start_date' => $eventStartDate,
            'event_end_date' => $eventEndDate,
            'location' => $this->faker->city(),
            'is_active' => $this->faker->boolean(),
            'is_current' => $this->faker->boolean(),
            'settings' => json_encode(['key' => $this->faker->word(), 'value' => $this->faker->sentence()]),
            'created_by' => \App\Models\User::factory(), // Assuming UserFactory exists
        ];
    }
}