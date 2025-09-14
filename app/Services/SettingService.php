<?php

namespace App\Services;

use App\Models\User;
use App\Models\SystemSetting;
use App\Repositories\SettingRepository;
use App\Repositories\HackathonEditionRepository;
use App\Providers\SettingsServiceProvider;
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
     * Get all system settings
     */
    public function getAllSettings(User $user): array
    {
        // Check if user is system admin
        if (!$user->hasRole('system_admin')) {
            throw new \Exception('You do not have permission to access system settings.');
        }

        return [
            'smtp' => SystemSetting::getGroup('smtp'),
            'branding' => SystemSetting::getGroup('branding'),
            'notifications' => SystemSetting::getGroup('notifications'),
            'sms' => SystemSetting::getGroup('sms'),
            'twitter' => SystemSetting::getGroup('twitter'),
            'app_name' => SystemSetting::get('app_name', config('app.name')),
            'app_logo' => SystemSetting::get('app_logo'),
            'primary_color' => SystemSetting::get('primary_color', '#0d9488'),
            'secondary_color' => SystemSetting::get('secondary_color', '#14b8a6'),
        ];
    }

    /**
     * Get branding settings
     */
    public function getBrandingSettings(User $user): array
    {
        // Check if user is system admin
        if (!$user->hasRole('system_admin')) {
            throw new \Exception('You do not have permission to access branding settings.');
        }

        return [
            'app_name' => SystemSetting::get('app_name', config('app.name')),
            'app_logo' => SystemSetting::get('app_logo'),
            'primary_color' => SystemSetting::get('primary_color', '#0d9488'),
            'secondary_color' => SystemSetting::get('secondary_color', '#14b8a6'),
            'footer_text' => SystemSetting::get('footer_text'),
        ];
    }

    /**
     * Update branding settings
     */
    public function updateBrandingSettings(array $data, User $user): array
    {
        // Check if user is system admin
        if (!$user->hasRole('system_admin')) {
            throw new \Exception('You do not have permission to update branding settings.');
        }

        DB::beginTransaction();
        try {
            // Update each branding setting
            SystemSetting::set('app_name', $data['app_name'], 'branding');
            SystemSetting::set('app_logo', $data['app_logo'] ?? null, 'branding');
            SystemSetting::set('primary_color', $data['primary_color'], 'branding');
            SystemSetting::set('secondary_color', $data['secondary_color'], 'branding');
            SystemSetting::set('footer_text', $data['footer_text'] ?? null, 'branding');

            // Clear settings cache
            SettingsServiceProvider::clearCache();

            DB::commit();

            return [
                'success' => true,
                'message' => 'Branding settings updated successfully.'
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to update branding settings', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
                'data' => $data
            ]);
            throw $e;
        }
    }

    /**
     * Get SMTP settings
     */
    public function getSmtpSettings(User $user): array
    {
        // Check if user is system admin
        if (!$user->hasRole('system_admin')) {
            throw new \Exception('You do not have permission to access SMTP settings.');
        }

        return [
            'smtp_host' => SystemSetting::get('mail_host', config('mail.mailers.smtp.host')),
            'smtp_port' => SystemSetting::get('mail_port', config('mail.mailers.smtp.port')),
            'smtp_username' => SystemSetting::get('mail_username', config('mail.mailers.smtp.username')),
            'smtp_password' => SystemSetting::get('mail_password'),
            'smtp_encryption' => SystemSetting::get('mail_encryption', config('mail.mailers.smtp.encryption')),
            'smtp_from_address' => SystemSetting::get('mail_from_address', config('mail.from.address')),
            'smtp_from_name' => SystemSetting::get('mail_from_name', config('mail.from.name')),
        ];
    }

    /**
     * Update SMTP settings
     */
    public function updateSmtpSettings(array $data, User $user): array
    {
        // Check if user is system admin
        if (!$user->hasRole('system_admin')) {
            throw new \Exception('You do not have permission to update SMTP settings.');
        }

        DB::beginTransaction();
        try {
            // Update each SMTP setting
            SystemSetting::set('mail_host', $data['smtp_host'], 'smtp');
            SystemSetting::set('mail_port', $data['smtp_port'], 'smtp');
            SystemSetting::set('mail_username', $data['smtp_username'], 'smtp');

            // Only update password if provided
            if (!empty($data['smtp_password'])) {
                SystemSetting::set('mail_password', $data['smtp_password'], 'smtp');
            }

            SystemSetting::set('mail_encryption', $data['smtp_encryption'] ?? null, 'smtp');
            SystemSetting::set('mail_from_address', $data['smtp_from_address'], 'smtp');
            SystemSetting::set('mail_from_name', $data['smtp_from_name'], 'smtp');

            // Clear settings cache
            SettingsServiceProvider::clearCache();

            DB::commit();

            return [
                'success' => true,
                'message' => 'SMTP settings updated successfully.'
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to update SMTP settings', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
                'data' => array_diff_key($data, ['smtp_password' => '']) // Don't log password
            ]);
            throw $e;
        }
    }

    /**
     * Get notification settings
     */
    public function getNotificationSettings(User $user): array
    {
        // Check if user is system admin
        if (!$user->hasRole('system_admin')) {
            throw new \Exception('You do not have permission to access notification settings.');
        }

        return [
            'email_enabled' => SystemSetting::get('notification_email_enabled', '1') === '1',
            'sms_enabled' => SystemSetting::get('notification_sms_enabled', '0') === '1',
            'push_enabled' => SystemSetting::get('notification_push_enabled', '0') === '1',
            'in_app_enabled' => SystemSetting::get('notification_in_app_enabled', '1') === '1',
            'email_digest' => SystemSetting::get('notification_email_digest', 'daily'),
            'quiet_hours_enabled' => SystemSetting::get('notification_quiet_hours_enabled', '0') === '1',
            'quiet_hours_start' => SystemSetting::get('notification_quiet_hours_start', '22:00'),
            'quiet_hours_end' => SystemSetting::get('notification_quiet_hours_end', '08:00'),
        ];
    }

    /**
     * Update notification settings
     */
    public function updateNotificationSettings(array $data, User $user): array
    {
        // Check if user is system admin
        if (!$user->hasRole('system_admin')) {
            throw new \Exception('You do not have permission to update notification settings.');
        }

        DB::beginTransaction();
        try {
            // Update notification settings - store booleans as '1' or '0'
            SystemSetting::set('notification_email_enabled', $data['email_enabled'] ? '1' : '0', 'notifications');
            SystemSetting::set('notification_sms_enabled', $data['sms_enabled'] ? '1' : '0', 'notifications');
            SystemSetting::set('notification_push_enabled', $data['push_enabled'] ? '1' : '0', 'notifications');
            SystemSetting::set('notification_in_app_enabled', $data['in_app_enabled'] ? '1' : '0', 'notifications');

            if (isset($data['email_digest'])) {
                SystemSetting::set('notification_email_digest', $data['email_digest'], 'notifications');
            }

            SystemSetting::set('notification_quiet_hours_enabled', $data['quiet_hours_enabled'] ? '1' : '0', 'notifications');

            if (isset($data['quiet_hours_start'])) {
                SystemSetting::set('notification_quiet_hours_start', $data['quiet_hours_start'], 'notifications');
            }

            if (isset($data['quiet_hours_end'])) {
                SystemSetting::set('notification_quiet_hours_end', $data['quiet_hours_end'], 'notifications');
            }

            // Clear settings cache
            SettingsServiceProvider::clearCache();

            DB::commit();

            return [
                'success' => true,
                'message' => 'Notification settings updated successfully.'
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to update notification settings', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
                'data' => $data
            ]);
            throw $e;
        }
    }

    /**
     * Get SMS settings
     */
    public function getSmsSettings(User $user): array
    {
        // Check if user is system admin
        if (!$user->hasRole('system_admin')) {
            throw new \Exception('You do not have permission to access SMS settings.');
        }

        return [
            'sms_provider' => SystemSetting::get('sms_provider', 'twilio'),
            'sms_api_key' => SystemSetting::get('sms_api_key'),
            'sms_api_secret' => SystemSetting::get('sms_api_secret'),
            'sms_from_number' => SystemSetting::get('sms_from_number'),
        ];
    }

    /**
     * Update SMS settings
     */
    public function updateSmsSettings(array $data, User $user): array
    {
        // Check if user is system admin
        if (!$user->hasRole('system_admin')) {
            throw new \Exception('You do not have permission to update SMS settings.');
        }

        DB::beginTransaction();
        try {
            // Update SMS settings
            SystemSetting::set('sms_provider', $data['sms_provider'], 'sms');
            SystemSetting::set('sms_api_key', $data['sms_api_key'], 'sms');
            SystemSetting::set('sms_api_secret', $data['sms_api_secret'] ?? null, 'sms');
            SystemSetting::set('sms_from_number', $data['sms_from_number'], 'sms');

            // Clear settings cache
            SettingsServiceProvider::clearCache();

            DB::commit();

            return [
                'success' => true,
                'message' => 'SMS settings updated successfully.'
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to update SMS settings', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
                'data' => array_diff_key($data, ['sms_api_key' => '', 'sms_api_secret' => '']) // Don't log credentials
            ]);
            throw $e;
        }
    }

    /**
     * Get Twitter settings
     */
    public function getTwitterSettings(User $user): array
    {
        // Check if user is system admin
        if (!$user->hasRole('system_admin')) {
            throw new \Exception('You do not have permission to access Twitter settings.');
        }

        return [
            'twitter_api_key' => SystemSetting::get('twitter_api_key'),
            'twitter_api_secret' => SystemSetting::get('twitter_api_secret'),
            'twitter_access_token' => SystemSetting::get('twitter_access_token'),
            'twitter_access_token_secret' => SystemSetting::get('twitter_access_token_secret'),
            'twitter_auto_post' => SystemSetting::get('twitter_auto_post', '0') === '1',
        ];
    }

    /**
     * Update Twitter settings
     */
    public function updateTwitterSettings(array $data, User $user): array
    {
        // Check if user is system admin
        if (!$user->hasRole('system_admin')) {
            throw new \Exception('You do not have permission to update Twitter settings.');
        }

        DB::beginTransaction();
        try {
            // Update Twitter settings
            if (isset($data['twitter_api_key'])) {
                SystemSetting::set('twitter_api_key', $data['twitter_api_key'], 'twitter');
            }
            if (isset($data['twitter_api_secret'])) {
                SystemSetting::set('twitter_api_secret', $data['twitter_api_secret'], 'twitter');
            }
            if (isset($data['twitter_access_token'])) {
                SystemSetting::set('twitter_access_token', $data['twitter_access_token'], 'twitter');
            }
            if (isset($data['twitter_access_token_secret'])) {
                SystemSetting::set('twitter_access_token_secret', $data['twitter_access_token_secret'], 'twitter');
            }

            SystemSetting::set('twitter_auto_post', isset($data['twitter_auto_post']) && $data['twitter_auto_post'] ? '1' : '0', 'twitter');

            // Clear settings cache
            SettingsServiceProvider::clearCache();

            DB::commit();

            return [
                'success' => true,
                'message' => 'Twitter settings updated successfully.'
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to update Twitter settings', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
                'data' => array_diff_key($data, ['twitter_api_key' => '', 'twitter_api_secret' => '', 'twitter_access_token' => '', 'twitter_access_token_secret' => '']) // Don't log credentials
            ]);
            throw $e;
        }
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
        if (!$user->hasRole('system_admin') && !empty($data['edition_id'])) {
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
        return $user->hasRole('system_admin');
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
