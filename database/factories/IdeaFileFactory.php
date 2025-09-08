<?php

namespace Database\Factories;

use App\Models\IdeaFile;
use Illuminate\Database\Eloquent\Factories\Factory;

class IdeaFileFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = IdeaFile::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $fileName = $this->faker->word();
        $fileExtension = $this->faker->fileExtension();
        $mimeType = $this->faker->mimeType();
        $fileCategory = $this->faker->randomElement(['presentation', 'document', 'image', 'video', 'other']);

        return [
            'idea_id' => \App\Models\Idea::factory(),
            'original_name' => $fileName . '.' . $fileExtension,
            'file_name' => $this->faker->uuid() . '.' . $fileExtension,
            'file_path' => 'idea_files/' . $this->faker->uuid() . '.' . $fileExtension,
            'file_type' => $fileExtension,
            'mime_type' => $mimeType,
            'file_size' => $this->faker->numberBetween(1024, 5 * 1024 * 1024), // in bytes (1KB to 5MB)
            'file_category' => $fileCategory,
            'description' => $this->faker->sentence(),
            'uploaded_by' => \App\Models\User::factory(),
            'is_virus_scanned' => $this->faker->boolean(),
            'is_safe' => $this->faker->boolean(),
        ];
    }
}