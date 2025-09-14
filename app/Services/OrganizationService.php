<?php

namespace App\Services;

use App\Models\User;
use App\Repositories\OrganizationRepository;
use App\Repositories\HackathonEditionRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Collection;

class OrganizationService extends BaseService
{
    protected OrganizationRepository $organizationRepository;
    protected HackathonEditionRepository $editionRepository;

    public function __construct(
        OrganizationRepository $organizationRepository,
        HackathonEditionRepository $editionRepository
    ) {
        $this->organizationRepository = $organizationRepository;
        $this->editionRepository = $editionRepository;
    }

    /**
     * Get paginated organization_PLURAL based on user role and filters
     */
    public function getPaginatedOrganizations(User $user, array $filters = [], int $perPage = 15): array
    {
        // Build filters based on user role
        $roleFilters = $this->buildRoleFilters($user, $filters);

        // Get paginated organization_PLURAL
        $organization_PLURAL = $this->organizationRepository->getPaginatedWithFilters($roleFilters, $perPage);

        // Get statistics
        $statistics = $this->organizationRepository->getStatistics($roleFilters);

        // Get editions for filter dropdown
        $editions = $this->getEditionsForUser($user);

        return [
            'organization_PLURAL' => $organization_PLURAL,
            'statistics' => $statistics,
            'editions' => $editions,
            'filters' => $filters
        ];
    }

    /**
     * Get organization details
     */
    public function getOrganizationDetails(int $organizationId, User $user): ?array
    {
        $organization = $this->organizationRepository->findWithFullDetails($organizationId);

        if (!$organization) {
            return null;
        }

        // Check if user has access to this organization
        if (!$this->userCanAccessOrganization($user, $organization)) {
            return null;
        }

        return [
            'organization' => $organization,
            'permissions' => $this->getOrganizationPermissions($user, $organization)
        ];
    }

    /**
     * Create a new organization
     */
    public function createOrganization(array $data, User $user): array
    {
        // Check permissions
        if (!$this->userCanCreateOrganization($user)) {
            throw new \Exception('You do not have permission to create organization_PLURAL.');
        }

        // Validate edition access for non-system admin
        if (!$user->hasRole('system_admin') && !empty($data['edition_id'])) {
            if (!$this->userCanAccessEdition($user, $data['edition_id'])) {
                throw new \Exception('You do not have access to this edition.');
            }
        }

        DB::beginTransaction();
        try {
            // Create organization
            $organization = $this->organizationRepository->create($data);

            // Log activity
            Log::info('Organization created', [
                'organization_id' => $organization->id,
                'user_id' => $user->id,
                'data' => $data
            ]);

            DB::commit();

            return [
                'success' => true,
                'organization' => $organization,
                'message' => 'Organization created successfully.'
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to create organization', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
                'data' => $data
            ]);
            throw $e;
        }
    }

    /**
     * Update a organization
     */
    public function updateOrganization(int $organizationId, array $data, User $user): array
    {
        $organization = $this->organizationRepository->find($organizationId);

        if (!$organization) {
            throw new \Exception('Organization not found.');
        }

        // Check permissions
        if (!$this->userCanEditOrganization($user, $organization)) {
            throw new \Exception('You do not have permission to edit this organization.');
        }

        DB::beginTransaction();
        try {
            // Update organization
            $this->organizationRepository->update($organizationId, $data);

            // Refresh organization data
            $organization = $this->organizationRepository->findWithFullDetails($organizationId);

            // Log activity
            Log::info('Organization updated', [
                'organization_id' => $organizationId,
                'user_id' => $user->id,
                'data' => $data
            ]);

            DB::commit();

            return [
                'success' => true,
                'organization' => $organization,
                'message' => 'Organization updated successfully.'
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to update organization', [
                'organization_id' => $organizationId,
                'user_id' => $user->id,
                'error' => $e->getMessage(),
                'data' => $data
            ]);
            throw $e;
        }
    }

    /**
     * Delete a organization
     */
    public function deleteOrganization(int $organizationId, User $user): array
    {
        $organization = $this->organizationRepository->find($organizationId);

        if (!$organization) {
            throw new \Exception('Organization not found.');
        }

        // Check permissions
        if (!$this->userCanDeleteOrganization($user, $organization)) {
            throw new \Exception('You do not have permission to delete this organization.');
        }

        // Check dependencies
        if ($this->organizationRepository->hasDependencies($organizationId)) {
            throw new \Exception('Cannot delete organization with dependencies.');
        }

        DB::beginTransaction();
        try {
            // Delete organization
            $this->organizationRepository->delete($organizationId);

            // Log activity
            Log::info('Organization deleted', [
                'organization_id' => $organizationId,
                'user_id' => $user->id
            ]);

            DB::commit();

            return [
                'success' => true,
                'message' => 'Organization deleted successfully.'
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to delete organization', [
                'organization_id' => $organizationId,
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
     * Check if user can access a specific organization
     */
    protected function userCanAccessOrganization(User $user, $organization): bool
    {
        switch ($user->user_type) {
            case 'system_admin':
                return true;

            case 'hackathon_admin':
                return !isset($organization->edition_id) || $user->edition_id == $organization->edition_id;

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
     * Check if user can create organization_PLURAL
     */
    protected function userCanCreateOrganization(User $user): bool
    {
        return in_array($user->user_type, ['system_admin', 'hackathon_admin']);
    }

    /**
     * Check if user can edit a organization
     */
    protected function userCanEditOrganization(User $user, $organization): bool
    {
        if (!$this->userCanAccessOrganization($user, $organization)) {
            return false;
        }

        return in_array($user->user_type, ['system_admin', 'hackathon_admin']);
    }

    /**
     * Check if user can delete a organization
     */
    protected function userCanDeleteOrganization(User $user, $organization): bool
    {
        if (!$this->userCanAccessOrganization($user, $organization)) {
            return false;
        }

        // Only system admin can delete
        return $user->hasRole('system_admin');
    }

    /**
     * Get permissions for a organization
     */
    protected function getOrganizationPermissions(User $user, $organization): array
    {
        return [
            'canEdit' => $this->userCanEditOrganization($user, $organization),
            'canDelete' => $this->userCanDeleteOrganization($user, $organization),
            'canExport' => true,
        ];
    }
}
