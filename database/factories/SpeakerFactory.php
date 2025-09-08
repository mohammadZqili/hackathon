<?php

namespace Database\Factories;

use App\Models\Speaker;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class SpeakerFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Speaker::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = $this->faker->name();
        return [
            'name' => $name,
            'slug' => Str::slug($name) . '-' . $this->faker->uuid(),
            'title' => $this->faker->jobTitle(),
            'bio' => $this->faker->paragraph(3),
            'photo_path' => 'speaker_photos/' . $this->faker->uuid() . '.jpg',
            'email' => strtolower(str_replace(' ', '.', $name)) . '@' . $this->faker->domainName(),
            'phone' => $this->faker->phoneNumber(),
            'expertise_areas' => json_encode($this->faker->words(3)),
            'social_media' => json_encode([
                'linkedin' => 'https://linkedin.com/in/' . $this->faker->userName(),
                'twitter' => 'https://twitter.com/' . $this->faker->userName(),
            ]),
            'is_active' => $this->faker->boolean(),
        ];
    }
}