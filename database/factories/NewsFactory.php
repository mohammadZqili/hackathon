<?php

namespace Database\Factories;

use App\Models\News;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class NewsFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = News::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $title = $this->faker->sentence(5);
        $status = $this->faker->randomElement(['draft', 'published', 'archived']);
        $publishedAt = ($status === 'published') ? $this->faker->dateTimeBetween('-1 month', 'now') : null;
        $autoPostTwitter = $this->faker->boolean();
        $postedToTwitter = $autoPostTwitter ? $this->faker->boolean(70) : false; // 70% chance if auto_post is true
        $twitterPostId = $postedToTwitter ? $this->faker->uuid() : null;
        $twitterPostedAt = $postedToTwitter ? $this->faker->dateTimeBetween($publishedAt ?? '-1 week', 'now') : null;

        return [
            'title' => $title,
            'slug' => Str::slug($title) . '-' . $this->faker->uuid(),
            'excerpt' => $this->faker->paragraph(2),
            'content' => $this->faker->paragraphs(5, true),
            'featured_image_path' => 'news_images/' . $this->faker->uuid() . '.jpg',
            'status' => $status,
            'is_featured' => $this->faker->boolean(),
            'published_at' => $publishedAt,
            'author_id' => \App\Models\User::factory(),
            'tags' => json_encode($this->faker->words(3)),
            'views_count' => $this->faker->numberBetween(0, 1000),
            'auto_post_twitter' => $autoPostTwitter,
            'posted_to_twitter' => $postedToTwitter,
            'twitter_post_id' => $twitterPostId,
            'twitter_posted_at' => $twitterPostedAt,
            'seo_data' => json_encode([
                'meta_title' => $this->faker->sentence(6),
                'meta_description' => $this->faker->paragraph(2),
            ]),
        ];
    }
}