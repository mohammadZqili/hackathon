<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class News extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'excerpt',
        'content',
        'featured_image_path',
        'status',
        'is_featured',
        'published_at',
        'author_id',
        'hackathon_id',
        'tags',
        'views_count',
        'auto_post_twitter',
        'posted_to_twitter',
        'twitter_post_id',
        'twitter_posted_at',
        'seo_data',
    ];

    protected $casts = [
        'published_at' => 'datetime',
        'is_featured' => 'boolean',
        'views_count' => 'integer',
        'auto_post_twitter' => 'boolean',
        'posted_to_twitter' => 'boolean',
        'twitter_posted_at' => 'datetime',
        'tags' => 'array',
        'seo_data' => 'array',
    ];

    protected static function booted()
    {
        static::creating(function ($news) {
            if (empty($news->slug)) {
                $news->slug = Str::slug($news->title);
            }
            if (empty($news->excerpt) && $news->content) {
                $news->excerpt = Str::limit(strip_tags($news->content), 200);
            }
        });

        static::updating(function ($news) {
            if ($news->isDirty('title') && empty($news->slug)) {
                $news->slug = Str::slug($news->title);
            }
        });
    }

    /**
     * Get the author of this news article.
     */
    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    /**
     * Get the hackathon this news article belongs to.
     */
    public function hackathon(): BelongsTo
    {
        return $this->belongsTo(Hackathon::class, 'hackathon_id');
    }

    /**
     * Publish the news article.
     */
    public function publish(): bool
    {
        return $this->update([
            'status' => 'published',
            'published_at' => now(),
        ]);
    }

    /**
     * Archive the news article.
     */
    public function archive(): bool
    {
        return $this->update(['status' => 'archived']);
    }

    /**
     * Increment views count.
     */
    public function incrementViews(): void
    {
        $this->increment('views_count');
    }

    /**
     * Mark as featured.
     */
    public function setFeatured(bool $featured = true): bool
    {
        return $this->update(['is_featured' => $featured]);
    }

    /**
     * Mark as posted to Twitter.
     */
    public function markPostedToTwitter(string $tweetId): bool
    {
        return $this->update([
            'posted_to_twitter' => true,
            'twitter_post_id' => $tweetId,
            'twitter_posted_at' => now(),
        ]);
    }

    /**
     * Get the featured image URL.
     */
    public function getFeaturedImageUrlAttribute(): ?string
    {
        return $this->featured_image_path ? asset('storage/' . $this->featured_image_path) : null;
    }

    /**
     * Get the news article URL.
     */
    public function getUrlAttribute(): string
    {
        return route('news.show', $this->slug);
    }

    /**
     * Get the Twitter share URL.
     */
    public function getTwitterShareUrlAttribute(): string
    {
        $text = urlencode($this->title);
        $url = urlencode($this->url);
        return "https://twitter.com/intent/tweet?text={$text}&url={$url}";
    }

    /**
     * Get reading time in minutes.
     */
    public function getReadingTimeAttribute(): int
    {
        $wordCount = str_word_count(strip_tags($this->content));
        return max(1, ceil($wordCount / 200)); // Assuming 200 words per minute
    }

    /**
     * Get formatted tags.
     */
    public function getFormattedTagsAttribute(): string
    {
        return is_array($this->tags) ? implode(', ', $this->tags) : '';
    }

    /**
     * Get the status badge color.
     */
    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'draft' => 'gray',
            'published' => 'green',
            'archived' => 'red',
            default => 'gray',
        };
    }

    /**
     * Get the status label.
     */
    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'draft' => 'Draft',
            'published' => 'Published',
            'archived' => 'Archived',
            default => 'Unknown',
        };
    }

    /**
     * Check if article is published.
     */
    public function isPublished(): bool
    {
        return $this->status === 'published' && 
               $this->published_at && 
               $this->published_at->isPast();
    }

    /**
     * Check if article is draft.
     */
    public function isDraft(): bool
    {
        return $this->status === 'draft';
    }

    /**
     * Check if article is archived.
     */
    public function isArchived(): bool
    {
        return $this->status === 'archived';
    }

    /**
     * Scope to get published articles.
     */
    public function scopePublished($query)
    {
        return $query->where('status', 'published')
            ->where('published_at', '<=', now());
    }

    /**
     * Scope to get featured articles.
     */
    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    /**
     * Scope to get articles by status.
     */
    public function scopeByStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope to search articles.
     */
    public function scopeSearch($query, string $search)
    {
        return $query->where('title', 'like', "%{$search}%")
            ->orWhere('content', 'like', "%{$search}%")
            ->orWhere('excerpt', 'like', "%{$search}%");
    }

    /**
     * Scope to get articles by tag.
     */
    public function scopeByTag($query, string $tag)
    {
        return $query->whereJsonContains('tags', $tag);
    }

    /**
     * Scope to order by latest.
     */
    public function scopeLatest($query)
    {
        return $query->orderBy('published_at', 'desc')
            ->orderBy('created_at', 'desc');
    }
}
