<?php

namespace App\Models;

// ... existing imports ...
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

// Add these methods to the User model:

trait HackathonAdminEditionTrait
{
    /**
     * Get editions managed by this hackathon admin
     */
    public function managedEditions(): HasMany
    {
        return $this->hasMany(Edition::class, 'admin_id');
    }

    /**
     * Get editions assigned to this user (for multiple edition support)
     */
    public function assignedEditions(): BelongsToMany
    {
        return $this->belongsToMany(Edition::class, 'user_edition_assignments')
                    ->withPivot('role')
                    ->withTimestamps();
    }

    /**
     * Get the primary managed edition for this hackathon admin
     */
    public function primaryEdition()
    {
        return $this->managedEditions()->where('is_active', true)->first() 
               ?? $this->managedEditions()->latest()->first();
    }

    /**
     * Check if user can access a specific edition
     */
    public function canAccessEdition($editionId): bool
    {
        // System admins can access all editions
        if ($this->hasRole('system_admin') || $this->isSuperUser()) {
            return true;
        }

        // Check if user is the admin of this edition
        if ($this->managedEditions()->where('id', $editionId)->exists()) {
            return true;
        }

        // Check if user is assigned to this edition
        if ($this->assignedEditions()->where('edition_id', $editionId)->exists()) {
            return true;
        }

        return false;
    }

    /**
     * Get accessible edition IDs for this user
     */
    public function getAccessibleEditionIds(): array
    {
        // System admins can access all editions
        if ($this->hasRole('system_admin') || $this->isSuperUser()) {
            return Edition::pluck('id')->toArray();
        }

        $editionIds = [];

        // Get managed editions
        $managedIds = $this->managedEditions()->pluck('id')->toArray();
        $editionIds = array_merge($editionIds, $managedIds);

        // Get assigned editions
        $assignedIds = $this->assignedEditions()->pluck('editions.id')->toArray();
        $editionIds = array_merge($editionIds, $assignedIds);

        return array_unique($editionIds);
    }

    /**
     * Scope queries to only accessible editions
     */
    public function scopeWithAccessibleEditions($query, $user = null)
    {
        $user = $user ?? auth()->user();

        if (!$user) {
            return $query->whereRaw('1 = 0'); // Return no results if no user
        }

        $editionIds = $user->getAccessibleEditionIds();

        if (empty($editionIds)) {
            return $query->whereRaw('1 = 0'); // Return no results if no accessible editions
        }

        return $query->whereIn('edition_id', $editionIds);
    }
}

// Add this trait to your User model class:
// use HackathonAdminEditionTrait;
