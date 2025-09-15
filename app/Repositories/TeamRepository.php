<?php

namespace App\Repositories;

use App\Models\Team;
use App\Models\TeamMember;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class TeamRepository extends BaseRepository
{
    public function __construct()
    {
        parent::__construct(new Team());
    }

    /**
     * Get paginated teams with filters
     */
    public function getPaginatedWithFilters(array $filters, int $perPage = 15): LengthAwarePaginator
    {
        $query = $this->query()
            ->with(['leader', 'members', 'track', 'edition', 'idea'])
            ->withCount(['members']);

        // Apply filters
        if (!empty($filters['edition_id'])) {
            $query->where('edition_id', $filters['edition_id']);
        }

        if (!empty($filters['track_id'])) {
            $query->where('track_id', $filters['track_id']);
        }

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhereHas('leader', function ($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%");
                  });
            });
        }

        if (!empty($filters['team_ids'])) {
            $query->whereIn('id', $filters['team_ids']);
        }

        return $query->latest()->paginate($perPage)->withQueryString();
    }

    /**
     * Get team with full details
     */
    public function findWithFullDetails(string $id): ?Team
    {
        return $this->query()
            ->with(['leader', 'members', 'track', 'edition', 'idea'])
            ->withCount(['members'])
            ->find($id);
    }

    /**
     * Get statistics for teams
     */
    public function getStatistics(array $filters = []): array
    {
        $query = $this->query();

        // Apply same filters as listing
        if (!empty($filters['edition_id'])) {
            $query->where('edition_id', $filters['edition_id']);
        }

        if (!empty($filters['track_id'])) {
            $query->where('track_id', $filters['track_id']);
        }

        if (!empty($filters['team_ids'])) {
            $query->whereIn('id', $filters['team_ids']);
        }

        return [
            'total' => $query->count(),
            'active' => (clone $query)->where('status', 'active')->count(),
            'pending' => (clone $query)->where('status', 'pending')->count(),
            'disqualified' => (clone $query)->where('status', 'disqualified')->count(),
            'with_ideas' => (clone $query)->has('idea')->count(),
            'total_members' => TeamMember::whereIn('team_id', (clone $query)->pluck('id'))->count(),
        ];
    }

    /**
     * Find team by leader
     */
    public function findByLeaderId(string $userId): ?Team
    {
        return $this->query()
            ->where('leader_id', $userId)
            ->with(['members', 'track', 'edition'])
            ->first();
    }

    /**
     * Find member's team
     */
    public function findMemberTeam(string $userId): ?TeamMember
    {
        return TeamMember::where('user_id', $userId)
            ->with('team.members', 'team.track', 'team.edition')
            ->first();
    }

    /**
     * Add member to team
     */
    public function addMember(string $teamId, array $memberData): TeamMember
    {
        return TeamMember::create([
            'team_id' => $teamId,
            'user_id' => $memberData['user_id'],
            'role' => $memberData['role'] ?? 'member',
            'joined_at' => $memberData['joined_at'] ?? now()
        ]);
    }

    /**
     * Remove member from team
     */
    public function removeMember(string $teamId, string $userId): bool
    {
        return TeamMember::where('team_id', $teamId)
            ->where('user_id', $userId)
            ->delete() > 0;
    }

    /**
     * Get teams for export
     */
    public function getForExport(array $filters = []): Collection
    {
        $query = $this->query()
            ->with(['leader', 'members', 'track', 'edition'])
            ->withCount(['members']);

        if (!empty($filters['edition_id'])) {
            $query->where('edition_id', $filters['edition_id']);
        }

        if (!empty($filters['track_id'])) {
            $query->where('track_id', $filters['track_id']);
        }

        if (!empty($filters['team_ids'])) {
            $query->whereIn('id', $filters['team_ids']);
        }

        return $query->get();
    }

    /**
     * Check if team has dependencies
     */
    public function hasDependencies(string $id): bool
    {
        $team = $this->find($id);
        if (!$team) {
            return false;
        }

        return $team->idea()->exists() || $team->members()->exists();
    }

    /**
     * Update team status
     */
    public function updateStatus(string $id, string $status): bool
    {
        return $this->update($id, ['status' => $status]);
    }

    /**
     * Get available teams for registration
     */
    public function getAvailableForRegistration(string $editionId): Collection
    {
        return $this->query()
            ->where('edition_id', $editionId)
            ->where('status', 'active')
            ->whereRaw('(max_members IS NULL OR (SELECT COUNT(*) FROM team_members WHERE team_members.team_id = teams.id) < max_members)')
            ->with(['members'])
            ->withCount('members')
            ->get();
    }

    /**
     * Get teams by track
     */
    public function getByTrack(string $trackId): Collection
    {
        return $this->query()
            ->where('track_id', $trackId)
            ->with(['leader', 'members', 'idea'])
            ->get();
    }

    /**
     * Check if user is in any team
     */
    public function userHasTeam(string $userId): bool
    {
        return $this->query()->where('leader_id', $userId)->exists() ||
               TeamMember::where('user_id', $userId)->exists();
    }

    /**
     * Get user's team (as leader or member)
     */
    public function getUserTeam(string $userId): ?Team
    {
        // First check if user is a leader
        $team = $this->findByLeaderId($userId);
        if ($team) {
            return $team;
        }

        // Then check if user is a member
        $membership = $this->findMemberTeam($userId);
        if ($membership) {
            return $membership->team;
        }

        return null;
    }

    /**
     * Count teams with filters
     */
    public function count(array $filters = []): int
    {
        $query = $this->query();

        if (!empty($filters['edition_id'])) {
            $query->where('edition_id', $filters['edition_id']);
        }

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['created_date'])) {
            $query->whereDate('created_at', $filters['created_date']);
        }

        if (!empty($filters['force_empty'])) {
            $query->whereRaw('1 = 0');
        }

        return $query->count();
    }

    /**
     * Get recent teams
     */
    public function getRecent(int $limit, array $filters = []): Collection
    {
        $query = $this->query()->with(['leader', 'edition']);

        if (!empty($filters['edition_id'])) {
            $query->where('edition_id', $filters['edition_id']);
        }

        if (!empty($filters['force_empty'])) {
            $query->whereRaw('1 = 0');
        }

        return $query->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Get all teams that a user is part of (as leader or member)
     */
    public function getTeamsForUser(string $userId): Collection
    {
        return $this->model
            ->where('leader_id', $userId)
            ->orWhereHas('members', function ($query) use ($userId) {
                $query->where('user_id', $userId);
            })
            ->with(['leader', 'members', 'edition'])
            ->get();
    }
}
