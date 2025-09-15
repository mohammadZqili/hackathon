<?php

namespace App\Repositories;

use App\Models\Organization;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class OrganizationRepository extends BaseRepository
{
    public function __construct()
    {
        parent::__construct(new Organization());
    }

    public function getAllOrderedByName()
    {
        return $this->model->orderBy('name')->get();
    }

    public function getPaginatedWithFilters(array $filters, int $perPage = 15): LengthAwarePaginator
    {
        $query = $this->query()
            ->withCount('speakers');

        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('website', 'like', "%{$search}%");
            });
        }

        if (!empty($filters['type'])) {
            $query->where('type', $filters['type']);
        }

        if (!empty($filters['force_empty'])) {
            $query->whereRaw('1 = 0');
        }

        return $query->latest()->paginate($perPage)->withQueryString();
    }

    public function findWithSpeakers(int $id)
    {
        return $this->model->with('speakers')->find($id);
    }

    public function findWithFullDetails(int $id): ?Organization
    {
        return $this->query()
            ->with(['speakers'])
            ->withCount('speakers')
            ->find($id);
    }

    public function getStatistics(array $filters = []): array
    {
        $query = $this->query();

        return [
            'total' => $query->count(),
            'companies' => (clone $query)->where('type', 'company')->count(),
            'universities' => (clone $query)->where('type', 'university')->count(),
            'sponsors' => (clone $query)->where('type', 'sponsor')->count(),
        ];
    }

    public function hasDependencies(int $id): bool
    {
        $org = $this->find($id);
        return $org && $org->speakers()->exists();
    }
}
