<?php

namespace App\Repositories\Contracts;

use App\Models\Team;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

interface TeamRepositoryInterface extends BaseRepositoryInterface
{
    /**
     * Find team by invite code
     */
    public function findByInviteCode(string $inviteCode): ?Team;

    /**
     * Get teams for hackathon
     */
    public function getTeamsForHackathon(int $hackathonId): Collection;

    /**
     * Get teams by leader
     */
    public function getTeamsByLeader(int $leaderId): Collection;

    /**
     * Get all teams for a user (as leader or member)
     */
    public function getTeamsForUser($userId): Collection;

    /**
     * Get teams with member
     */
    public function getTeamsWithMember(int $userId): Collection;

    /**
     * Get paginated teams with filters
     */
    public function getPaginatedWithFilters(array $filters = [], int $perPage = 15): LengthAwarePaginator;

    /**
     * Add member to team
     */
    public function addMember(int $teamId, int $userId, string $status = 'pending'): bool;

    /**
     * Remove member from team
     */
    public function removeMember(int $teamId, int $userId): bool;

    /**
     * Accept member request
     */
    public function acceptMember(int $teamId, int $userId): bool;

    /**
     * Reject member request
     */
    public function rejectMember(int $teamId, int $userId): bool;

    /**
     * Get team with all relationships
     */
    public function getTeamWithRelations(int $teamId): ?Team;

    /**
     * Check if user can join team
     */
    public function canUserJoinTeam(int $teamId, int $userId): bool;

    /**
     * Get team members count
     */
    public function getMembersCount(int $teamId): int;

    /**
     * Get teams with idea submission status
     */
    public function getTeamsWithIdeaStatus(int $hackathonId): Collection;

    /**
     * Get teams by track
     */
    public function getTeamsByTrack(int $trackId): Collection;

    /**
     * Update team status
     */
    public function updateStatus(int $teamId, string $status): bool;

    /**
     * Get team statistics for hackathon
     */
    public function getHackathonTeamStats(int $hackathonId): array;
}
