<?php

namespace App\Services;

use App\Models\News;
use App\Repositories\NewsRepository;
use App\Services\Contracts\NewsServiceInterface;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class NewsService implements NewsServiceInterface
{
    public function __construct(
        private NewsRepository $newsRepo
    ) {}

    /**
     * Get published news with filters and pagination.
     */
    public function getPublishedNews(array $filters = [], int $perPage = 12): array
    {
        $cacheKey = 'published_news_' . md5(serialize($filters) . $perPage);
        
        return Cache::remember($cacheKey, 300, function () use ($filters, $perPage) {
            return $this->newsRepo->getPublished($filters, $perPage);
        });
    }

    /**
     * Get news article by slug with related data.
     */
    public function getNewsBySlug(string $slug): array
    {
        $news = $this->newsRepo->findBySlug($slug);
        
        if (!$news) {
            throw new \Exception('المقال غير موجود');
        }

        // Check if article is published and not in future
        if ($news->status !== 'published' || 
            !$news->published_at || 
            $news->published_at > now()) {
            throw new \Exception('المقال غير متاح');
        }

        // Load author
        $news->load(['author']);

        // Get related and navigation articles
        $relatedNews = $this->getRelatedNews($news);
        $navigationArticles = $this->getNavigationArticles($news);

        return [
            'article' => $news,
            'relatedNews' => $relatedNews,
            'previousArticle' => $navigationArticles['previous'],
            'nextArticle' => $navigationArticles['next'],
            'meta' => [
                'title' => $news->title,
                'description' => $news->excerpt ?: substr(strip_tags($news->content), 0, 160),
                'image' => $news->featured_image_path,
                'published_at' => $news->published_at,
                'author' => $news->author->name,
            ],
        ];
    }

    /**
     * Get featured news article.
     */
    public function getFeaturedNews(): ?News
    {
        return Cache::remember('featured_news', 300, function () {
            return $this->newsRepo->getFeatured();
        });
    }

    /**
     * Get recent news articles.
     */
    public function getRecentNews(int $limit = 5, ?int $excludeId = null): Collection
    {
        $cacheKey = "recent_news_{$limit}_" . ($excludeId ?: 'none');
        
        return Cache::remember($cacheKey, 300, function () use ($limit, $excludeId) {
            return $this->newsRepo->getRecent($limit, $excludeId);
        });
    }

    /**
     * Get all available tags.
     */
    public function getAvailableTags(): Collection
    {
        return Cache::remember('news_tags', 600, function () {
            return $this->newsRepo->getAllTags();
        });
    }

    /**
     * Get related news articles based on tags.
     */
    public function getRelatedNews(News $news, int $limit = 3): Collection
    {
        if (!$news->tags || count($news->tags) === 0) {
            return collect();
        }

        $cacheKey = "related_news_{$news->id}_{$limit}";
        
        return Cache::remember($cacheKey, 300, function () use ($news, $limit) {
            return $this->newsRepo->getRelatedByTags($news, $limit);
        });
    }

    /**
     * Get navigation articles (previous/next).
     */
    public function getNavigationArticles(News $news): array
    {
        $cacheKey = "navigation_articles_{$news->id}";
        
        return Cache::remember($cacheKey, 300, function () use ($news) {
            return [
                'previous' => $this->newsRepo->getPreviousArticle($news),
                'next' => $this->newsRepo->getNextArticle($news),
            ];
        });
    }

    /**
     * Increment views count for article.
     */
    public function incrementViews(News $news): void
    {
        // Use repository to increment views
        $this->newsRepo->incrementViews($news->id);
        
        // Clear related caches
        Cache::forget('news_statistics');
    }

    /**
     * Get news statistics.
     */
    public function getNewsStatistics(): array
    {
        return Cache::remember('news_statistics', 600, function () {
            return [
                'total' => $this->newsRepo->count(),
                'published' => $this->newsRepo->count(['status' => 'published']),
                'draft' => $this->newsRepo->count(['status' => 'draft']),
                'archived' => $this->newsRepo->count(['status' => 'archived']),
                'featured' => $this->newsRepo->count(['is_featured' => true, 'status' => 'published']),
                'total_views' => $this->newsRepo->getTotalViews(),
                'recent_published' => $this->newsRepo->count([
                    'status' => 'published',
                    'published_after' => now()->subDays(7),
                ]),
            ];
        });
    }
}