<?php

namespace App\Repositories\Contracts;

interface NewsRepositoryInterface extends BaseRepositoryInterface
{
    /**
     * Get news by hackathon ID
     */
    public function getByHackathon(int $hackathonId, array $filters = []);

    /**
     * Get published news
     */
    public function getPublished(array $filters = []);

    /**
     * Get news by category
     */
    public function getByCategory(string $category, array $filters = []);

    /**
     * Get news by author
     */
    public function getByAuthor(int $authorId, array $filters = []);

    /**
     * Get recent news
     */
    public function getRecent(int $limit = 10);

    /**
     * Get news for Twitter posting
     */
    public function getForTwitter(array $filters = []);

    /**
     * Mark news as posted on Twitter
     */
    public function markAsPostedOnTwitter(int $newsId, array $twitterData = []);

    /**
     * Get news statistics
     */
    public function getStatistics();

    /**
     * Search news
     */
    public function search(string $query, array $filters = []);

    /**
     * Get featured news
     */
    public function getFeatured(array $filters = []);

    /**
     * Toggle news featured status
     */
    public function toggleFeatured(int $newsId);

    /**
     * Publish/unpublish news
     */
    public function togglePublished(int $newsId);
}
