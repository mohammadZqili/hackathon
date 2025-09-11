<?php

namespace App\Repositories;

use App\Repositories\Contracts\BaseRepositoryInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;

abstract class BaseRepository implements BaseRepositoryInterface
{
    protected Model $model;

    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    public function all(array $columns = ['*']): Collection
    {
        return $this->model->select($columns)->get();
    }

    public function find(string $id, array $columns = ['*']): ?Model
    {
        return $this->model->select($columns)->find($id);
    }

    public function findOrFail(string $id, array $columns = ['*']): Model
    {
        return $this->model->select($columns)->findOrFail($id);
    }

    public function create(array $data): Model
    {
        return $this->model->create($data);
    }

    public function update(string $id, array $data): bool
    {
        return $this->model->whereId($id)->update($data);
    }

    public function delete(string $id): bool
    {
        return $this->model->destroy($id);
    }

    public function paginate(int $perPage = 15, array $columns = ['*']): LengthAwarePaginator
    {
        return $this->model->select($columns)->paginate($perPage);
    }

    public function findBy(string $field, mixed $value, array $columns = ['*']): ?Model
    {
        return $this->model->select($columns)->where($field, $value)->first();
    }

    public function findManyBy(string $field, mixed $value, array $columns = ['*']): Collection
    {
        return $this->model->select($columns)->where($field, $value)->get();
    }

    public function count(): int
    {
        return $this->model->count();
    }

    public function exists(string $id): bool
    {
        return $this->model->where('id', $id)->exists();
    }

    /**
     * Find by multiple conditions
     */
    public function findByQuery(array $conditions, array $columns = ['*']): ?Model
    {
        $query = $this->model->select($columns);
        
        foreach ($conditions as $field => $value) {
            if (is_array($value)) {
                $query->whereIn($field, $value);
            } else {
                $query->where($field, $value);
            }
        }
        
        return $query->first();
    }

    /**
     * Get query builder for complex queries
     */
    protected function query(): Builder
    {
        return $this->model->newQuery();
    }

    /**
     * Get model instance
     */
    public function getModel(): Model
    {
        return $this->model;
    }

    /**
     * Set model instance
     */
    public function setModel(Model $model): self
    {
        $this->model = $model;
        return $this;
    }
}
