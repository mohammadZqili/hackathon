<?php

namespace App\Services;

use App\Models\User;
use App\Repositories\CheckinRepository;
use App\Repositories\HackathonEditionRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Collection;

class CheckinService extends BaseService
{
    protected CheckinRepository $checkinRepository;
    protected HackathonEditionRepository $editionRepository;

    public function __construct(
        CheckinRepository $checkinRepository,
        HackathonEditionRepository $editionRepository
    ) {
        $this->checkinRepository = $checkinRepository;
        $this->editionRepository = $editionRepository;
    }

    /**
     * Get paginated checkin_PLURAL based on user role and filters
     */
    public function getPaginatedCheckins(User $user, array $filters = [], int $perPage = 15): array
    {
        // Build filters based on user role
        $roleFilters = $this->buildRoleFilters($user, $filters);

        // Get paginated checkin_PLURAL
        $checkin_PLURAL = $this->checkinRepository->getPaginatedWithFilters($roleFilters, $perPage);

        // Get statistics
        $statistics = $this->checkinRepository->getStatistics($roleFilters);

        // Get editions for filter dropdown
        $editions = $this->getEditionsForUser($user);

        return [
            'checkin_PLURAL' => $checkin_PLURAL,
            'statistics' => $statistics,
            'editions' => $editions,
            'filters' => $filters
        ];
    }

    /**
     * Get checkin details
     */
    public function getCheckinDetails(int $checkinId, User $user): ?array
    {
        $checkin = $this->checkinRepository->findWithFullDetails($checkinId);

        if (!$checkin) {
            return null;
        }

        // Check if user has access to this checkin
        if (!$this->userCanAccessCheckin($user, $checkin)) {
            return null;
        }

        return [
            'checkin' => $checkin,
            'permissions' => $this->getCheckinPermissions($user, $checkin)
        ];
    }

    /**
     * Create a new checkin
     */
    public function createCheckin(array $data, User $user): array
    {
        // Check permissions
        if (!$this->userCanCreateCheckin($user)) {
            throw new \Exception('You do not have permission to create checkin_PLURAL.');
        }

        // Validate edition access for non-system admin
        if (!$user->hasRole('system_admin') && !empty($data['edition_id'])) {
            if (!$this->userCanAccessEdition($user, $data['edition_id'])) {
                throw new \Exception('You do not have access to this edition.');
            }
        }

        DB::beginTransaction();
        try {
            // Create checkin
            $checkin = $this->checkinRepository->create($data);

            // Log activity
            Log::info('Checkin created', [
                'checkin_id' => $checkin->id,
                'user_id' => $user->id,
                'data' => $data
            ]);

            DB::commit();

            return [
                'success' => true,
                'checkin' => $checkin,
                'message' => 'Checkin created successfully.'
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to create checkin', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
                'data' => $data
            ]);
            throw $e;
        }
    }

    /**
     * Update a checkin
     */
    public function updateCheckin(int $checkinId, array $data, User $user): array
    {
        $checkin = $this->checkinRepository->find($checkinId);

        if (!$checkin) {
            throw new \Exception('Checkin not found.');
        }

        // Check permissions
        if (!$this->userCanEditCheckin($user, $checkin)) {
            throw new \Exception('You do not have permission to edit this checkin.');
        }

        DB::beginTransaction();
        try {
            // Update checkin
            $this->checkinRepository->update($checkinId, $data);

            // Refresh checkin data
            $checkin = $this->checkinRepository->findWithFullDetails($checkinId);

            // Log activity
            Log::info('Checkin updated', [
                'checkin_id' => $checkinId,
                'user_id' => $user->id,
                'data' => $data
            ]);

            DB::commit();

            return [
                'success' => true,
                'checkin' => $checkin,
                'message' => 'Checkin updated successfully.'
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to update checkin', [
                'checkin_id' => $checkinId,
                'user_id' => $user->id,
                'error' => $e->getMessage(),
                'data' => $data
            ]);
            throw $e;
        }
    }

    /**
     * Delete a checkin
     */
    public function deleteCheckin(int $checkinId, User $user): array
    {
        $checkin = $this->checkinRepository->find($checkinId);

        if (!$checkin) {
            throw new \Exception('Checkin not found.');
        }

        // Check permissions
        if (!$this->userCanDeleteCheckin($user, $checkin)) {
            throw new \Exception('You do not have permission to delete this checkin.');
        }

        // Check dependencies
        if ($this->checkinRepository->hasDependencies($checkinId)) {
            throw new \Exception('Cannot delete checkin with dependencies.');
        }

        DB::beginTransaction();
        try {
            // Delete checkin
            $this->checkinRepository->delete($checkinId);

            // Log activity
            Log::info('Checkin deleted', [
                'checkin_id' => $checkinId,
                'user_id' => $user->id
            ]);

            DB::commit();

            return [
                'success' => true,
                'message' => 'Checkin deleted successfully.'
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to delete checkin', [
                'checkin_id' => $checkinId,
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
     * Check if user can access a specific checkin
     */
    protected function userCanAccessCheckin(User $user, $checkin): bool
    {
        switch ($user->user_type) {
            case 'system_admin':
                return true;

            case 'hackathon_admin':
                return !isset($checkin->edition_id) || $user->edition_id == $checkin->edition_id;

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
     * Check if user can create checkin_PLURAL
     */
    protected function userCanCreateCheckin(User $user): bool
    {
        return in_array($user->user_type, ['system_admin', 'hackathon_admin']);
    }

    /**
     * Check if user can edit a checkin
     */
    protected function userCanEditCheckin(User $user, $checkin): bool
    {
        if (!$this->userCanAccessCheckin($user, $checkin)) {
            return false;
        }

        return in_array($user->user_type, ['system_admin', 'hackathon_admin']);
    }

    /**
     * Check if user can delete a checkin
     */
    protected function userCanDeleteCheckin(User $user, $checkin): bool
    {
        if (!$this->userCanAccessCheckin($user, $checkin)) {
            return false;
        }

        // Only system admin can delete
        return $user->hasRole('system_admin');
    }

    /**
     * Get permissions for a checkin
     */
    protected function getCheckinPermissions(User $user, $checkin): array
    {
        return [
            'canEdit' => $this->userCanEditCheckin($user, $checkin),
            'canDelete' => $this->userCanDeleteCheckin($user, $checkin),
            'canExport' => true,
        ];
    }
}
