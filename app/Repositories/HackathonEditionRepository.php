<?php

namespace App\Repositories;

use App\Models\HackathonEdition;
use App\Repositories\Contracts\HackathonEditionRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class HackathonEditionRepository extends BaseRepository implements HackathonEditionRepositoryInterface
{
    public function __construct(HackathonEdition $model)
    {
        parent::__construct($model);
    }

    /**
     * Get all editions with creator relationship
     */
    public function getAllWithCreator(int $perPage = 15): LengthAwarePaginator
    {
        return $this->model
            ->with(['creator'])
            ->orderBy('year', 'desc')
            ->paginate($perPage);
    }

    /**
     * Get edition with all relationships
     */
    public function getWithRelations(int $id): ?HackathonEdition
    {
        return $this->model
            ->with(['creator', 'workshops', 'teams', 'tracks', 'news'])
            ->find($id);
    }

    /**
     * Get edition for editing with necessary relationships
     */
    public function getForEdit(int $id): ?HackathonEdition
    {
        return $this->model
            ->with(['creator', 'workshops'])
            ->find($id);
    }

    /**
     * Get current edition
     */
    public function getCurrentEdition(): ?HackathonEdition
    {
        return $this->model
            ->where('is_current', true)
            ->first();
    }

    /**
     * Set edition as current
     */
    public function setCurrent(int $id): bool
    {
        // First, unset all editions as current
        $this->model->where('id', '!=', $id)->update(['is_current' => false]);
        
        // Then set the specified edition as current
        return $this->model->where('id', $id)->update(['is_current' => true]);
    }

    /**
     * Archive an edition
     */
    public function archive(int $id): bool
    {
        return $this->model->where('id', $id)->update([
            'status' => 'archived',
            'is_current' => false,
        ]);
    }

    /**
     * Get editions by status
     */
    public function getByStatus(string $status): Collection
    {
        return $this->model
            ->where('status', $status)
            ->orderBy('year', 'desc')
            ->get();
    }

    /**
     * Get editions by year
     */
    public function getByYear(int $year): Collection
    {
        return $this->model
            ->where('year', $year)
            ->get();
    }

    /**
     * Check if slug exists
     */
    public function slugExists(string $slug, ?int $excludeId = null): bool
    {
        $query = $this->model->where('slug', $slug);
        
        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }
        
        return $query->exists();
    }

    /**
     * Generate unique slug
     */
    public function generateUniqueSlug(string $name, int $year, ?int $excludeId = null): string
    {
        $baseSlug = \Illuminate\Support\Str::slug($name . '-' . $year);
        $slug = $baseSlug;
        $counter = 1;
        
        while ($this->slugExists($slug, $excludeId)) {
            $slug = $baseSlug . '-' . $counter;
            $counter++;
        }
        
        return $slug;
    }

    /**
     * Get active editions
     */
    public function getActiveEditions(): Collection
    {
        return $this->model
            ->where('status', 'active')
            ->orderBy('year', 'desc')
            ->get();
    }

    /**
     * Update edition status
     */
    public function updateStatus(int $id, string $status): bool
    {
        return $this->model
            ->where('id', $id)
            ->update(['status' => $status]);
    }

    /**
     * Get editions with workshop count
     */
    public function getWithWorkshopCount(int $perPage = 15): LengthAwarePaginator
    {
        return $this->model
            ->with(['creator'])
            ->withCount(['workshops', 'teams'])
            ->orderBy('year', 'desc')
            ->paginate($perPage);
    }

    /**
     * Get editions for dropdown/select
     */
    public function getForSelect(): Collection
    {
        return $this->model
            ->select('id', 'name', 'year')
            ->orderBy('year', 'desc')
            ->get();
    }

    /**
     * Check if registration is open for an edition
     */
    public function isRegistrationOpen(int $id): bool
    {
        $edition = $this->find($id);
        
        if (!$edition) {
            return false;
        }
        
        return now()->between($edition->registration_start_date, $edition->registration_end_date);
    }

    /**
     * Check if idea submission is open for an edition
     */
    public function isIdeaSubmissionOpen(int $id): bool
    {
        $edition = $this->find($id);
        
        if (!$edition) {
            return false;
        }
        
        return now()->between($edition->idea_submission_start_date, $edition->idea_submission_end_date);
    }
}