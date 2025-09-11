<?php

namespace App\Repositories\Contracts;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;

interface BaseRepositoryInterface
{
    /**
     * Get all records
     */
    public function all(array $columns = ['*']): Collection;

    /**
     * Find record by ID
     */
    public function find(string $id, array $columns = ['*']): ?Model;

    /**
     * Find record by ID or fail
     */
    public function findOrFail(string $id, array $columns = ['*']): Model;

    /**
     * Create new record
     */
    public function create(array $data): Model;

    /**
     * Update record
     */
    public function update(string $id, array $data): bool;

    /**
     * Delete record
     */
    public function delete(string $id): bool;

    /**
     * Get paginated records
     */
    public function paginate(int $perPage = 15, array $columns = ['*']): LengthAwarePaginator;

    /**
     * Find by specific field
     */
    public function findBy(string $field, mixed $value, array $columns = ['*']): ?Model;

    /**
     * Find multiple by specific field
     */
    public function findManyBy(string $field, mixed $value, array $columns = ['*']): Collection;

    /**
     * Count records
     */
    public function count(): int;

    /**
     * Check if record exists
     */
    public function exists(string $id): bool;

    /**
     * Find by multiple conditions
     */
    public function findByQuery(array $conditions, array $columns = ['*']): ?Model;
}
