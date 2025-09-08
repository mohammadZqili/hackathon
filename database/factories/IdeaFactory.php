<?php

namespace Database\Factories;

use App\Models\Idea;
use Illuminate\Database\Eloquent\Factories\Factory;

class IdeaFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Idea::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $status = $this->faker->randomElement(['draft', 'submitted', 'under_review', 'needs_revision', 'accepted', 'rejected']);
        $submittedAt = null;
        $reviewedBy = null;
        $reviewedAt = null;
        $score = null;
        $feedback = null;
        $evaluationScores = null;

        if (in_array($status, ['submitted', 'under_review', 'needs_revision', 'accepted', 'rejected'])) {
            $submittedAt = $this->faker->dateTimeBetween('-1 month', 'now');
        }

        if (in_array($status, ['under_review', 'needs_revision', 'accepted', 'rejected'])) {
            $reviewedBy = \App\Models\User::factory();
            $reviewedAt = $this->faker->dateTimeBetween($submittedAt ?? '-1 week', 'now');
            $score = $this->faker->randomFloat(2, 0, 100);
            $feedback = $this->faker->paragraph();
            $evaluationScores = json_encode([
                'innovation' => $this->faker->numberBetween(1, 5),
                'feasibility' => $this->faker->numberBetween(1, 5),
                'impact' => $this->faker->numberBetween(1, 5),
            ]);
        }

        return [
            'team_id' => \App\Models\Team::factory(),
            'track_id' => \App\Models\Track::factory(),
            'title' => $this->faker->sentence(4),
            'description' => $this->faker->paragraph(3),
            'problem_statement' => $this->faker->paragraph(),
            'solution_approach' => $this->faker->paragraph(),
            'expected_impact' => $this->faker->paragraph(),
            'technologies' => json_encode($this->faker->words(3)),
            'status' => $status,
            'score' => $score,
            'feedback' => $feedback,
            'reviewed_by' => $reviewedBy,
            'submitted_at' => $submittedAt,
            'reviewed_at' => $reviewedAt,
            'evaluation_scores' => $evaluationScores,
        ];
    }
}