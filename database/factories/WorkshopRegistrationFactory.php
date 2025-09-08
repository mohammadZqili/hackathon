<?php

namespace Database\Factories;

use App\Models\WorkshopRegistration;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class WorkshopRegistrationFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = WorkshopRegistration::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $registeredAt = $this->faker->dateTimeBetween('-1 month', 'now');
        $status = $this->faker->randomElement(['registered', 'confirmed', 'cancelled', 'attended', 'no_show']);
        $confirmedAt = null;
        $attendedAt = null;
        $attendanceMethod = null;
        $markedBy = null;
        $notes = null;
        $additionalData = null;

        if (in_array($status, ['confirmed', 'attended'])) {
            $confirmedAt = $this->faker->dateTimeBetween($registeredAt, 'now');
        }

        if ($status === 'attended') {
            $attendedAt = $this->faker->dateTimeBetween($confirmedAt ?? $registeredAt, 'now');
            $attendanceMethod = $this->faker->randomElement(['barcode_scan', 'manual']);
            $markedBy = \App\Models\User::factory();
        }

        if ($status === 'cancelled') {
            $notes = $this->faker->sentence();
        }

        return [
            'workshop_id' => \App\Models\Workshop::factory(),
            'user_id' => \App\Models\User::factory(),
            'barcode' => Str::random(10),
            'status' => $status,
            'registered_at' => $registeredAt,
            'confirmed_at' => $confirmedAt,
            'attended_at' => $attendedAt,
            'attendance_method' => $attendanceMethod,
            'marked_by' => $markedBy,
            'notes' => $notes,
            'additional_data' => json_encode(['source' => $this->faker->word()]),
        ];
    }
}