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

    public function updateStatus(int $userId, bool $isActive): bool
    {
        return $this->model->whereId($userId)->update(['is_active' => $isActive]);
    }

    public function updateLastLogin(int $userId): bool
    {
        return $this->model->whereId($userId)->update(['last_login_at' => now()]);
    }

    public function searchUsers(string $term): Collection
    {
        return $this->model
            ->where('name', 'like', '%' . $term . '%')
            ->orWhere('email', 'like', '%' . $term . '%')
            ->limit(10)
            ->get();
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
}
