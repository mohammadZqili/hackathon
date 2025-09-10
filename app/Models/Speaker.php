<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Str;

class Speaker extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'title',
        'bio',
        'photo_path',
        'email',
        'phone',
        'organization_id',
        'expertise_areas',
        'social_media',
        'is_active',
    ];

    protected $casts = [
        'expertise_areas' => 'array',
        'social_media' => 'array',
        'is_active' => 'boolean',
    ];

    protected static function booted()
    {
        static::creating(function ($speaker) {
            if (empty($speaker->slug)) {
                $speaker->slug = Str::slug($speaker->name);
            }
        });

        static::updating(function ($speaker) {
            if ($speaker->isDirty('name') && empty($speaker->slug)) {
                $speaker->slug = Str::slug($speaker->name);
            }
        });
    }

    /**
     * Get the organization this speaker belongs to.
     */
    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    /**
     * Get the workshops this speaker is associated with.
     */
    public function workshops(): BelongsToMany
    {
        return $this->belongsToMany(Workshop::class, 'workshop_speakers')
            ->withPivot(['role', 'order'])
            ->withTimestamps()
            ->orderBy('workshop_speakers.order');
    }

    /**
     * Get workshops where this speaker is the main speaker.
     */
    public function mainSpeakerWorkshops(): BelongsToMany
    {
        return $this->workshops()->wherePivot('role', 'main_speaker');
    }

    /**
     * Get workshops where this speaker is a co-speaker.
     */
    public function coSpeakerWorkshops(): BelongsToMany
    {
        return $this->workshops()->wherePivot('role', 'co_speaker');
    }

    /**
     * Get workshops where this speaker is a moderator.
     */
    public function moderatedWorkshops(): BelongsToMany
    {
        return $this->workshops()->wherePivot('role', 'moderator');
    }

    /**
     * Get the speaker's photo URL.
     */
    public function getPhotoUrlAttribute(): ?string
    {
        return $this->photo_path ? asset('storage/' . $this->photo_path) : null;
    }

    /**
     * Get the speaker's full name with title.
     */
    public function getFullNameAttribute(): string
    {
        return $this->title ? $this->name . ', ' . $this->title : $this->name;
    }

    /**
     * Get social media links.
     */
    public function getSocialLinksAttribute(): array
    {
        $links = [];
        $socialMedia = $this->social_media ?? [];

        foreach ($socialMedia as $platform => $handle) {
            if (empty($handle)) continue;

            $links[$platform] = match ($platform) {
                'twitter' => 'https://twitter.com/' . ltrim($handle, '@'),
                'linkedin' => 'https://linkedin.com/in/' . ltrim($handle, '/'),
                'instagram' => 'https://instagram.com/' . ltrim($handle, '@'),
                'facebook' => 'https://facebook.com/' . ltrim($handle, '/'),
                'youtube' => 'https://youtube.com/c/' . ltrim($handle, '/'),
                'website' => str_starts_with($handle, 'http') ? $handle : 'https://' . $handle,
                default => $handle,
            };
        }

        return $links;
    }

    /**
     * Get formatted expertise areas.
     */
    public function getFormattedExpertiseAttribute(): string
    {
        return is_array($this->expertise_areas) ? implode(', ', $this->expertise_areas) : '';
    }

    /**
     * Get speaker statistics.
     */
    public function getStatsAttribute(): array
    {
        return [
            'total_workshops' => $this->workshops()->count(),
            'upcoming_workshops' => $this->workshops()->where('start_time', '>', now())->count(),
            'completed_workshops' => $this->workshops()->where('end_time', '<', now())->count(),
        ];
    }

    /**
     * Scope to get active speakers.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to search speakers.
     */
    public function scopeSearch($query, string $search)
    {
        return $query->where('name', 'like', "%{$search}%")
            ->orWhere('title', 'like', "%{$search}%")
            ->orWhere('bio', 'like', "%{$search}%");
    }

    /**
     * Scope to get speakers by expertise.
     */
    public function scopeByExpertise($query, string $expertise)
    {
        return $query->whereJsonContains('expertise_areas', $expertise);
    }
}
