<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'remember_token' => Str::random(10),
            'phone' => fake()->phoneNumber(),
            'university' => fake()->randomElement(['MIT', 'Stanford', 'Harvard', 'Yale', 'Princeton', 'UC Berkeley', 'Carnegie Mellon']),
            'major' => fake()->randomElement(['Computer Science', 'Software Engineering', 'Data Science', 'AI/ML', 'Cybersecurity']),
            'graduation_year' => fake()->numberBetween(2024, 2027),
            'github_username' => fake()->userName(),
            'linkedin_url' => 'https://linkedin.com/in/' . fake()->userName(),
            'bio' => fake()->paragraph(),
            'skills' => json_encode(fake()->randomElements(['Python', 'JavaScript', 'React', 'Node.js', 'Java', 'C++', 'Machine Learning', 'Docker', 'Kubernetes', 'AWS'], 5)),
            'interests' => json_encode(fake()->randomElements(['AI', 'Blockchain', 'IoT', 'FinTech', 'HealthTech', 'EdTech', 'Sustainability'], 3)),
            'dietary_restrictions' => fake()->randomElement(['None', 'Vegetarian', 'Vegan', 'Gluten-free', 'Halal', 'Kosher', null]),
            'tshirt_size' => fake()->randomElement(['XS', 'S', 'M', 'L', 'XL', 'XXL']),
            'emergency_contact_name' => fake()->name(),
            'emergency_contact_phone' => fake()->phoneNumber(),
            'participated_before' => fake()->boolean(30),
            'previous_hackathons' => fake()->numberBetween(0, 10),
            'discord_username' => fake()->userName() . '#' . fake()->numberBetween(1000, 9999),
            'is_active' => true,
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }
}