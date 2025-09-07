<?php

namespace App\Repositories;

use App\Models\Team;
use App\Models\User;
use App\Repositories\Contracts\TeamRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class TeamRepository extends BaseRepository implements TeamRepositoryInterface
{
    public function __construct(Team $model)
    {
        parent::__construct($model);
    }

    public function findByInviteCode(string $inviteCode): ?Team
    {
        return $this->model->where('invite_code', $inviteCode)->first();
    }

    public function getTeamsForHackathon(int $hackathonId): Collection
    {
        return $this->model->where('hackathon_id', $hackathonId)
            ->with(['leader', 'track'])
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function getTeamsByLeader(int $leaderId): Collection
    {
        return $this->model->where('leader_id', $leaderId)
            ->with(['hackathon', 'track'])
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Get all teams for a user (as leader or member)
     */
    public function getTeamsForUser($userId): Collection
    {
        $userId = (int) $userId; // Cast to integer to handle string IDs
        
        return $this->model->where(function ($query) use ($userId) {
            $query->where('leader_id', $userId)
                  ->orWhereHas('members', function ($q) use ($userId) {
                      $q->where('user_id', $userId)
                        ->whereIn('status', ['accepted', 'pending']);
                  });
        })
        ->with(['hackathon', 'track', 'leader', 'members'])
        ->withCount('acceptedMembers')
        ->orderBy('created_at', 'desc')
        ->get();
    }

    public function getTeamsWithMember(int $userId): Collection
    {
        return $this->model->whereHas('members', function ($query) use ($userId) {
            $query->where('user_id', $userId)
                  ->where('status', 'accepted');
        })
        ->with(['hackathon', 'track', 'leader'])
        ->orderBy('created_at', 'desc')
        ->get();
    }

    public function getPaginatedWithFilters(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = $this->model->newQuery();

        if (!empty($filters['search'])) {
            $query->where('name', 'like', '%' . $filters['search'] . '%');
        }

        if (!empty($filters['hackathon_id'])) {
            $query->where('hackathon_id', $filters['hackathon_id']);
        }

        if (!empty($filters['track_id'])) {
            $query->where('track_id', $filters['track_id']);
        }

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['leader_name'])) {
            $query->whereHas('leader', function ($q) use ($filters) {
                $q->where('name', 'like', '%' . $filters['leader_name'] . '%');
            });
        }

        return $query->with(['leader', 'hackathon', 'track'])
            ->withCount('acceptedMembers')
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    public function addMember(int $teamId, int $userId, string $status = 'pending'): bool
    {
        $team = $this->find($teamId);
        if (!$team) {
            return false;
        }

        // Check if user is already a member
        if ($team->members()->where('user_id', $userId)->exists()) {
            return false;
        }

        $team->members()->attach($userId, [
            'status' => $status,
            'role' => 'member',
            'joined_at' => $status === 'accepted' ? now() : null,
        ]);

        return true;
    }

    public function removeMember(int $teamId, int $userId): bool
    {
        $team = $this->find($teamId);
        if (!$team) {
            return false;
        }

        return $team->members()->detach($userId);
    }

    public function acceptMember(int $teamId, int $userId): bool
    {
        $team = $this->find($teamId);
        if (!$team) {
            return false;
        }

        return $team->members()->updateExistingPivot($userId, [
            'status' => 'accepted',
            'joined_at' => now(),
        ]);
    }

    public function rejectMember(int $teamId, int $userId): bool
    {
        $team = $this->find($teamId);
        if (!$team) {
            return false;
        }

        return $team->members()->updateExistingPivot($userId, [
            'status' => 'rejected',
        ]);
    }

    public function getTeamWithRelations(int $teamId): ?Team
    {
        return $this->model->with([
            'leader',
            'hackathon',
            'track',
            'members',
            'acceptedMembers',
            'pendingMembers',
            'idea.files',
            'idea.auditLogs'
        ])->find($teamId);
    }

    public function canUserJoinTeam(int $teamId, int $userId): bool
    {
        $team = $this->find($teamId);
        
        if (!$team) {
            return false;
        }

        // Check if team is full
        if ($team->isFull()) {
            return false;
        }

        // Check if user is already a member
        if ($team->members()->where('user_id', $userId)->exists()) {
            return false;
        }

        // Check if hackathon registration is still open
        if (!$team->hackathon->isRegistrationOpen()) {
            return false;
        }

        return true;
    }

    public function getMembersCount(int $teamId): int
    {
        $team = $this->find($teamId);
        return $team ? $team->acceptedMembers()->count() : 0;
    }

    /**
     * Get teams with idea submission status
     */
    public function getTeamsWithIdeaStatus(int $hackathonId): Collection
    {
        return $this->model->where('hackathon_id', $hackathonId)
            ->with(['leader', 'track', 'idea'])
            ->withCount('acceptedMembers')
            ->get();
    }

    /**
     * Get teams by track
     */
    public function getTeamsByTrack(int $trackId): Collection
    {
        return $this->model->where('track_id', $trackId)
            ->with(['leader', 'idea'])
            ->withCount('acceptedMembers')
            ->get();
    }

    /**
     * Update team status
     */
    public function updateStatus(int $teamId, string $status): bool
    {
        return $this->model->whereId($teamId)->update(['status' => $status]);
    }

    /**
     * Get team statistics for hackathon
     */
    public function getHackathonTeamStats(int $hackathonId): array
    {
        $teams = $this->getTeamsForHackathon($hackathonId);
        
        $stats = [
            'total_teams' => $teams->count(),
            'active_teams' => $teams->where('status', 'active')->count(),
            'submitted_teams' => $teams->where('status', 'submitted')->count(),
            'total_participants' => 0,
            'teams_with_ideas' => 0,
            'ideas_by_status' => [
                'draft' => 0,
                'submitted' => 0,
                'accepted' => 0,
                'rejected' => 0,
                'needs_revision' => 0
            ]
        ];

        foreach ($teams as $team) {
            $stats['total_participants'] += $team->acceptedMembers()->count();
            
            if ($team->idea) {
                $stats['teams_with_ideas']++;
                $status = $team->idea->status;
                if (isset($stats['ideas_by_status'][$status])) {
                    $stats['ideas_by_status'][$status]++;
                }
            }
        }

        return $stats;
    }
}
