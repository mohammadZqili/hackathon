<?php

namespace App\Repositories;

use App\Models\News;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;

class NewsRepository extends BaseRepository
{
    public function __construct(News $model)
    {
        parent::__construct($model);
    }

    /**
     * Get articles for media center
     */
    public function getArticlesForMediaCenter()
    {
        return $this->model->select('id', 'title')
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Get news with media
     */
    public function getNewsWithMedia()
    {
        return $this->model->whereNotNull('featured_image_path')
            ->orWhereNotNull('seo_data->gallery_images')
            ->get();
    }

    /**
     * Find news or fail
     */
    public function findOrFail(string $id, array $columns = ['*']): Model
    {
        return $this->model->select($columns)->findOrFail($id);
    }

    /**
     * Get paginated news with filters (for consistency with other repositories)
     */
    public function getPaginatedWithFilters(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = $this->model->with(['author']);

        // Apply search filter
        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('excerpt', 'like', "%{$search}%")
                  ->orWhere('content', 'like', "%{$search}%");
            });
        }

        // Apply status filter
        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        // Apply featured filter
        if (isset($filters['featured'])) {
            $query->where('is_featured', $filters['featured']);
        }

        // Apply hackathon filter
        if (!empty($filters['hackathon_id'])) {
            $query->where('hackathon_id', $filters['hackathon_id']);
        }

        // Apply author filter
        if (!empty($filters['author_id'])) {
            $query->where('author_id', $filters['author_id']);
        }

        // Apply tags filter (using tags instead of category)
        if (!empty($filters['tags'])) {
            $query->whereJsonContains('tags', $filters['tags']);
        }

        // Apply date range filter
        if (!empty($filters['date_from'])) {
            $query->where('published_at', '>=', $filters['date_from']);
        }

        if (!empty($filters['date_to'])) {
            $query->where('published_at', '<=', $filters['date_to']);
        }

        // Apply sorting
        $sortBy = $filters['sort_by'] ?? 'published_at';
        $sortDirection = $filters['sort_direction'] ?? 'desc';
        $query->orderBy($sortBy, $sortDirection);

        return $query->paginate($perPage);
    }

    /**
     * Get news articles with optional filters
     */
    public function getNews(array $filters = []): array
    {
        $query = $this->model->with(['author']);

        // Apply search filter
        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('excerpt', 'like', "%{$search}%")
                  ->orWhere('content', 'like', "%{$search}%");
            });
        }

        // Apply status filter
        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        // Apply featured filter
        if (isset($filters['featured'])) {
            $query->where('is_featured', $filters['featured']);
        }

        // Apply hackathon filter
        if (!empty($filters['hackathon_id'])) {
            $query->where('hackathon_id', $filters['hackathon_id']);
        }

        // Apply author filter
        if (!empty($filters['author_id'])) {
            $query->where('author_id', $filters['author_id']);
        }

        // Apply tags filter (using tags instead of category)
        if (!empty($filters['tags'])) {
            $query->whereJsonContains('tags', $filters['tags']);
        }

        // Apply date range filter
        if (!empty($filters['date_from'])) {
            $query->where('published_at', '>=', $filters['date_from']);
        }

        if (!empty($filters['date_to'])) {
            $query->where('published_at', '<=', $filters['date_to']);
        }

        // Apply sorting
        $sortBy = $filters['sort_by'] ?? 'published_at';
        $sortDirection = $filters['sort_direction'] ?? 'desc';
        $query->orderBy($sortBy, $sortDirection);

        // Apply limit if specified
        if (!empty($filters['limit'])) {
            $query->limit($filters['limit']);
        }

        // Check if pagination is required
        if (isset($filters['paginate']) && $filters['paginate']) {
            $perPage = $filters['per_page'] ?? 15;
            $result = $query->paginate($perPage);

            return [
                'data' => $result->items(),
                'meta' => [
                    'current_page' => $result->currentPage(),
                    'last_page' => $result->lastPage(),
                    'per_page' => $result->perPage(),
                    'total' => $result->total(),
                    'from' => $result->firstItem(),
                    'to' => $result->lastItem(),
                ]
            ];
        }

        return [
            'data' => $query->get(),
            'meta' => null
        ];
    }

    /**
     * Find news article by ID
     */
    public function findById(string $newsId): ?News
    {
        return $this->model->with(['author'])->find($newsId);
    }

    /**
     * Find news with full details
     */
    public function findWithFullDetails(string $newsId): ?News
    {
        return $this->model->with(['author'])->find($newsId);
    }

    /**
     * Create a new news article
     */
    public function create(array $newsData): News
    {
        return $this->model->create($newsData);
    }

    /**
     * Update an existing news article and return the updated model
     */
    public function updateAndReturn(string $newsId, array $newsData): News
    {
        $news = $this->model->findOrFail($newsId);
        $news->update($newsData);
        return $news->fresh(['author']);
    }

    /**
     * Delete a news article
     */
    public function delete(string $newsId): bool
    {
        $news = $this->model->findOrFail($newsId);
        return $news->delete();
    }

    /**
     * Get news statistics
     */
    public function getStatistics(array $filters = []): array
    {
        $query = $this->model;

        // Apply hackathon filter
        if (!empty($filters['hackathon_id'])) {
            $query->where('hackathon_id', $filters['hackathon_id']);
        }

        $totalNews = $query->count();
        $publishedNews = $query->where('status', 'published')->count();
        $draftNews = $query->where('status', 'draft')->count();
        $featuredNews = $query->where('is_featured', true)->count();

        // Get news by tags (instead of category)
        $newsByTags = [];

        // Get latest news
        $latestNews = $this->model
            ->where('status', 'published')
            ->orderBy('published_at', 'desc')
            ->limit(5)
            ->get();

        // Get most viewed news
        $mostViewedNews = $this->model
            ->where('status', 'published')
            ->orderBy('views_count', 'desc')
            ->limit(5)
            ->get();

        return [
            'total_news' => $totalNews,
            'published_news' => $publishedNews,
            'draft_news' => $draftNews,
            'featured_news' => $featuredNews,
            'news_by_tags' => $newsByTags,
            'latest_news' => $latestNews,
            'most_viewed_news' => $mostViewedNews,
        ];
    }

    /**
     * Get news tags
     */
    public function getTags(): Collection
    {
        return $this->model
            ->whereNotNull('tags')
            ->get()
            ->pluck('tags')
            ->flatten()
            ->unique()
            ->filter()
            ->values();
    }

    /**
     * Increment view count
     */
    public function incrementViews(string $newsId): bool
    {
        $news = $this->model->find($newsId);
        if ($news) {
            $news->increment('views_count');
            return true;
        }
        return false;
    }

    /**
     * Get related news articles
     */
    public function getRelatedNews(string $newsId, int $limit = 3): Collection
    {
        $currentNews = $this->model->find($newsId);

        if (!$currentNews) {
            return collect();
        }

        return $this->model
            ->where('id', '!=', $newsId)
            ->where('status', 'published')
            ->where('author_id', $currentNews->author_id)
            ->orderBy('published_at', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Search news articles
     */
    public function searchNews(string $searchTerm, array $filters = []): array
    {
        $filters['search'] = $searchTerm;
        return $this->getNews($filters);
    }

    /**
     * Get published news with filters and pagination.
     */
    public function getPublished(array $filters = [], int $perPage = 12): array
    {
        $query = $this->model->where('status', 'published')
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now())
            ->with(['author']);

        // Apply search
        if (!empty($filters['search'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('title', 'like', "%{$filters['search']}%")
                  ->orWhere('excerpt', 'like', "%{$filters['search']}%")
                  ->orWhere('content', 'like', "%{$filters['search']}%");
            });
        }

        // Filter by tag
        if (!empty($filters['tag'])) {
            $query->whereJsonContains('tags', $filters['tag']);
        }

        $news = $query->orderBy('is_featured', 'desc')
            ->orderBy('published_at', 'desc')
            ->paginate($perPage);

        return [
            'data' => $news->items(),
            'meta' => [
                'current_page' => $news->currentPage(),
                'last_page' => $news->lastPage(),
                'per_page' => $news->perPage(),
                'total' => $news->total(),
                'from' => $news->firstItem(),
                'to' => $news->lastItem(),
            ]
        ];
    }

    /**
     * Find news by slug.
     */
    public function findBySlug(string $slug): ?News
    {
        return $this->model->where('slug', $slug)->first();
    }

    /**
     * Get featured news article.
     */
    public function getFeatured(): ?News
    {
        return $this->model->where('status', 'published')
            ->where('is_featured', true)
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now())
            ->with(['author'])
            ->orderBy('published_at', 'desc')
            ->first();
    }

    /**
     * Get recent news articles.
     */
    public function getRecent(int $limit = 5, ?int $excludeId = null): Collection
    {
        $query = $this->model->where('status', 'published')
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now());

        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        return $query->orderBy('published_at', 'desc')
            ->take($limit)
            ->get(['id', 'slug', 'title', 'published_at']);
    }

    /**
     * Get all available tags.
     */
    public function getAllTags()
    {
        return $this->model->where('status', 'published')
            ->whereNotNull('tags')
            ->get()
            ->pluck('tags')
            ->flatten()
            ->unique()
            ->sort()
            ->values();
    }

    /**
     * Get related news by tags.
     */
    public function getRelatedByTags(News $news, int $limit = 3): Collection
    {
        return $this->model->where('status', 'published')
            ->where('id', '!=', $news->id)
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now())
            ->where(function ($query) use ($news) {
                foreach ($news->tags as $tag) {
                    $query->orWhereJsonContains('tags', $tag);
                }
            })
            ->orderBy('published_at', 'desc')
            ->take($limit)
            ->get(['id', 'slug', 'title', 'excerpt', 'published_at', 'featured_image_path']);
    }

    /**
     * Get previous article.
     */
    public function getPreviousArticle(News $news): ?News
    {
        return $this->model->where('status', 'published')
            ->where('published_at', '<', $news->published_at)
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now())
            ->orderBy('published_at', 'desc')
            ->first(['id', 'slug', 'title']);
    }

    /**
     * Get next article.
     */
    public function getNextArticle(News $news): ?News
    {
        return $this->model->where('status', 'published')
            ->where('published_at', '>', $news->published_at)
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now())
            ->orderBy('published_at', 'asc')
            ->first(['id', 'slug', 'title']);
    }

    /**
     * Get total views count.
     */
    public function getTotalViews(): int
    {
        return $this->model->sum('views_count');
    }

    /**
     * Count records with filters.
     */
    public function count(array $filters = []): int
    {
        $query = $this->model->query();

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['is_featured'])) {
            $query->where('is_featured', $filters['is_featured']);
        }

        if (!empty($filters['published_after'])) {
            $query->where('published_at', '>=', $filters['published_after']);
        }

        return $query->count();
    }
}
