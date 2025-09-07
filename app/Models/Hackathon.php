<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Hackathon extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'theme',
        'year',
        'registration_start_date',
        'registration_end_date',
        'idea_submission_start_date',
        'idea_submission_end_date',
        'event_start_date',
        'event_end_date',
        'location',
        'is_active',
        'is_current',
        'settings',
        'created_by',
    ];

    protected $casts = [
        'registration_start_date' => 'date',
        'registration_end_date' => 'date',
        'idea_submission_start_date' => 'date',
        'idea_submission_end_date' => 'date',
        'event_start_date' => 'date',
        'event_end_date' => 'date',
        'is_active' => 'boolean',
        'is_current' => 'boolean',
        'settings' => 'array',
    ];

    /**
     * Get the creator of this hackathon.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the tracks for this hackathon.
     */
    public function tracks(): HasMany
    {
        return $this->hasMany(Track::class);
    }

    /**
     * Get the teams for this hackathon.
     */
    public function teams(): HasMany
    {
        return $this->hasMany(Team::class);
    }

    /**
     * Get the workshops for this hackathon.
     */
    public function workshops(): HasMany
    {
        return $this->hasMany(Workshop::class);
    }

    /**
     * Check if registration is open.
     */
    public function isRegistrationOpen(): bool
    {
        $now = now()->toDateString();
        return $now >= $this->registration_start_date->toDateString() &&
               $now <= $this->registration_end_date->toDateString() &&
               $this->is_active;
    }

    /**
     * Check if idea submission is open.
     */
    public function isIdeaSubmissionOpen(): bool
    {
        $now = now()->toDateString();
        return $now >= $this->idea_submission_start_date->toDateString() &&
               $now <= $this->idea_submission_end_date->toDateString() &&
               $this->is_active;
    }

    /**
     * Check if the event is currently running.
     */
    public function isEventRunning(): bool
    {
        $now = now()->toDateString();
        return $now >= $this->event_start_date->toDateString() &&
               $now <= $this->event_end_date->toDateString() &&
               $this->is_active;
    }

    /**
     * Get the current hackathon.
     */
    public static function current(): ?self
    {
        return self::where('is_current', true)->where('is_active', true)->first();
    }

    /**
     * Set this hackathon as current and deactivate others.
     */
    public function setCurrent(): void
    {
        self::where('is_current', true)->update(['is_current' => false]);
        $this->update(['is_current' => true, 'is_active' => true]);
    }

    /**
     * Get registration stats.
     */
    public function getRegistrationStats(): array
    {
        return [
            'total_teams' => $this->teams()->count(),
            'total_participants' => $this->teams()->withCount('members')->get()->sum('members_count'),
            'submitted_ideas' => $this->teams()->whereHas('idea', function ($query) {
                $query->where('status', 'submitted');
            })->count(),
            'accepted_ideas' => $this->teams()->whereHas('idea', function ($query) {
                $query->where('status', 'accepted');
            })->count(),
        ];
    }

    /**
     * Get the hackathon's full title with year.
     */
    public function getFullTitleAttribute(): string
    {
        return $this->name . ' ' . $this->year;
    }
}
