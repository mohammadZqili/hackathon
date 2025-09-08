<?php

namespace Database\Factories;

use App\Models\IdeaAuditLog;
use Illuminate\Database\Eloquent\Factories\Factory;

class IdeaAuditLogFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = IdeaAuditLog::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $action = $this->faker->randomElement(['created', 'updated', 'status_changed', 'file_added', 'file_removed']);
        $fieldName = null;
        $oldValue = null;
        $newValue = null;
        $notes = $this->faker->sentence();
        $metadata = null;

        if ($action === 'updated' || $action === 'status_changed') {
            $fieldName = $this->faker->randomElement(['title', 'description', 'status', 'track_id']);
            $oldValue = $this->faker->word();
            $newValue = $this->faker->word();
        } elseif ($action === 'file_added' || $action === 'file_removed') {
            $metadata = json_encode(['file_name' => $this->faker->word() . '.pdf']);
        }

        return [
            'idea_id' => \App\Models\Idea::factory(),
            'user_id' => \App\Models\User::factory(),
            'action' => $action,
            'field_name' => $fieldName,
            'old_value' => $oldValue,
            'new_value' => $newValue,
            'notes' => $notes,
            'metadata' => $metadata,
            'ip_address' => $this->faker->ipv4(),
            'user_agent' => $this->faker->userAgent(),
        ];
    }
}