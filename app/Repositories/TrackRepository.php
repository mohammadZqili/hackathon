<?php

namespace App\Repositories;

use App\Models\Track;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class TrackRepository extends BaseRepository
{
    public function __construct(Track $model)
    {
        parent::__construct($model);
    }

    /**
     * Get tracks with optional filters
     */
    public function getTracks(array $filters = []): array
    {
        $query = $this->model->with(['supervisor', 'teams', 'ideas']);

        // Apply search filter
        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Apply status filter
        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        // Apply hackathon filter
        if (!empty($filters['hackathon_id'])) {
            $query->where('hackathon_id', $filters['hackathon_id']);
        }

        // Apply supervisor filter
        if (!empty($filters['supervisor_id'])) {
            $query->where('supervisor_id', $filters['supervisor_id']);
        }

        // Apply sorting
        $sortBy = $filters['sort_by'] ?? 'created_at';
        $sortDirection = $filters['sort_direction'] ?? 'desc';
        $query->orderBy($sortBy, $sortDirection);

        // Check if pagination is required
        if (isset($filters['paginate']) && $filters['paginate']) {
            $perPage = $filters['per_page'] ?? 15;
            $result = $query->paginate($perPage);
            
            return [
                'data' => $result->items(),
                'meta' => [
                    'current_page' => $result->currentPage(),
                    'last_page' => $result->lastPage(),
                    'per_page' => $result->perPage(),
                    'total' => $result->total(),
                    'from' => $result->firstItem(),
                    'to' => $result->lastItem(),
                ]
            ];
        }

        return [
            'data' => $query->get(),
            'meta' => null
        ];
    }

    /**
     * Find track by ID
     */
    public function findById(int $trackId): ?Track
    {
        return $this->model->with(['supervisor', 'teams', 'ideas'])->find($trackId);
    }

    /**
     * Create a new track
     */
    public function create(array $trackData): Track
    {
        return $this->model->create($trackData);
    }

    /**
     * Update an existing track and return the updated model
     */
    public function updateAndReturn(int $trackId, array $trackData): Track
    {
        $track = $this->model->findOrFail($trackId);
        $track->update($trackData);
        return $track->fresh(['supervisor', 'teams', 'ideas']);
    }

    /**
     * Delete a track
     */
    public function delete(int $trackId): bool
    {
        $track = $this->model->findOrFail($trackId);
        return $track->delete();
    }

    /**
     * Get track statistics
     */
    public function getStatistics(array $filters = []): array
    {
        $query = $this->model;

        // Apply hackathon filter
        if (!empty($filters['hackathon_id'])) {
            $query->where('hackathon_id', $filters['hackathon_id']);
        }

        $totalTracks = $query->count();
        $activeTracks = $query->where('status', 'active')->count();
        $inactiveTracks = $query->where('status', 'inactive')->count();

        // Get tracks with most teams
        $tracksWithMostTeams = $this->model
            ->withCount('teams')
            ->orderBy('teams_count', 'desc')
            ->limit(5)
            ->get();

        // Get tracks with most ideas
        $tracksWithMostIdeas = $this->model
            ->withCount('ideas')
            ->orderBy('ideas_count', 'desc')
            ->limit(5)
            ->get();

        return [
            'total_tracks' => $totalTracks,
            'active_tracks' => $activeTracks,
            'inactive_tracks' => $inactiveTracks,
            'tracks_with_most_teams' => $tracksWithMostTeams,
            'tracks_with_most_ideas' => $tracksWithMostIdeas,
        ];
    }

    /**
     * Get tracks for dropdown/select
     */
    public function getTracksForSelect(array $filters = []): Collection
    {
        $query = $this->model->where('status', 'active');

        // Apply hackathon filter
        if (!empty($filters['hackathon_id'])) {
            $query->where('hackathon_id', $filters['hackathon_id']);
        }

        return $query->select('id', 'name')->orderBy('name')->get();
    }

    /**
     * Check if track name exists
     */
    public function trackNameExists(string $name, int $hackathonId, ?int $excludeId = null): bool
    {
        $query = $this->model->where('name', $name)
                            ->where('hackathon_id', $hackathonId);

        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        return $query->exists();
    }

    /**
     * Get tracks by hackathon ID with statistics.
     */
    public function getByHackathon(int $hackathonId, array $filters = [])
    {
        $query = $this->model->where('hackathon_id', $hackathonId)
            ->withCount([
                'ideas',
                'ideas as submitted_ideas_count' => function ($query) {
                    $query->whereIn('status', ['submitted', 'under_review', 'accepted', 'rejected']);
                },
                'ideas as accepted_ideas_count' => function ($query) {
                    $query->where('status', 'accepted');
                }
            ])
            ->with(['supervisor']);

        if (!empty($filters['search'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('name', 'like', "%{$filters['search']}%")
                  ->orWhere('description', 'like', "%{$filters['search']}%");
            });
        }

        return $query->get();
    }

    /**
     * Get track with supervisor relationship.
     */
    public function getWithSupervisor(int $trackId)
    {
        return $this->model->with(['supervisor', 'hackathon'])->find($trackId);
    }

    /**
     * Get track statistics including idea counts by status.
     */
    public function getTrackStatistics(int $trackId): array
    {
        $track = $this->model->withCount([
            'teams',
            'ideas',
            'ideas as draft_ideas_count' => function ($query) {
                $query->where('status', 'draft');
            },
            'ideas as submitted_status_count' => function ($query) {
                $query->where('status', 'submitted');
            },
            'ideas as under_review_count' => function ($query) {
                $query->where('status', 'under_review');
            },
            'ideas as needs_revision_count' => function ($query) {
                $query->where('status', 'needs_revision');
            },
            'ideas as accepted_status_count' => function ($query) {
                $query->where('status', 'accepted');
            },
            'ideas as rejected_count' => function ($query) {
                $query->where('status', 'rejected');
            },
            'ideas as submitted_ideas_count' => function ($query) {
                $query->whereIn('status', ['submitted', 'under_review', 'accepted', 'rejected']);
            },
            'ideas as accepted_ideas_count' => function ($query) {
                $query->where('status', 'accepted');
            }
        ])->find($trackId);

        if (!$track) {
            return [];
        }

        $averageScore = $track->ideas()->whereNotNull('score')->avg('score') ?? 0;

        return [
            'teams_count' => $track->teams_count,
            'ideas_count' => $track->ideas_count,
            'draft_ideas_count' => $track->draft_ideas_count,
            'submitted_status_count' => $track->submitted_status_count,
            'under_review_count' => $track->under_review_count,
            'needs_revision_count' => $track->needs_revision_count,
            'accepted_status_count' => $track->accepted_status_count,
            'rejected_count' => $track->rejected_count,
            'submitted_ideas_count' => $track->submitted_ideas_count,
            'accepted_ideas_count' => $track->accepted_ideas_count,
            'average_score' => round($averageScore, 2),
        ];
    }

    /**
     * Get ideas for a track with optional filters.
     */
    public function getIdeasByTrack(int $trackId, array $filters = [])
    {
        $track = $this->model->find($trackId);
        if (!$track) {
            return collect();
        }

        $query = $track->ideas()
            ->with(['team' => function ($q) {
                $q->with(['leader', 'acceptedMembers.user']);
            }]);

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        return $query->orderBy('score', 'desc')->get();
    }
}
