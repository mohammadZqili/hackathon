<?php

namespace App\Repositories;

use App\Models\Report;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class ReportRepository extends BaseRepository
{
    public function __construct()
    {
        parent::__construct(new Report());
    }

    /**
     * Get paginated report_PLURAL with filters
     */
    public function getPaginatedWithFilters(array $filters, int $perPage = 15): LengthAwarePaginator
    {
        $query = $this->query();

        // Add relationships based on entity
        $this->addRelationships($query);

        // Apply filters
        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $this->applySearchFilters($q, $search);
            });
        }

        if (!empty($filters['edition_id'])) {
            $this->applyEditionFilter($query, $filters['edition_id']);
        }

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['force_empty'])) {
            $query->whereRaw('1 = 0');
        }

        return $query->latest()->paginate($perPage)->withQueryString();
    }

    /**
     * Find with full details
     */
    public function findWithFullDetails(int $id): ?Report
    {
        $query = $this->query();
        $this->addRelationships($query);
        return $query->find($id);
    }

    /**
     * Get statistics
     */
    public function getStatistics(array $filters = []): array
    {
        $query = $this->query();

        // Apply filters
        if (!empty($filters['edition_id'])) {
            $this->applyEditionFilter($query, $filters['edition_id']);
        }

        return [
            'total' => $query->count(),
            'active' => (clone $query)->where('status', 'active')->count(),
            'inactive' => (clone $query)->where('status', 'inactive')->count(),
        ];
    }

    /**
     * Get for export
     */
    public function getForExport(array $filters = []): Collection
    {
        $query = $this->query();
        $this->addRelationships($query);

        if (!empty($filters['edition_id'])) {
            $this->applyEditionFilter($query, $filters['edition_id']);
        }

        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $this->applySearchFilters($q, $search);
            });
        }

        return $query->get();
    }

    /**
     * Check if entity has dependencies
     */
    public function hasDependencies(int $id): bool
    {
        // Override in specific repository to check for actual dependencies
        return false;
    }

    /**
     * Add relationships to query
     */
    protected function addRelationships($query): void
    {
        // Override in specific repository to add relationships
    }

    /**
     * Apply search filters
     */
    protected function applySearchFilters($query, string $search): void
    {
        // Override in specific repository for entity-specific search
        $query->where('name', 'like', "%{$search}%");
    }

    /**
     * Apply edition filter
     */
    protected function applyEditionFilter($query, $editionId): void
    {
        // Override in specific repository if entity has edition relationship
        if ($this->model->getTable() !== 'hackathon_editions') {
            $query->where('edition_id', $editionId);
        }
    }
}
