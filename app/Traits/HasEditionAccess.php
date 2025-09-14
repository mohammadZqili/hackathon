<?php

namespace App\Traits;

use App\Models\Edition;
use Illuminate\Database\Eloquent\Builder;

trait HasEditionAccess
{
    /**
     * Get the current edition ID for the authenticated user
     */
    protected function getCurrentEditionId(): ?int
    {
        return session('current_edition_id') ?? request()->input('edition_filter');
    }

    /**
     * Get accessible edition IDs for the current user
     */
    protected function getAccessibleEditionIds(): array
    {
        return auth()->user()->getAccessibleEditionIds();
    }

    /**
     * Apply edition filter to a query
     */
    protected function applyEditionFilter(Builder $query, string $column = 'edition_id'): Builder
    {
        $editionIds = $this->getAccessibleEditionIds();
        
        if (empty($editionIds)) {
            // Return empty result set if no accessible editions
            return $query->whereRaw('1 = 0');
        }

        // If specific edition is selected, use it
        $currentEditionId = $this->getCurrentEditionId();
        if ($currentEditionId && in_array($currentEditionId, $editionIds)) {
            return $query->where($column, $currentEditionId);
        }

        // Otherwise filter by all accessible editions
        return $query->whereIn($column, $editionIds);
    }

    /**
     * Get editions for dropdown/filter
     */
    protected function getEditionsForFilter()
    {
        $user = auth()->user();
        
        // System admins see all editions
        if ($user->hasRole('system_admin') || $user->isSuperUser()) {
            return Edition::orderBy('year', 'desc')->orderBy('name')->get();
        }

        // Hackathon admins see only their assigned editions
        $editionIds = $this->getAccessibleEditionIds();
        return Edition::whereIn('id', $editionIds)
                      ->orderBy('year', 'desc')
                      ->orderBy('name')
                      ->get();
    }

    /**
     * Check if user can access the current resource based on edition
     */
    protected function canAccessResource($resource): bool
    {
        if (!$resource || !isset($resource->edition_id)) {
            return false;
        }

        return in_array($resource->edition_id, $this->getAccessibleEditionIds());
    }

    /**
     * Abort if user cannot access the resource
     */
    protected function authorizeEditionAccess($resource): void
    {
        if (!$this->canAccessResource($resource)) {
            abort(403, 'You do not have access to this resource.');
        }
    }
}
