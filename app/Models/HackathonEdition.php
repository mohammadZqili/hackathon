<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;

class HackathonEdition extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'year',
        'description',
        'theme',
        'registration_start_date',
        'registration_end_date',
        'idea_submission_start_date',
        'idea_submission_end_date',
        'event_start_date',
        'event_end_date',
        'location',
        'status',
        'is_current',
        'settings',
        'statistics',
        'created_by',
    ];

    protected $casts = [
        'registration_start_date' => 'date',
        'registration_end_date' => 'date',
        'idea_submission_start_date' => 'date',
        'idea_submission_end_date' => 'date',
        'event_start_date' => 'date',
        'event_end_date' => 'date',
        'is_current' => 'boolean',
        'settings' => 'json',
        'statistics' => 'json',
    ];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function teams(): HasMany
    {
        return $this->hasMany(Team::class, 'edition_id');
    }

    public function workshops(): HasMany
    {
        return $this->hasMany(Workshop::class, 'hackathon_edition_id');
    }

    public function tracks(): HasMany
    {
        return $this->hasMany(Track::class, 'hackathon_edition_id');
    }

    public function news(): HasMany
    {
        return $this->hasMany(News::class, 'hackathon_id');
    }

    // Scopes
    public function scopeCurrent(Builder $query): Builder
    {
        return $query->where('is_current', true);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', 'active');
    }

    public function scopeByYear(Builder $query, int $year): Builder
    {
        return $query->where('year', $year);
    }

    // Helper methods
    public function isRegistrationOpen(): bool
    {
        return now()->between($this->registration_start_date, $this->registration_end_date);
    }

    public function isIdeaSubmissionOpen(): bool
    {
        return now()->between($this->idea_submission_start_date, $this->idea_submission_end_date);
    }

    public function getStatusBadgeAttribute(): string
    {
        return match($this->status) {
            'draft' => 'bg-gray-100 text-gray-800',
            'active' => 'bg-green-100 text-green-800',
            'completed' => 'bg-blue-100 text-blue-800',
            'archived' => 'bg-yellow-100 text-yellow-800',
            default => 'bg-gray-100 text-gray-800'
        };
    }
}