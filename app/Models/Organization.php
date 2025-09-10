<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Organization extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'logo_path',
        'website',
        'email',
        'phone',
        'address',
        'type',
        'is_active',
        'social_media',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'social_media' => 'array',
    ];

    protected static function booted()
    {
        static::creating(function ($organization) {
            if (empty($organization->slug)) {
                $organization->slug = Str::slug($organization->name);
            }
        });

        static::updating(function ($organization) {
            if ($organization->isDirty('name') && empty($organization->slug)) {
                $organization->slug = Str::slug($organization->name);
            }
        });
    }

    /**
     * Get the speakers that belong to this organization.
     */
    public function speakers(): HasMany
    {
        return $this->hasMany(Speaker::class);
    }

    /**
     * Get the workshops this organization is associated with.
     */
    public function workshops(): BelongsToMany
    {
        return $this->belongsToMany(Workshop::class, 'workshop_organizations')
            ->withPivot(['role'])
            ->withTimestamps();
    }

    /**
     * Get workshops where this organization is the organizer.
     */
    public function organizedWorkshops(): BelongsToMany
    {
        return $this->workshops()->wherePivot('role', 'organizer');
    }

    /**
     * Get workshops where this organization is a sponsor.
     */
    public function sponsoredWorkshops(): BelongsToMany
    {
        return $this->workshops()->wherePivot('role', 'sponsor');
    }

    /**
     * Get workshops where this organization is a partner.
     */
    public function partneredWorkshops(): BelongsToMany
    {
        return $this->workshops()->wherePivot('role', 'partner');
    }

    /**
     * Get the organization's logo URL.
     */
    public function getLogoUrlAttribute(): ?string
    {
        return $this->logo_path ? asset('storage/' . $this->logo_path) : null;
    }

    /**
     * Get formatted website URL.
     */
    public function getFormattedWebsiteAttribute(): ?string
    {
        if (!$this->website) {
            return null;
        }

        if (!str_starts_with($this->website, 'http')) {
            return 'https://' . $this->website;
        }

        return $this->website;
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
                'linkedin' => 'https://linkedin.com/company/' . ltrim($handle, '/'),
                'instagram' => 'https://instagram.com/' . ltrim($handle, '@'),
                'facebook' => 'https://facebook.com/' . ltrim($handle, '/'),
                'youtube' => 'https://youtube.com/c/' . ltrim($handle, '/'),
                default => $handle,
            };
        }

        return $links;
    }

    /**
     * Get organization type label.
     */
    public function getTypeAttribute($value): string
    {
        return match ($value) {
            'government' => 'Government',
            'private' => 'Private Sector',
            'ngo' => 'NGO',
            'educational' => 'Educational Institution',
            'other' => 'Other',
            default => 'Other',
        };
    }

    /**
     * Scope to get active organizations.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to get organizations by type.
     */
    public function scopeByType($query, string $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Scope to search organizations.
     */
    public function scopeSearch($query, string $search)
    {
        return $query->where('name', 'like', "%{$search}%")
            ->orWhere('description', 'like', "%{$search}%");
    }
}
