<?php

namespace App\Repositories;

use App\Models\User;
use App\Repositories\Contracts\UserRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class UserRepository extends BaseRepository implements UserRepositoryInterface
{
    public function __construct(User $model)
    {
        parent::__construct($model);
    }

    public function findByEmail(string $email): ?User
    {
        return $this->model->where('email', $email)->first();
    }

    public function findByNationalId(string $nationalId): ?User
    {
        return $this->model->where('national_id', $nationalId)->first();
    }

    public function getUsersByType(string $userType): Collection
    {
        return $this->model->where('user_type', $userType)->get();
    }

    public function getPaginatedWithFilters(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = $this->model->newQuery();

        if (!empty($filters['search'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('name', 'like', '%' . $filters['search'] . '%')
                  ->orWhere('email', 'like', '%' . $filters['search'] . '%');
            });
        }

        if (!empty($filters['user_type'])) {
            $query->where('user_type', $filters['user_type']);
        }

        if (isset($filters['is_active'])) {
            $query->where('is_active', $filters['is_active']);
        }

        if (!empty($filters['occupation'])) {
            $query->where('occupation', $filters['occupation']);
        }

        return $query->orderBy('created_at', 'desc')->paginate($perPage);
    }

    public function getActiveUsers(): Collection
    {
        return $this->model->where('is_active', true)->get();
    }

    public function getInactiveUsers(): Collection
    {
        return $this->model->where('is_active', false)->get();
    }

    public function updateStatus(string $userId, bool $isActive): bool
    {
        return $this->model->whereId($userId)->update(['is_active' => $isActive]);
    }

    public function updateLastLogin(string $userId): bool
    {
        return $this->model->whereId($userId)->update(['last_login_at' => now()]);
    }


    /**
     * Get team leaders
     */
    public function getTeamLeaders(): Collection
    {
        return $this->model->where('user_type', 'team_leader')->get();
    }

    /**
     * Get track supervisors
     */
    public function getTrackSupervisors(): Collection
    {
        return $this->model->where('user_type', 'track_supervisor')->get();
    }

    /**
     * Get hackathon admins
     */
    public function getHackathonAdmins(): Collection
    {
        return $this->model->where('user_type', 'hackathon_admin')->get();
    }

    /**
     * Create user with role assignment
     */
    public function createWithRole(array $userData, string $role): User
    {
        $user = $this->create($userData);
        $user->assignRole($role);
        return $user;
    }

    /**
     * Get users with roles
     */
    public function getUsersWithRoles(): Collection
    {
        return $this->model->with('roles')->get();
    }

    /**
     * Get user with full details
     */
    public function findWithFullDetails(string $id): ?User
    {
        return $this->model->with(['teams', 'ideas'])->find($id);
    }

    /**
     * Get statistics
     */
    public function getStatistics(array $filters = []): array
    {
        $query = $this->model->newQuery();

        if (!empty($filters['edition_id'])) {
            $query->where('edition_id', $filters['edition_id']);
        }

        if (!empty($filters['force_empty'])) {
            $query->whereRaw('1 = 0');
        }

        return [
            'total' => $query->count(),
            'active' => (clone $query)->where('is_active', true)->count(),
            'inactive' => (clone $query)->where('is_active', false)->count(),
            'team_leaders' => (clone $query)->where('user_type', 'team_leader')->count(),
            'team_members' => (clone $query)->where('user_type', 'team_member')->count(),
            'visitors' => (clone $query)->where('user_type', 'visitor')->count(),
        ];
    }

    /**
     * Check if user has dependencies
     */
    public function hasDependencies(string $id): bool
    {
        $user = $this->find($id);
        if (!$user) {
            return false;
        }

        // Check if user is a team leader
        if (\DB::table('teams')->where('leader_id', $id)->exists()) {
            return true;
        }

        // Check if user has submitted ideas
        if (\DB::table('ideas')->where('submitted_by', $id)->exists()) {
            return true;
        }

        return false;
    }

    /**
     * Search users by name or email (interface method)
     */
    public function searchUsers(string $term): Collection
    {
        return $this->model
            ->where('name', 'like', '%' . $term . '%')
            ->orWhere('email', 'like', '%' . $term . '%')
            ->limit(10)
            ->get();
    }

    /**
     * Search users with advanced filters
     */
    public function searchUsersWithFilters(array $filters, int $limit = 10): Collection
    {
        $query = $this->model->newQuery();

        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                  ->orWhere('email', 'like', '%' . $search . '%');
            });
        }

        if (!empty($filters['edition_id'])) {
            $query->where('edition_id', $filters['edition_id']);
        }

        if (!empty($filters['user_types'])) {
            $query->whereIn('user_type', $filters['user_types']);
        }

        if (!empty($filters['exclude_user_types'])) {
            $query->whereNotIn('user_type', $filters['exclude_user_types']);
        }

        if (!empty($filters['team_ids'])) {
            $query->whereHas('teams', function ($q) use ($filters) {
                $q->whereIn('team_id', $filters['team_ids']);
            });
        }

        if (!empty($filters['force_empty'])) {
            $query->whereRaw('1 = 0');
        }

        return $query->select('id', 'name', 'email', 'user_type')
            ->limit($limit)
            ->get()
            ->map(function ($user) {
                $team = \DB::table('team_members')
                    ->join('teams', 'team_members.team_id', '=', 'teams.id')
                    ->where('team_members.user_id', $user->id)
                    ->select('teams.id', 'teams.name')
                    ->first();
                
                if ($team) {
                    $user->team_id = $team->id;
                    $user->team_name = $team->name;
                }
                
                return $user;
            });
    }

    /**
     * Get users for export
     */
    public function getForExport(array $filters = []): Collection
    {
        $query = $this->model->with(['teams', 'edition']);

        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                  ->orWhere('email', 'like', '%' . $search . '%');
            });
        }

        if (!empty($filters['user_type'])) {
            $query->where('user_type', $filters['user_type']);
        }

        if (!empty($filters['edition_id'])) {
            $query->where('edition_id', $filters['edition_id']);
        }

        if (!empty($filters['status'])) {
            $query->where('is_active', $filters['status'] === 'active');
        }

        if (!empty($filters['exclude_user_types'])) {
            $query->whereNotIn('user_type', $filters['exclude_user_types']);
        }

        if (!empty($filters['user_types'])) {
            $query->whereIn('user_type', $filters['user_types']);
        }

        if (!empty($filters['team_ids'])) {
            $query->whereHas('teams', function ($q) use ($filters) {
                $q->whereIn('team_id', $filters['team_ids']);
            });
        }

        if (!empty($filters['force_empty'])) {
            $query->whereRaw('1 = 0');
        }

        return $query->get();
    }

    /**
     * Count users with filters
     */
    public function count(array $filters = []): int
    {
        $query = $this->model->newQuery();

        if (!empty($filters['edition_id'])) {
            $query->where('edition_id', $filters['edition_id']);
        }

        if (!empty($filters['is_active'])) {
            $query->where('is_active', $filters['is_active']);
        }

        if (!empty($filters['created_date'])) {
            $query->whereDate('created_at', $filters['created_date']);
        }

        if (!empty($filters['created_after'])) {
            $query->where('created_at', '>=', $filters['created_after']);
        }

        if (!empty($filters['force_empty'])) {
            $query->whereRaw('1 = 0');
        }

        return $query->count();
    }

    /**
     * Get recent users
     */
    public function getRecent(int $limit, array $filters = []): Collection
    {
        $query = $this->model->newQuery();

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
}
