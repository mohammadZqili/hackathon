<?php

namespace Database\Factories;

use App\Models\Organization;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class OrganizationFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Organization::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // Generate organization names without exhausting unique combinations
        $orgTypes = [
            'Technologies', 'Solutions', 'Systems', 'Innovations', 'Dynamics', 'Labs',
            'Works', 'Corp', 'Industries', 'Enterprises', 'Group', 'Holdings',
            'Institute', 'Foundation', 'Center', 'Hub', 'Studio', 'Agency'
        ];
        
        $orgPrefixes = [
            'Global', 'Digital', 'Smart', 'Future', 'Advanced', 'Premier', 'Elite',
            'Prime', 'Alpha', 'Beta', 'Quantum', 'Nexus', 'Matrix', 'Vector',
            'Phoenix', 'Titan', 'Nova', 'Vertex', 'Apex', 'Core', 'Peak'
        ];
        
        $orgNames = [
            'Tech', 'Data', 'Cloud', 'Cyber', 'Nano', 'Bio', 'Eco', 'Green',
            'Blue', 'Red', 'Silver', 'Gold', 'Iron', 'Steel', 'Crystal', 'Diamond'
        ];

        $prefix = $this->faker->randomElement($orgPrefixes);
        $name = $this->faker->randomElement($orgNames);
        $type = $this->faker->randomElement($orgTypes);
        
        $fullName = $prefix . ' ' . $name . ' ' . $type;
        
        // Add random number occasionally for additional uniqueness
        if ($this->faker->boolean(25)) { // 25% chance to add number
            $fullName .= ' ' . $this->faker->numberBetween(1, 100);
        }
        
        $slug = Str::slug($fullName);
        $email = strtolower(str_replace([' ', '-'], '', $prefix . $name)) . '@' . $this->faker->domainName();
        
        return [
            'name' => $fullName,
            'slug' => $slug,
            'description' => $this->faker->paragraph(2),
            'logo_path' => 'organization_logos/' . $this->faker->uuid() . '.png',
            'website' => 'https://www.' . $slug . '.com',
            'email' => $email,
            'phone' => $this->faker->phoneNumber(),
            'address' => $this->faker->address(),
            'type' => $this->faker->randomElement(['government', 'private', 'ngo', 'educational', 'other']),
            'is_active' => $this->faker->boolean(85), // 85% chance of being active
            'social_media' => [
                'twitter' => 'https://twitter.com/' . strtolower(str_replace(' ', '', $prefix . $name)),
                'linkedin' => 'https://linkedin.com/company/' . $slug,
                'facebook' => 'https://facebook.com/' . $slug,
            ],
        ];
    }

    /**
     * Indicate that the organization is active.
     */
    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => true,
        ]);
    }

    /**
     * Indicate that the organization is inactive.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }

    /**
     * Set a specific organization type.
     */
    public function ofType(string $type): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => $type,
        ]);
    }
}
