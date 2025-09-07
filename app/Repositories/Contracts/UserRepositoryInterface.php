<?php

namespace App\Repositories\Contracts;

use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

interface UserRepositoryInterface extends BaseRepositoryInterface
{
    /**
     * Find user by email
     */
    public function findByEmail(string $email): ?User;

    /**
     * Find user by national ID
     */
    public function findByNationalId(string $nationalId): ?User;

    /**
     * Get users by type
     */
    public function getUsersByType(string $userType): Collection;

    /**
     * Get paginated users with filters
     */
    public function getPaginatedWithFilters(array $filters = [], int $perPage = 15): LengthAwarePaginator;

    /**
     * Get active users
     */
    public function getActiveUsers(): Collection;

    /**
     * Get inactive users
     */
    public function getInactiveUsers(): Collection;

    /**
     * Update user status
     */
    public function updateStatus(int $userId, bool $isActive): bool;

    /**
     * Update last login time
     */
    public function updateLastLogin(int $userId): bool;

    /**
     * Search users by name or email
     */
    public function searchUsers(string $term): Collection;
}
