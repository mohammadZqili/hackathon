<?php

namespace App\Repositories;

use App\Models\Speaker;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class SpeakerRepository extends BaseRepository
{
    public function __construct()
    {
        parent::__construct(new Speaker());
    }

    public function getPaginatedWithFilters(array $filters, int $perPage = 15): LengthAwarePaginator
    {
        $query = $this->query()
            ->with(['organization', 'workshops']);

        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('expertise', 'like', "%{$search}%");
            });
        }

        if (!empty($filters['organization_id'])) {
            $query->where('organization_id', $filters['organization_id']);
        }

        if (!empty($filters['edition_id'])) {
            $query->whereHas('workshops', function ($q) use ($filters) {
                $q->where('edition_id', $filters['edition_id']);
            });
        }

        if (!empty($filters['force_empty'])) {
            $query->whereRaw('1 = 0');
        }

        return $query->latest()->paginate($perPage)->withQueryString();
    }

    public function findWithFullDetails(int $id): ?Speaker
    {
        return $this->query()
            ->with(['organization', 'workshops.edition'])
            ->find($id);
    }

    public function getStatistics(array $filters = []): array
    {
        $query = $this->query();

        if (!empty($filters['edition_id'])) {
            $query->whereHas('workshops', function ($q) use ($filters) {
                $q->where('edition_id', $filters['edition_id']);
            });
        }

        return [
            'total' => $query->count(),
            'with_workshops' => (clone $query)->has('workshops')->count(),
            'active' => (clone $query)->where('is_active', true)->count(),
        ];
    }

    public function hasDependencies(int $id): bool
    {
        $speaker = $this->find($id);
        return $speaker && $speaker->workshops()->exists();
    }
}
