<?php
namespace App\Services\Contracts;

use App\Models\Team;
use App\Models\User;
use Illuminate\Support\Collection;

interface TeamServiceInterface
{
    public function createTeam(array $data, User $leader): Team;
    public function joinTeamByCode(string $code, User $user): array;
    public function acceptMember(int $teamId, int $userId, User $leader): bool;
    public function rejectMember(int $teamId, int $userId, User $leader): bool;
    public function removeFromTeam(int $teamId, int $userId, User $leader): bool;
    public function getUserTeams(User $user): Collection;
    public function canUserCreateTeam(User $user): bool;
    public function regenerateInviteCode(int $teamId, User $leader): string;
}
