<?php

namespace App\Services;

use App\Models\User;
use App\Repositories\Contracts\HackathonEditionRepositoryInterface;
use App\Services\Contracts\HackathonEditionServiceInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class HackathonEditionService implements HackathonEditionServiceInterface
{
    public function __construct(
        protected HackathonEditionRepositoryInterface $repository
    ) {}

    /**
     * Get paginated editions for index page
     */
    public function getPaginatedEditions(int $perPage = 15): LengthAwarePaginator
    {
        // The repository already includes withCount for teams and workshops
        return $this->repository->getWithWorkshopCount($perPage);
    }

    /**
     * Get edition details for viewing
     */
    public function getEditionForView(int $id): ?object
    {
        return $this->repository->getWithRelations($id);
    }

    /**
     * Get edition data for editing
     */
    public function getEditionForEdit(int $id): array
    {
        $edition = $this->repository->getForEdit($id);
        
        if (!$edition) {
            return [
                'edition' => null,
                'users' => []
            ];
        }
        
        // Get users with admin roles for the dropdown
        $users = User::role(['system_admin', 'hackathon_admin'])
            ->get(['id', 'name', 'email']);
        
        return [
            'edition' => $edition,
            'users' => $users
        ];
    }

    /**
     * Create a new edition
     */
    public function createEdition(array $data): ?object
    {
        try {
            DB::beginTransaction();
            
            // Add creator
            $data['created_by'] = auth()->id();
            
            // Set default status if not provided
            if (!isset($data['status'])) {
                $data['status'] = 'draft';
            }
            
            // Generate slug if not provided
            if (empty($data['slug'])) {
                $data['slug'] = $this->repository->generateUniqueSlug(
                    $data['name'],
                    $data['year']
                );
            }
            
            // Create the edition
            $edition = $this->repository->create($data);
            
            // If marked as current, update other editions
            if ($data['is_current'] ?? false) {
                $this->repository->setCurrent($edition->id);
            }
            
            DB::commit();
            
            return $edition;
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to create hackathon edition: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Update an existing edition
     */
    public function updateEdition(int $id, array $data): bool
    {
        try {
            DB::beginTransaction();
            
            // Generate slug if not provided
            if (empty($data['slug'])) {
                $data['slug'] = $this->repository->generateUniqueSlug(
                    $data['name'],
                    $data['year'],
                    $id
                );
            }
            
            // Update the edition
            $updated = $this->repository->update($id, $data);
            
            // If marked as current, update other editions
            if ($data['is_current'] ?? false) {
                $this->repository->setCurrent($id);
            }
            
            DB::commit();
            
            return $updated;
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to update hackathon edition: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Delete an edition
     */
    public function deleteEdition(int $id): bool
    {
        try {
            return $this->repository->delete($id);
        } catch (\Exception $e) {
            Log::error('Failed to delete hackathon edition: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Set an edition as current
     */
    public function setCurrentEdition(int $id): bool
    {
        try {
            DB::beginTransaction();
            
            $result = $this->repository->setCurrent($id);
            
            DB::commit();
            
            return $result;
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to set current edition: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Archive an edition
     */
    public function archiveEdition(int $id): bool
    {
        try {
            return $this->repository->archive($id);
        } catch (\Exception $e) {
            Log::error('Failed to archive edition: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Get current edition
     */
    public function getCurrentEdition(): ?object
    {
        return $this->repository->getCurrentEdition();
    }

    /**
     * Get editions by status
     */
    public function getEditionsByStatus(string $status): Collection
    {
        return $this->repository->getByStatus($status);
    }

    /**
     * Get editions by year
     */
    public function getEditionsByYear(int $year): Collection
    {
        return $this->repository->getByYear($year);
    }

    /**
     * Get active editions
     */
    public function getActiveEditions(): Collection
    {
        return $this->repository->getActiveEditions();
    }

    /**
     * Get editions for dropdown selection
     */
    public function getEditionsForSelect(): Collection
    {
        return $this->repository->getForSelect();
    }

    /**
     * Check if registration is open
     */
    public function isRegistrationOpen(int $id): bool
    {
        return $this->repository->isRegistrationOpen($id);
    }

    /**
     * Check if idea submission is open
     */
    public function isIdeaSubmissionOpen(int $id): bool
    {
        return $this->repository->isIdeaSubmissionOpen($id);
    }

    /**
     * Export edition data (placeholder for now)
     */
    public function exportEdition(int $id): array
    {
        $edition = $this->repository->getWithRelations($id);
        
        if (!$edition) {
            throw new \Exception('Edition not found');
        }
        
        // TODO: Implement actual export logic
        return [
            'message' => 'Export functionality to be implemented',
            'edition' => $edition->toArray()
        ];
    }

    /**
     * Get edition statistics
     */
    public function getEditionStatistics(int $id): array
    {
        $edition = $this->repository->getWithRelations($id);
        
        if (!$edition) {
            return [];
        }
        
        // Calculate statistics
        return [
            'total_teams' => $edition->teams()->count(),
            'total_ideas' => $edition->teams()->whereHas('idea')->count(),
            'total_workshops' => $edition->workshops()->count(),
            'total_news' => $edition->news()->count(),
            'registration_open' => $this->isRegistrationOpen($id),
            'idea_submission_open' => $this->isIdeaSubmissionOpen($id),
        ];
    }

    /**
     * Validate edition dates
     */
    public function validateDates(array $data): array
    {
        $errors = [];
        
        // Check registration dates
        if (isset($data['registration_start_date']) && isset($data['registration_end_date'])) {
            if ($data['registration_end_date'] <= $data['registration_start_date']) {
                $errors['registration_end_date'] = 'Registration end date must be after start date';
            }
        }
        
        // Check idea submission dates
        if (isset($data['idea_submission_start_date']) && isset($data['idea_submission_end_date'])) {
            if ($data['idea_submission_end_date'] <= $data['idea_submission_start_date']) {
                $errors['idea_submission_end_date'] = 'Idea submission end date must be after start date';
            }
        }
        
        // Check event dates
        if (isset($data['event_start_date']) && isset($data['event_end_date'])) {
            if ($data['event_end_date'] <= $data['event_start_date']) {
                $errors['event_end_date'] = 'Event end date must be after start date';
            }
        }
        
        return $errors;
    }
}