<?php

namespace App\Services;

use App\Models\News;
use App\Models\User;
use App\Repositories\NewsRepository;
use App\Repositories\HackathonEditionRepository;
use App\Services\Contracts\NewsServiceInterface;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class NewsService extends BaseService implements NewsServiceInterface
{
    protected HackathonEditionRepository $editionRepository;

    public function __construct(
        private NewsRepository $newsRepo,
        HackathonEditionRepository $editionRepository = null
    ) {
        if ($editionRepository) {
            $this->editionRepository = $editionRepository;
        }
    }

    /**
     * Get articles for media center
     */
    public function getArticlesForMediaCenter()
    {
        return $this->newsRepo->getArticlesForMediaCenter();
    }

    /**
     * Get news with media
     */
    public function getNewsWithMedia()
    {
        return $this->newsRepo->getNewsWithMedia();
    }

    /**
     * Find news or fail
     */
    public function findOrFail(int $id)
    {
        return $this->newsRepo->findOrFail($id);
    }

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

    /**
     * Get paginated news for admin based on user role and filters
     */
    public function getPaginatedNewsAdmin(User $user, array $filters = [], int $perPage = 15): array
    {
        // Build filters based on user role
        $roleFilters = $this->buildRoleFilters($user, $filters);

        // Get paginated news
        $news = $this->newsRepo->getPaginatedWithFilters($roleFilters, $perPage);

        // Get statistics
        $statistics = $this->newsRepo->getStatistics($roleFilters);

        // Get editions for filter dropdown if available
        $editions = $this->editionRepository ? $this->getEditionsForUser($user) : collect();

        return [
            'news' => $news,
            'statistics' => $statistics,
            'editions' => $editions,
            'filters' => $filters
        ];
    }

    /**
     * Get news details for admin
     */
    public function getNewsDetailsAdmin(int $newsId, User $user): ?array
    {
        $news = $this->newsRepo->findWithFullDetails($newsId);

        if (!$news) {
            return null;
        }

        // Check if user has access to this news
        if (!$this->userCanAccessNews($user, $news)) {
            return null;
        }

        return [
            'news' => $news,
            'permissions' => $this->getNewsPermissions($user, $news)
        ];
    }

    /**
     * Create a new news article (admin)
     */
    public function createNewsAdmin(array $data, User $user): array
    {
        // Check permissions
        if (!$this->userCanCreateNews($user)) {
            throw new \Exception('You do not have permission to create news articles.');
        }

        DB::beginTransaction();
        try {
            // Handle main image - move from temp to permanent storage
            $featuredImagePath = null;
            if (!empty($data['main_image'])) {
                $featuredImagePath = $this->moveImageFromTemp($data['main_image'], 'news/featured/');
            }

            // Handle gallery images
            $galleryImages = [];
            if (!empty($data['gallery_images'])) {
                foreach ($data['gallery_images'] as $tempPath) {
                    $galleryImages[] = $this->moveImageFromTemp($tempPath, 'news/gallery/');
                }
            }

            // Process keywords into tags array
            $tags = [];
            if (!empty($data['keywords'])) {
                $tags = array_map('trim', explode(',', $data['keywords']));
            }

            // Prepare news data
            $newsData = [
                'title' => $data['title'],
                'slug' => Str::slug($data['title']),
                'content' => $data['content'],
                'excerpt' => Str::limit(strip_tags($data['content']), 200),
                'featured_image_path' => $featuredImagePath,
                'status' => 'draft',
                'author_id' => $user->id,
                'tags' => $tags,
                'auto_post_twitter' => $data['publish_to_twitter'] ?? false,
                'seo_data' => [
                    'keywords' => $data['keywords'] ?? '',
                    'video_url' => $data['video_url'] ?? '',
                    'twitter_message' => $data['twitter_message'] ?? '',
                    'gallery_images' => $galleryImages
                ]
            ];

            // Add edition_id for hackathon admin
            if ($user->user_type === 'hackathon_admin' && $user->edition_id) {
                $newsData['edition_id'] = $user->edition_id;
            }

            // Create news
            $news = $this->newsRepo->create($newsData);

            // Log activity
            Log::info('News created', [
                'news_id' => $news->id,
                'user_id' => $user->id,
                'title' => $data['title']
            ]);

            DB::commit();

            return [
                'success' => true,
                'news' => $news,
                'message' => 'News article created successfully.'
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to create news', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
                'data' => $data
            ]);
            throw $e;
        }
    }

    /**
     * Update a news article (admin)
     */
    public function updateNewsAdmin(int $newsId, array $data, User $user): array
    {
        $news = $this->newsRepo->find($newsId);

        if (!$news) {
            throw new \Exception('News article not found.');
        }

        // Check permissions
        if (!$this->userCanEditNews($user, $news)) {
            throw new \Exception('You do not have permission to edit this news article.');
        }

        DB::beginTransaction();
        try {
            // Handle main image
            $featuredImagePath = $news->featured_image_path;
            if (!empty($data['main_image'])) {
                // Check if it's a new temp image (starts with 'temp/')
                if (Str::startsWith($data['main_image'], 'temp/')) {
                    // Delete old image if exists
                    if ($featuredImagePath && Storage::disk('public')->exists($featuredImagePath)) {
                        Storage::disk('public')->delete($featuredImagePath);
                    }
                    $featuredImagePath = $this->moveImageFromTemp($data['main_image'], 'news/featured/');
                }
                // Otherwise keep the existing path
            }

            // Handle gallery images
            $galleryImages = [];

            if (!empty($data['gallery_images'])) {
                foreach ($data['gallery_images'] as $imagePath) {
                    // Check if it's a new temp image or existing one
                    if (Str::startsWith($imagePath, 'temp/')) {
                        // Move from temp to permanent storage
                        $newPath = $this->moveImageFromTemp($imagePath, 'news/gallery/');
                        if ($newPath) {
                            $galleryImages[] = $newPath;
                        }
                    } else {
                        // Keep existing image path
                        $galleryImages[] = $imagePath;
                    }
                }
            }

            // Process keywords into tags array
            $tags = [];
            if (!empty($data['keywords'])) {
                $tags = array_map('trim', explode(',', $data['keywords']));
            }

            // Update news data
            $updateData = [
                'title' => $data['title'],
                'slug' => Str::slug($data['title']),
                'content' => $data['content'],
                'excerpt' => Str::limit(strip_tags($data['content']), 200),
                'featured_image_path' => $featuredImagePath,
                'tags' => $tags,
                'auto_post_twitter' => $data['publish_to_twitter'] ?? false,
                'seo_data' => [
                    'keywords' => $data['keywords'] ?? '',
                    'video_url' => $data['video_url'] ?? '',
                    'twitter_message' => $data['twitter_message'] ?? '',
                    'gallery_images' => $galleryImages
                ]
            ];

            // Update news
            $this->newsRepo->update($newsId, $updateData);

            // Refresh news data
            $news = $this->newsRepo->findWithFullDetails($newsId);

            // Log activity
            Log::info('News updated', [
                'news_id' => $newsId,
                'user_id' => $user->id
            ]);

            DB::commit();

            return [
                'success' => true,
                'news' => $news,
                'message' => 'News article updated successfully.'
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to update news', [
                'news_id' => $newsId,
                'user_id' => $user->id,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Delete a news article (admin)
     */
    public function deleteNewsAdmin(int $newsId, User $user): array
    {
        $news = $this->newsRepo->find($newsId);

        if (!$news) {
            throw new \Exception('News article not found.');
        }

        // Check permissions
        if (!$this->userCanDeleteNews($user, $news)) {
            throw new \Exception('You do not have permission to delete this news article.');
        }

        DB::beginTransaction();
        try {
            // Delete associated images
            if ($news->featured_image_path) {
                Storage::disk('public')->delete($news->featured_image_path);
            }

            $galleryImages = $news->seo_data['gallery_images'] ?? [];
            foreach ($galleryImages as $imagePath) {
                Storage::disk('public')->delete($imagePath);
            }

            // Delete news
            $this->newsRepo->delete($newsId);

            // Log activity
            Log::info('News deleted', [
                'news_id' => $newsId,
                'user_id' => $user->id
            ]);

            DB::commit();

            return [
                'success' => true,
                'message' => 'News article deleted successfully.'
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to delete news', [
                'news_id' => $newsId,
                'user_id' => $user->id,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Publish a news article (admin)
     */
    public function publishNewsAdmin(int $newsId, User $user): array
    {
        $news = $this->newsRepo->find($newsId);

        if (!$news) {
            throw new \Exception('News article not found.');
        }

        // Check permissions
        if (!$this->userCanEditNews($user, $news)) {
            throw new \Exception('You do not have permission to publish this news article.');
        }

        try {
            $this->newsRepo->update($newsId, [
                'status' => 'published',
                'published_at' => now()
            ]);

            // Clear cache
            Cache::forget('published_news_*');
            Cache::forget('featured_news');
            Cache::forget('recent_news_*');

            return [
                'success' => true,
                'message' => 'News article published successfully.'
            ];
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Unpublish a news article (admin)
     */
    public function unpublishNewsAdmin(int $newsId, User $user): array
    {
        $news = $this->newsRepo->find($newsId);

        if (!$news) {
            throw new \Exception('News article not found.');
        }

        // Check permissions
        if (!$this->userCanEditNews($user, $news)) {
            throw new \Exception('You do not have permission to unpublish this news article.');
        }

        try {
            $this->newsRepo->update($newsId, ['status' => 'draft']);

            // Clear cache
            Cache::forget('published_news_*');
            Cache::forget('featured_news');
            Cache::forget('recent_news_*');

            return [
                'success' => true,
                'message' => 'News article unpublished successfully.'
            ];
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Move image from temp to permanent storage
     */
    protected function moveImageFromTemp(string $tempPath, string $targetDir): ?string
    {
        // Check if this is actually a temp path
        if (!Str::startsWith($tempPath, 'temp/')) {
            // Not a temp path, return as is
            return $tempPath;
        }

        if (Storage::disk('public')->exists($tempPath)) {
            $filename = basename($tempPath);
            $newPath = $targetDir . $filename;
            Storage::disk('public')->move($tempPath, $newPath);
            return $newPath;
        }
        return null;
    }

    /**
     * Build filters based on user role
     */
    protected function buildRoleFilters(User $user, array $filters): array
    {
        $roleFilters = $filters;

        switch ($user->user_type) {
            case 'hackathon_admin':
                // Limit to user's edition
                if ($user->edition_id) {
                    $roleFilters['edition_id'] = $user->edition_id;
                }
                break;

            case 'system_admin':
                // No additional filters - can see everything
                break;

            default:
                // Other roles - force empty result
                $roleFilters['force_empty'] = true;
                break;
        }

        return $roleFilters;
    }

    /**
     * Get editions available to user
     */
    protected function getEditionsForUser(User $user): Collection
    {
        if (!$this->editionRepository) {
            return collect();
        }

        switch ($user->user_type) {
            case 'system_admin':
                return $this->editionRepository->all();

            case 'hackathon_admin':
                if ($user->edition_id) {
                    return collect([$this->editionRepository->find($user->edition_id)]);
                }
                return collect();

            default:
                return collect();
        }
    }

    /**
     * Check if user can access a specific news article
     */
    protected function userCanAccessNews(User $user, $news): bool
    {
        switch ($user->user_type) {
            case 'system_admin':
                return true;

            case 'hackathon_admin':
                return !isset($news->edition_id) || $user->edition_id == $news->edition_id;

            default:
                return false;
        }
    }

    /**
     * Check if user can create news
     */
    protected function userCanCreateNews(User $user): bool
    {
        return in_array($user->user_type, ['system_admin', 'hackathon_admin']);
    }

    /**
     * Check if user can edit news
     */
    protected function userCanEditNews(User $user, $news): bool
    {
        if (!$this->userCanAccessNews($user, $news)) {
            return false;
        }

        return in_array($user->user_type, ['system_admin', 'hackathon_admin']);
    }

    /**
     * Check if user can delete news
     */
    protected function userCanDeleteNews(User $user, $news): bool
    {
        if (!$this->userCanAccessNews($user, $news)) {
            return false;
        }

        // Only system admin can delete
        return $user->hasRole('system_admin');
    }

    /**
     * Get permissions for news
     */
    protected function getNewsPermissions(User $user, $news): array
    {
        return [
            'canEdit' => $this->userCanEditNews($user, $news),
            'canDelete' => $this->userCanDeleteNews($user, $news),
            'canPublish' => $this->userCanEditNews($user, $news),
        ];
    }
}
