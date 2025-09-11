<?php

namespace App\Services;

use App\Models\User;
use App\Repositories\SettingRepository;
use App\Repositories\HackathonEditionRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Collection;

class SettingService extends BaseService
{
    protected SettingRepository $settingRepository;
    protected HackathonEditionRepository $editionRepository;

    public function __construct(
        SettingRepository $settingRepository,
        HackathonEditionRepository $editionRepository
    ) {
        $this->settingRepository = $settingRepository;
        $this->editionRepository = $editionRepository;
    }

    /**
     * Get paginated setting_PLURAL based on user role and filters
     */
    public function getPaginatedSettings(User $user, array $filters = [], int $perPage = 15): array
    {
        // Build filters based on user role
        $roleFilters = $this->buildRoleFilters($user, $filters);
        
        // Get paginated setting_PLURAL
        $setting_PLURAL = $this->settingRepository->getPaginatedWithFilters($roleFilters, $perPage);
        
        // Get statistics
        $statistics = $this->settingRepository->getStatistics($roleFilters);
        
        // Get editions for filter dropdown
        $editions = $this->getEditionsForUser($user);
        
        return [
            'setting_PLURAL' => $setting_PLURAL,
            'statistics' => $statistics,
            'editions' => $editions,
            'filters' => $filters
        ];
    }

    /**
     * Get setting details
     */
    public function getSettingDetails(int $settingId, User $user): ?array
    {
        $setting = $this->settingRepository->findWithFullDetails($settingId);
        
        if (!$setting) {
            return null;
        }
        
        // Check if user has access to this setting
        if (!$this->userCanAccessSetting($user, $setting)) {
            return null;
        }
        
        return [
            'setting' => $setting,
            'permissions' => $this->getSettingPermissions($user, $setting)
        ];
    }

    /**
     * Create a new setting
     */
    public function createSetting(array $data, User $user): array
    {
        // Check permissions
        if (!$this->userCanCreateSetting($user)) {
            throw new \Exception('You do not have permission to create setting_PLURAL.');
        }
        
        // Validate edition access for non-system admin
        if ($user->user_type !== 'system_admin' && !empty($data['edition_id'])) {
            if (!$this->userCanAccessEdition($user, $data['edition_id'])) {
                throw new \Exception('You do not have access to this edition.');
            }
        }
        
        DB::beginTransaction();
        try {
            // Create setting
            $setting = $this->settingRepository->create($data);
            
            // Log activity
            Log::info('Setting created', [
                'setting_id' => $setting->id,
                'user_id' => $user->id,
                'data' => $data
            ]);
            
            DB::commit();
            
            return [
                'success' => true,
                'setting' => $setting,
                'message' => 'Setting created successfully.'
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to create setting', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
                'data' => $data
            ]);
            throw $e;
        }
    }

    /**
     * Update a setting
     */
    public function updateSetting(int $settingId, array $data, User $user): array
    {
        $setting = $this->settingRepository->find($settingId);
        
        if (!$setting) {
            throw new \Exception('Setting not found.');
        }
        
        // Check permissions
        if (!$this->userCanEditSetting($user, $setting)) {
            throw new \Exception('You do not have permission to edit this setting.');
        }
        
        DB::beginTransaction();
        try {
            // Update setting
            $this->settingRepository->update($settingId, $data);
            
            // Refresh setting data
            $setting = $this->settingRepository->findWithFullDetails($settingId);
            
            // Log activity
            Log::info('Setting updated', [
                'setting_id' => $settingId,
                'user_id' => $user->id,
                'data' => $data
            ]);
            
            DB::commit();
            
            return [
                'success' => true,
                'setting' => $setting,
                'message' => 'Setting updated successfully.'
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to update setting', [
                'setting_id' => $settingId,
                'user_id' => $user->id,
                'error' => $e->getMessage(),
                'data' => $data
            ]);
            throw $e;
        }
    }

    /**
     * Delete a setting
     */
    public function deleteSetting(int $settingId, User $user): array
    {
        $setting = $this->settingRepository->find($settingId);
        
        if (!$setting) {
            throw new \Exception('Setting not found.');
        }
        
        // Check permissions
        if (!$this->userCanDeleteSetting($user, $setting)) {
            throw new \Exception('You do not have permission to delete this setting.');
        }
        
        // Check dependencies
        if ($this->settingRepository->hasDependencies($settingId)) {
            throw new \Exception('Cannot delete setting with dependencies.');
        }
        
        DB::beginTransaction();
        try {
            // Delete setting
            $this->settingRepository->delete($settingId);
            
            // Log activity
            Log::info('Setting deleted', [
                'setting_id' => $settingId,
                'user_id' => $user->id
            ]);
            
            DB::commit();
            
            return [
                'success' => true,
                'message' => 'Setting deleted successfully.'
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to delete setting', [
                'setting_id' => $settingId,
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
     * Check if user can access a specific setting
     */
    protected function userCanAccessSetting(User $user, $setting): bool
    {
        switch ($user->user_type) {
            case 'system_admin':
                return true;
                
            case 'hackathon_admin':
                return !isset($setting->edition_id) || $user->edition_id == $setting->edition_id;
                
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
     * Check if user can create setting_PLURAL
     */
    protected function userCanCreateSetting(User $user): bool
    {
        return in_array($user->user_type, ['system_admin', 'hackathon_admin']);
    }

    /**
     * Check if user can edit a setting
     */
    protected function userCanEditSetting(User $user, $setting): bool
    {
        if (!$this->userCanAccessSetting($user, $setting)) {
            return false;
        }
        
        return in_array($user->user_type, ['system_admin', 'hackathon_admin']);
    }

    /**
     * Check if user can delete a setting
     */
    protected function userCanDeleteSetting(User $user, $setting): bool
    {
        if (!$this->userCanAccessSetting($user, $setting)) {
            return false;
        }
        
        // Only system admin can delete
        return $user->user_type === 'system_admin';
    }

    /**
     * Get permissions for a setting
     */
    protected function getSettingPermissions(User $user, $setting): array
    {
        return [
            'canEdit' => $this->userCanEditSetting($user, $setting),
            'canDelete' => $this->userCanDeleteSetting($user, $setting),
            'canExport' => true,
        ];
    }
}
