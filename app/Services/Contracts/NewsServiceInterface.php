<?php

namespace App\Services\Contracts;

use App\Models\News;
use Illuminate\Support\Collection;

interface NewsServiceInterface
{
    /**
     * Get published news with filters and pagination.
     */
    public function getPublishedNews(array $filters = [], int $perPage = 12): array;

    /**
     * Get news article by slug with related data.
     */
    public function getNewsBySlug(string $slug): array;

    /**
     * Get featured news article.
     */
    public function getFeaturedNews(): ?News;

    /**
     * Get recent news articles.
     */
    public function getRecentNews(int $limit = 5, ?int $excludeId = null): Collection;

    /**
     * Get all available tags.
     */
    public function getAvailableTags(): Collection;

    /**
     * Get related news articles based on tags.
     */
    public function getRelatedNews(News $news, int $limit = 3): Collection;

    /**
     * Get navigation articles (previous/next).
     */
    public function getNavigationArticles(News $news): array;

    /**
     * Increment views count for article.
     */
    public function incrementViews(News $news): void;

    /**
     * Get news statistics.
     */
    public function getNewsStatistics(): array;
}