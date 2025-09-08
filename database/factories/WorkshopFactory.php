<?php

namespace Database\Factories;

use App\Models\Workshop;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class WorkshopFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Workshop::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $title = $this->faker->sentence(4) . ' Workshop';
        $startTime = $this->faker->dateTimeBetween('now', '+2 weeks');
        $endTime = $this->faker->dateTimeBetween($startTime, (clone $startTime)->modify('+2 hours'));
        $requiresRegistration = $this->faker->boolean();
        $registrationDeadline = $requiresRegistration ? $this->faker->dateTimeBetween('now', $startTime) : null;

        return [
            'hackathon_id' => \App\Models\Hackathon::factory(),
            'title' => $title,
            'slug' => Str::slug($title) . '-' . $this->faker->uuid(),
            'description' => $this->faker->paragraph(3),
            'type' => $this->faker->randomElement(['workshop', 'seminar', 'lecture', 'panel']),
            'start_time' => $startTime,
            'end_time' => $endTime,
            'format' => $this->faker->randomElement(['online', 'offline', 'hybrid']),
            'location' => $this->faker->address(),
            'max_attendees' => $this->faker->numberBetween(20, 100),
            'current_attendees' => $this->faker->numberBetween(0, 20),
            'prerequisites' => $this->faker->sentence(),
            'materials' => json_encode([
                'slides' => $this->faker->url(),
                'github' => $this->faker->url(),
            ]),
            'thumbnail_path' => 'workshop_thumbnails/' . $this->faker->uuid() . '.jpg',
            'is_active' => $this->faker->boolean(),
            'requires_registration' => $requiresRegistration,
            'registration_deadline' => $registrationDeadline,
            'settings' => json_encode(['difficulty' => $this->faker->randomElement(['beginner', 'intermediate', 'advanced'])]),
        ];
    }
}