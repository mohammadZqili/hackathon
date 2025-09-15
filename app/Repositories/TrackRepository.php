<?php

namespace App\Repositories;

use App\Models\Track;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class TrackRepository extends BaseRepository
{
    public function __construct()
    {
        parent::__construct(new Track());
    }

    /**
     * Get track supervisors
     */
    public function getTrackSupervisors()
    {
        return \App\Models\User::role('track_supervisor')->get();
    }

    /**
     * Get paginated tracks with filters
     */
    public function getPaginatedWithFilters(array $filters, int $perPage = 15): LengthAwarePaginator
    {
        $query = $this->query()
            ->with(['teams', 'ideas', 'edition', 'hackathon'])
            ->withCount(['teams', 'ideas']);

        // Apply filters
        if (!empty($filters['edition_id'])) {
            $query->where('edition_id', $filters['edition_id']);
        }

        if (!empty($filters['hackathon_id'])) {
            $query->where('hackathon_id', $filters['hackathon_id']);
        }

        if (isset($filters['status'])) {
            $isActive = $filters['status'] === 'active' ? 1 : 0;
            $query->where('is_active', $isActive);
        }

        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if (!empty($filters['track_ids'])) {
            $query->whereIn('id', $filters['track_ids']);
        }

        return $query->latest()->paginate($perPage)->withQueryString();
    }

    /**
     * Get track with full details
     */
    public function findWithFullDetails(int $id): ?Track
    {
        return $this->query()
            ->with(['teams.members', 'ideas.team', 'edition', 'hackathon'])
            ->find($id);
    }

    /**
     * Get statistics for tracks
     */
    public function getStatistics(array $filters = []): array
    {
        $query = $this->query();

        // Apply same filters as listing
        if (!empty($filters['edition_id'])) {
            $query->where('edition_id', $filters['edition_id']);
        }

        if (!empty($filters['hackathon_id'])) {
            $query->where('hackathon_id', $filters['hackathon_id']);
        }

        if (!empty($filters['track_ids'])) {
            $query->whereIn('id', $filters['track_ids']);
        }

        return [
            'total' => $query->count(),
            'active' => (clone $query)->where('is_active', true)->count(),
            'inactive' => (clone $query)->where('is_active', false)->count(),
            'with_teams' => (clone $query)->has('teams')->count(),
            'total_teams' => (clone $query)->withCount('teams')->get()->sum('teams_count'),
            'total_ideas' => (clone $query)->withCount('ideas')->get()->sum('ideas_count'),
        ];
    }

    /**
     * Get tracks for export
     */
    public function getForExport(array $filters = []): Collection
    {
        $query = $this->query()
            ->with(['edition', 'hackathon'])
            ->withCount(['teams', 'ideas']);

        if (!empty($filters['edition_id'])) {
            $query->where('edition_id', $filters['edition_id']);
        }

        if (!empty($filters['hackathon_id'])) {
            $query->where('hackathon_id', $filters['hackathon_id']);
        }

        if (!empty($filters['track_ids'])) {
            $query->whereIn('id', $filters['track_ids']);
        }

        return $query->get();
    }

    /**
     * Check if track has dependencies
     */
    public function hasDependencies(int $id): bool
    {
        $track = $this->find($id);
        if (!$track) {
            return false;
        }

        return $track->teams()->exists() || $track->ideas()->exists();
    }

    /**
     * Get active tracks
     */
    public function getActive(array $filters = []): Collection
    {
        $query = $this->query()->where('is_active', true);

        if (!empty($filters['edition_id'])) {
            $query->where('edition_id', $filters['edition_id']);
        }

        if (!empty($filters['hackathon_id'])) {
            $query->where('hackathon_id', $filters['hackathon_id']);
        }

        return $query->with(['teams', 'edition'])->get();
    }

    /**
     * Get tracks with team count
     */
    public function getWithTeamCount(array $filters = []): Collection
    {
        $query = $this->query()->withCount('teams');

        if (!empty($filters['edition_id'])) {
            $query->where('edition_id', $filters['edition_id']);
        }

        if (!empty($filters['hackathon_id'])) {
            $query->where('hackathon_id', $filters['hackathon_id']);
        }

        return $query->get();
    }

    /**
     * Get tracks by supervisor
     */
    public function getTracksBySupervisor(int $userId): Collection
    {
        return $this->query()
            ->whereHas('supervisors', function ($q) use ($userId) {
                $q->where('user_id', $userId);
            })
            ->with(['teams', 'ideas', 'edition'])
            ->get();
    }

    /**
     * Assign supervisor to track
     */
    public function assignSupervisor(int $trackId, int $userId, bool $isPrimary = false): bool
    {
        $track = $this->find($trackId);
        if (!$track) {
            return false;
        }

        // Check if already assigned
        if ($track->supervisors()->where('user_id', $userId)->exists()) {
            // Update if already exists
            $track->supervisors()->updateExistingPivot($userId, ['is_primary' => $isPrimary]);
        } else {
            // Attach new supervisor
            $track->supervisors()->attach($userId, ['is_primary' => $isPrimary]);
        }

        return true;
    }

    /**
     * Remove supervisor from track
     */
    public function removeSupervisor(int $trackId, int $userId): bool
    {
        $track = $this->find($trackId);
        if (!$track) {
            return false;
        }

        $track->supervisors()->detach($userId);
        return true;
    }

    /**
     * Update track status
     */
    public function updateStatus(int $id, bool $isActive): bool
    {
        return $this->update($id, ['is_active' => $isActive]);
    }

    /**
     * Check if track is full
     */
    public function isFull(int $id): bool
    {
        $track = $this->findWithFullDetails($id);
        if (!$track || !$track->max_teams) {
            return false;
        }

        return $track->teams()->count() >= $track->max_teams;
    }

    /**
     * Get available tracks for team registration
     */
    public function getAvailableForRegistration(int $editionId): Collection
    {
        return $this->query()
            ->where('edition_id', $editionId)
            ->where('is_active', true)
            ->whereRaw('(max_teams IS NULL OR (SELECT COUNT(*) FROM teams WHERE teams.track_id = tracks.id) < max_teams)')
            ->with(['teams'])
            ->withCount('teams')
            ->get();
    }
}