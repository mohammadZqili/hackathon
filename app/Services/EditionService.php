<?php

namespace App\Services;

use App\Models\User;
use App\Repositories\EditionRepository;
use App\Repositories\HackathonEditionRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Collection;

class EditionService extends BaseService
{
    protected EditionRepository $editionRepository;
    protected HackathonEditionRepository $editionRepository;

    public function __construct(
        EditionRepository $editionRepository,
        HackathonEditionRepository $editionRepository
    ) {
        $this->editionRepository = $editionRepository;
        $this->editionRepository = $editionRepository;
    }

    /**
     * Get paginated edition_PLURAL based on user role and filters
     */
    public function getPaginatedEditions(User $user, array $filters = [], int $perPage = 15): array
    {
        // Build filters based on user role
        $roleFilters = $this->buildRoleFilters($user, $filters);
        
        // Get paginated edition_PLURAL
        $edition_PLURAL = $this->editionRepository->getPaginatedWithFilters($roleFilters, $perPage);
        
        // Get statistics
        $statistics = $this->editionRepository->getStatistics($roleFilters);
        
        // Get editions for filter dropdown
        $editions = $this->getEditionsForUser($user);
        
        return [
            'edition_PLURAL' => $edition_PLURAL,
            'statistics' => $statistics,
            'editions' => $editions,
            'filters' => $filters
        ];
    }

    /**
     * Get edition details
     */
    public function getEditionDetails(int $editionId, User $user): ?array
    {
        $edition = $this->editionRepository->findWithFullDetails($editionId);
        
        if (!$edition) {
            return null;
        }
        
        // Check if user has access to this edition
        if (!$this->userCanAccessEdition($user, $edition)) {
            return null;
        }
        
        return [
            'edition' => $edition,
            'permissions' => $this->getEditionPermissions($user, $edition)
        ];
    }

    /**
     * Create a new edition
     */
    public function createEdition(array $data, User $user): array
    {
        // Check permissions
        if (!$this->userCanCreateEdition($user)) {
            throw new \Exception('You do not have permission to create edition_PLURAL.');
        }
        
        // Validate edition access for non-system admin
        if ($user->user_type !== 'system_admin' && !empty($data['edition_id'])) {
            if (!$this->userCanAccessEdition($user, $data['edition_id'])) {
                throw new \Exception('You do not have access to this edition.');
            }
        }
        
        DB::beginTransaction();
        try {
            // Create edition
            $edition = $this->editionRepository->create($data);
            
            // Log activity
            Log::info('Edition created', [
                'edition_id' => $edition->id,
                'user_id' => $user->id,
                'data' => $data
            ]);
            
            DB::commit();
            
            return [
                'success' => true,
                'edition' => $edition,
                'message' => 'Edition created successfully.'
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to create edition', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
                'data' => $data
            ]);
            throw $e;
        }
    }

    /**
     * Update a edition
     */
    public function updateEdition(int $editionId, array $data, User $user): array
    {
        $edition = $this->editionRepository->find($editionId);
        
        if (!$edition) {
            throw new \Exception('Edition not found.');
        }
        
        // Check permissions
        if (!$this->userCanEditEdition($user, $edition)) {
            throw new \Exception('You do not have permission to edit this edition.');
        }
        
        DB::beginTransaction();
        try {
            // Update edition
            $this->editionRepository->update($editionId, $data);
            
            // Refresh edition data
            $edition = $this->editionRepository->findWithFullDetails($editionId);
            
            // Log activity
            Log::info('Edition updated', [
                'edition_id' => $editionId,
                'user_id' => $user->id,
                'data' => $data
            ]);
            
            DB::commit();
            
            return [
                'success' => true,
                'edition' => $edition,
                'message' => 'Edition updated successfully.'
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to update edition', [
                'edition_id' => $editionId,
                'user_id' => $user->id,
                'error' => $e->getMessage(),
                'data' => $data
            ]);
            throw $e;
        }
    }

    /**
     * Delete a edition
     */
    public function deleteEdition(int $editionId, User $user): array
    {
        $edition = $this->editionRepository->find($editionId);
        
        if (!$edition) {
            throw new \Exception('Edition not found.');
        }
        
        // Check permissions
        if (!$this->userCanDeleteEdition($user, $edition)) {
            throw new \Exception('You do not have permission to delete this edition.');
        }
        
        // Check dependencies
        if ($this->editionRepository->hasDependencies($editionId)) {
            throw new \Exception('Cannot delete edition with dependencies.');
        }
        
        DB::beginTransaction();
        try {
            // Delete edition
            $this->editionRepository->delete($editionId);
            
            // Log activity
            Log::info('Edition deleted', [
                'edition_id' => $editionId,
                'user_id' => $user->id
            ]);
            
            DB::commit();
            
            return [
                'success' => true,
                'message' => 'Edition deleted successfully.'
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to delete edition', [
                'edition_id' => $editionId,
                'user_id' => $user->id,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Build filters based on user role
     */
    protected function buildRoleFilters(User $user, array $filters): array
    {
        $roleFilters = $filters;
        
        switch ($user->user_type) {
            case 'hackathon_admin':
                // Limit to user's edition
                if ($user->edition_id) {
                    $roleFilters['edition_id'] = $user->edition_id;
                }
                break;
                
            case 'system_admin':
                // No additional filters - can see everything
                break;
                
            default:
                // Other roles - force empty result
                $roleFilters['force_empty'] = true;
                break;
        }
        
        return $roleFilters;
    }

    /**
     * Get editions available to user
     */
    protected function getEditionsForUser(User $user): Collection
    {
        switch ($user->user_type) {
            case 'system_admin':
                return $this->editionRepository->all();
                
            case 'hackathon_admin':
                if ($user->edition_id) {
                    return collect([$this->editionRepository->find($user->edition_id)]);
                }
                return collect();
                
            default:
                return collect();
        }
    }

    /**
     * Check if user can access a specific edition
     */
    protected function userCanAccessEdition(User $user, $edition): bool
    {
        switch ($user->user_type) {
            case 'system_admin':
                return true;
                
            case 'hackathon_admin':
                return !isset($edition->edition_id) || $user->edition_id == $edition->edition_id;
                
            default:
                return false;
        }
    }

    /**
     * Check if user can access a specific edition
     */
    protected function userCanAccessEdition(User $user, int $editionId): bool
    {
        switch ($user->user_type) {
            case 'system_admin':
                return true;
                
            case 'hackathon_admin':
                return $user->edition_id == $editionId;
                
            default:
                return false;
        }
    }

    /**
     * Check if user can create edition_PLURAL
     */
    protected function userCanCreateEdition(User $user): bool
    {
        return in_array($user->user_type, ['system_admin', 'hackathon_admin']);
    }

    /**
     * Check if user can edit a edition
     */
    protected function userCanEditEdition(User $user, $edition): bool
    {
        if (!$this->userCanAccessEdition($user, $edition)) {
            return false;
        }
        
        return in_array($user->user_type, ['system_admin', 'hackathon_admin']);
    }

    /**
     * Check if user can delete a edition
     */
    protected function userCanDeleteEdition(User $user, $edition): bool
    {
        if (!$this->userCanAccessEdition($user, $edition)) {
            return false;
        }
        
        // Only system admin can delete
        return $user->user_type === 'system_admin';
    }

    /**
     * Get permissions for a edition
     */
    protected function getEditionPermissions(User $user, $edition): array
    {
        return [
            'canEdit' => $this->userCanEditEdition($user, $edition),
            'canDelete' => $this->userCanDeleteEdition($user, $edition),
            'canExport' => true,
        ];
    }
}
