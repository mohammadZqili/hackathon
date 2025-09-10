<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Edition extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'year',
        'registration_start_date',
        'registration_end_date',
        'hackathon_start_date',
        'hackathon_end_date',
        'admin_id',
        'description',
        'location',
        'max_teams',
        'max_team_members',
        'is_active',
        'settings'
    ];

    protected $casts = [
        'registration_start_date' => 'date',
        'registration_end_date' => 'date',
        'hackathon_start_date' => 'date',
        'hackathon_end_date' => 'date',
        'is_active' => 'boolean',
        'settings' => 'array'
    ];

    public function admin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'admin_id');
    }

    public function teams(): HasMany
    {
        return $this->hasMany(Team::class, 'edition_id', 'id');
    }

    public function ideas(): HasMany
    {
        return $this->hasMany(Idea::class, 'edition_id', 'id');
    }

    public function workshops(): HasMany
    {
        return $this->hasMany(Workshop::class, 'hackathon_edition_id', 'id');
    }

    public function getTeamsCountAttribute(): int
    {
        return $this->teams()->count();
    }

    public function getRegistrationDatesAttribute(): string
    {
        return $this->registration_start_date->format('M j') . ' - ' . $this->registration_end_date->format('M j');
    }

    public function getHackathonDatesAttribute(): string
    {
        return $this->hackathon_start_date->format('M j') . ' - ' . $this->hackathon_end_date->format('M j');
    }
}