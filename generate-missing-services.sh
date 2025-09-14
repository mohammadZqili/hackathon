#!/bin/bash

# Generate missing services for SystemAdmin entities

echo "Generating missing services..."

# Create Services directory if not exists
mkdir -p app/Services

# List of entities to generate services for
entities=(
    "Organization"
    "Speaker"
    "Edition"
    "Setting"
    "Checkin"
    "Report"
)

# Function to create a service file
create_service() {
    local entity=$1
    local filename="app/Services/${entity}Service.php"

    # Check if file already exists
    if [ -f "$filename" ]; then
        echo "Service already exists: $filename"
        return
    fi

    cat > "$filename" << 'EOF'
<?php

namespace App\Services;

use App\Models\User;
use App\Repositories\ENTITY_NAMERepository;
use App\Repositories\HackathonEditionRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Collection;

class ENTITY_NAMEService extends BaseService
{
    protected ENTITY_NAMERepository $ENTITY_LOWERRepository;
    protected HackathonEditionRepository $editionRepository;

    public function __construct(
        ENTITY_NAMERepository $ENTITY_LOWERRepository,
        HackathonEditionRepository $editionRepository
    ) {
        $this->ENTITY_LOWERRepository = $ENTITY_LOWERRepository;
        $this->editionRepository = $editionRepository;
    }

    /**
     * Get paginated ENTITY_LOWER_PLURAL based on user role and filters
     */
    public function getPaginatedENTITY_PLURAL(User $user, array $filters = [], int $perPage = 15): array
    {
        // Build filters based on user role
        $roleFilters = $this->buildRoleFilters($user, $filters);

        // Get paginated ENTITY_LOWER_PLURAL
        $ENTITY_LOWER_PLURAL = $this->ENTITY_LOWERRepository->getPaginatedWithFilters($roleFilters, $perPage);

        // Get statistics
        $statistics = $this->ENTITY_LOWERRepository->getStatistics($roleFilters);

        // Get editions for filter dropdown
        $editions = $this->getEditionsForUser($user);

        return [
            'ENTITY_LOWER_PLURAL' => $ENTITY_LOWER_PLURAL,
            'statistics' => $statistics,
            'editions' => $editions,
            'filters' => $filters
        ];
    }

    /**
     * Get ENTITY_LOWER details
     */
    public function getENTITY_NAMEDetails(int $ENTITY_LOWERId, User $user): ?array
    {
        $ENTITY_LOWER = $this->ENTITY_LOWERRepository->findWithFullDetails($ENTITY_LOWERId);

        if (!$ENTITY_LOWER) {
            return null;
        }

        // Check if user has access to this ENTITY_LOWER
        if (!$this->userCanAccessENTITY_NAME($user, $ENTITY_LOWER)) {
            return null;
        }

        return [
            'ENTITY_LOWER' => $ENTITY_LOWER,
            'permissions' => $this->getENTITY_NAMEPermissions($user, $ENTITY_LOWER)
        ];
    }

    /**
     * Create a new ENTITY_LOWER
     */
    public function createENTITY_NAME(array $data, User $user): array
    {
        // Check permissions
        if (!$this->userCanCreateENTITY_NAME($user)) {
            throw new \Exception('You do not have permission to create ENTITY_LOWER_PLURAL.');
        }

        // Validate edition access for non-system admin
        if (!$user->hasRole('system_admin') && !empty($data['edition_id'])) {
            if (!$this->userCanAccessEdition($user, $data['edition_id'])) {
                throw new \Exception('You do not have access to this edition.');
            }
        }

        DB::beginTransaction();
        try {
            // Create ENTITY_LOWER
            $ENTITY_LOWER = $this->ENTITY_LOWERRepository->create($data);

            // Log activity
            Log::info('ENTITY_NAME created', [
                'ENTITY_LOWER_id' => $ENTITY_LOWER->id,
                'user_id' => $user->id,
                'data' => $data
            ]);

            DB::commit();

            return [
                'success' => true,
                'ENTITY_LOWER' => $ENTITY_LOWER,
                'message' => 'ENTITY_NAME created successfully.'
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to create ENTITY_LOWER', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
                'data' => $data
            ]);
            throw $e;
        }
    }

    /**
     * Update a ENTITY_LOWER
     */
    public function updateENTITY_NAME(int $ENTITY_LOWERId, array $data, User $user): array
    {
        $ENTITY_LOWER = $this->ENTITY_LOWERRepository->find($ENTITY_LOWERId);

        if (!$ENTITY_LOWER) {
            throw new \Exception('ENTITY_NAME not found.');
        }

        // Check permissions
        if (!$this->userCanEditENTITY_NAME($user, $ENTITY_LOWER)) {
            throw new \Exception('You do not have permission to edit this ENTITY_LOWER.');
        }

        DB::beginTransaction();
        try {
            // Update ENTITY_LOWER
            $this->ENTITY_LOWERRepository->update($ENTITY_LOWERId, $data);

            // Refresh ENTITY_LOWER data
            $ENTITY_LOWER = $this->ENTITY_LOWERRepository->findWithFullDetails($ENTITY_LOWERId);

            // Log activity
            Log::info('ENTITY_NAME updated', [
                'ENTITY_LOWER_id' => $ENTITY_LOWERId,
                'user_id' => $user->id,
                'data' => $data
            ]);

            DB::commit();

            return [
                'success' => true,
                'ENTITY_LOWER' => $ENTITY_LOWER,
                'message' => 'ENTITY_NAME updated successfully.'
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to update ENTITY_LOWER', [
                'ENTITY_LOWER_id' => $ENTITY_LOWERId,
                'user_id' => $user->id,
                'error' => $e->getMessage(),
                'data' => $data
            ]);
            throw $e;
        }
    }

    /**
     * Delete a ENTITY_LOWER
     */
    public function deleteENTITY_NAME(int $ENTITY_LOWERId, User $user): array
    {
        $ENTITY_LOWER = $this->ENTITY_LOWERRepository->find($ENTITY_LOWERId);

        if (!$ENTITY_LOWER) {
            throw new \Exception('ENTITY_NAME not found.');
        }

        // Check permissions
        if (!$this->userCanDeleteENTITY_NAME($user, $ENTITY_LOWER)) {
            throw new \Exception('You do not have permission to delete this ENTITY_LOWER.');
        }

        // Check dependencies
        if ($this->ENTITY_LOWERRepository->hasDependencies($ENTITY_LOWERId)) {
            throw new \Exception('Cannot delete ENTITY_LOWER with dependencies.');
        }

        DB::beginTransaction();
        try {
            // Delete ENTITY_LOWER
            $this->ENTITY_LOWERRepository->delete($ENTITY_LOWERId);

            // Log activity
            Log::info('ENTITY_NAME deleted', [
                'ENTITY_LOWER_id' => $ENTITY_LOWERId,
                'user_id' => $user->id
            ]);

            DB::commit();

            return [
                'success' => true,
                'message' => 'ENTITY_NAME deleted successfully.'
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to delete ENTITY_LOWER', [
                'ENTITY_LOWER_id' => $ENTITY_LOWERId,
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
     * Check if user can access a specific ENTITY_LOWER
     */
    protected function userCanAccessENTITY_NAME(User $user, $ENTITY_LOWER): bool
    {
        switch ($user->user_type) {
            case 'system_admin':
                return true;

            case 'hackathon_admin':
                return !isset($ENTITY_LOWER->edition_id) || $user->edition_id == $ENTITY_LOWER->edition_id;

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
     * Check if user can create ENTITY_LOWER_PLURAL
     */
    protected function userCanCreateENTITY_NAME(User $user): bool
    {
        return in_array($user->user_type, ['system_admin', 'hackathon_admin']);
    }

    /**
     * Check if user can edit a ENTITY_LOWER
     */
    protected function userCanEditENTITY_NAME(User $user, $ENTITY_LOWER): bool
    {
        if (!$this->userCanAccessENTITY_NAME($user, $ENTITY_LOWER)) {
            return false;
        }

        return in_array($user->user_type, ['system_admin', 'hackathon_admin']);
    }

    /**
     * Check if user can delete a ENTITY_LOWER
     */
    protected function userCanDeleteENTITY_NAME(User $user, $ENTITY_LOWER): bool
    {
        if (!$this->userCanAccessENTITY_NAME($user, $ENTITY_LOWER)) {
            return false;
        }

        // Only system admin can delete
        return $user->hasRole('system_admin');
    }

    /**
     * Get permissions for a ENTITY_LOWER
     */
    protected function getENTITY_NAMEPermissions(User $user, $ENTITY_LOWER): array
    {
        return [
            'canEdit' => $this->userCanEditENTITY_NAME($user, $ENTITY_LOWER),
            'canDelete' => $this->userCanDeleteENTITY_NAME($user, $ENTITY_LOWER),
            'canExport' => true,
        ];
    }
}
EOF

    # Replace placeholders
    local entity_lower=$(echo "$entity" | tr '[:upper:]' '[:lower:]')
    local entity_plural=$(echo "${entity}s" | sed 's/ys$/ies/')
    local entity_lower_plural=$(echo "$entity_plural" | tr '[:upper:]' '[:lower:]')

    sed -i "s/ENTITY_NAME/$entity/g" "$filename"
    sed -i "s/ENTITY_LOWER/$entity_lower/g" "$filename"
    sed -i "s/ENTITY_PLURAL/$entity_plural/g" "$filename"
    sed -i "s/ENTITY_LOWER_PLURAL/$entity_lower_plural/g" "$filename"

    echo "Created service: $filename"
}

# Generate services for each entity
for entity in "${entities[@]}"; do
    create_service "$entity"
done

echo "Service generation complete!"
