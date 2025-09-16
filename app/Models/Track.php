<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Track extends Model
{
    use HasFactory;

    protected $fillable = [
        'hackathon_id',
        'edition_id',
        'name',
        'description',
        'instructions',
        'icon',
        'color',
        'max_teams',
        'evaluation_criteria',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'evaluation_criteria' => 'array',
        'is_active' => 'boolean',
        'max_teams' => 'integer',
        'sort_order' => 'integer',
    ];

    /**
     * Get the hackathon that owns this track.
     */
    public function hackathon(): BelongsTo
    {
        return $this->belongsTo(Hackathon::class);
    }

    /**
     * Get the edition that owns this track.
     */
    public function edition(): BelongsTo
    {
        return $this->belongsTo(HackathonEdition::class, 'edition_id');
    }

    /**
     * Get the teams in this track.
     */
    public function teams(): HasMany
    {
        return $this->hasMany(Team::class);
    }

    /**
     * Get the ideas submitted to this track.
     */
    public function ideas(): HasMany
    {
        return $this->hasMany(Idea::class);
    }

    /**
     * Get the supervisors for this track.
     */
    public function supervisors(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'track_supervisors')
            ->withPivot(['is_primary'])
            ->withTimestamps();
    }

    /**
     * Get the primary supervisor for this track (singular relationship).
     */
    public function supervisor()
    {
        return $this->belongsToMany(User::class, 'track_supervisors')
            ->withPivot(['is_primary'])
            ->wherePivot('is_primary', true)
            ->withTimestamps();
    }

    /**
     * Get the primary supervisor for this track.
     */
    public function primarySupervisor()
    {
        return $this->supervisors()->wherePivot('is_primary', true)->first();
    }

    /**
     * Check if track has reached maximum teams.
     */
    public function isFull(): bool
    {
        if (!$this->max_teams) {
            return false;
        }

        return $this->teams()->where('status', '!=', 'disqualified')->count() >= $this->max_teams;
    }

    /**
     * Get available spots in track.
     */
    public function getAvailableSpotsAttribute(): ?int
    {
        if (!$this->max_teams) {
            return null;
        }

        $usedSpots = $this->teams()->where('status', '!=', 'disqualified')->count();
        return max(0, $this->max_teams - $usedSpots);
    }

    /**
     * Get track statistics.
     */
    public function getStats(): array
    {
        $teams = $this->teams();
        $ideas = $this->ideas();

        return [
            'total_teams' => $teams->count(),
            'active_teams' => $teams->where('status', 'active')->count(),
            'submitted_ideas' => $ideas->where('status', 'submitted')->count(),
            'under_review' => $ideas->where('status', 'under_review')->count(),
            'accepted_ideas' => $ideas->where('status', 'accepted')->count(),
            'rejected_ideas' => $ideas->where('status', 'rejected')->count(),
        ];
    }

    /**
     * Scope to get active tracks.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to order by sort_order.
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order');
    }

    /**
     * Get the track's icon URL.
     */
    public function getIconUrlAttribute(): ?string
    {
        return $this->icon ? asset('storage/' . $this->icon) : null;
    }
}
