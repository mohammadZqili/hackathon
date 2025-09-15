<?php

namespace App\Http\Controllers\TrackSupervisor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\SystemSetting;
use Illuminate\Support\Facades\Cache;
use App\Providers\SettingsServiceProvider;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SettingsController extends Controller
{
    /**
     * Display the settings dashboard.
     */
    public function index()
    {
        $settings = [
            'smtp' => $this->getSmtpSettings(),
            'branding' => $this->getBrandingSettings(),
            'twitter' => $this->getTwitterSettings(),
            'sms' => $this->getSmsSettings(),
            'notifications' => $this->getNotificationSettings(),
        ];

        return Inertia::render('TrackSupervisor/Settings/Index', [
            'settings' => $settings
        ]);
    }

    /**
     * Display SMTP settings.
     */
    public function smtp()
    {
        $smtpSettings = $this->getSmtpSettings();

        return Inertia::render('TrackSupervisor/Settings/Smtp', [
            'settings' => $smtpSettings
        ]);
    }

    /**
     * Update SMTP settings.
     */
    public function updateSmtp(Request $request)
    {
        try {
            $validated = $request->validate([
                'mail_host' => 'nullable|string',
                'mail_port' => 'nullable|integer',
                'mail_username' => 'nullable|string',
                'mail_password' => 'nullable|string',
                'mail_encryption' => 'nullable|in:tls,ssl,',
                'mail_from_address' => 'nullable|email',
            ]);

            DB::beginTransaction();

            foreach ($validated as $key => $value) {
                if ($value !== null && $value !== '') {
                    SystemSetting::updateOrCreate(
                        ['key' => $key],
                        ['value' => $value, 'group' => 'smtp']
                    );
                }
            }

            DB::commit();

            // Clear all settings cache
            SettingsServiceProvider::clearCache();

            // Clear config cache to apply new settings
            \Artisan::call('config:clear');

            return redirect()->route('track-supervisor.settings.index')
                ->with('success', 'SMTP settings updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('SMTP settings update failed: ' . $e->getMessage());

            return redirect()->route('track-supervisor.settings.index')
                ->with('error', 'Failed to update SMTP settings.');
        }
    }

    /**
     * Display branding settings.
     */
    public function branding()
    {
        $brandingSettings = $this->getBrandingSettings();

        return Inertia::render('TrackSupervisor/Settings/Branding', [
            'settings' => $brandingSettings
        ]);
    }

    /**
     * Update branding settings.
     */
    public function updateBranding(Request $request)
    {
        try {
            $validated = $request->validate([
                'app_name' => 'nullable|string|max:255',
                'logo' => 'nullable|image|max:2048',
                'primary_color' => 'nullable|string|max:7',
                'secondary_color' => 'nullable|string|max:7',
                'success_color' => 'nullable|string|max:7',
                'danger_color' => 'nullable|string|max:7',
            ]);

            DB::beginTransaction();

            // Handle file upload
            if ($request->hasFile('logo')) {
                $logoPath = $request->file('logo')->store('branding', 'public');
                $validated['app_logo'] = $logoPath;
                unset($validated['logo']);
            }

            foreach ($validated as $key => $value) {
                if ($value !== null && $value !== '') {
                    SystemSetting::updateOrCreate(
                        ['key' => $key],
                        ['value' => $value, 'group' => 'branding']
                    );
                }
            }

            DB::commit();

            // Clear all settings cache
            SettingsServiceProvider::clearCache();

            // Clear config cache to apply new settings
            \Artisan::call('config:clear');

            return redirect()->route('track-supervisor.settings.index')
                ->with('success', 'Branding settings updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Branding settings update failed: ' . $e->getMessage());

            return redirect()->route('track-supervisor.settings.index')
                ->with('error', 'Failed to update branding settings.');
        }
    }

    /**
     * Display Twitter settings.
     */
    public function twitter()
    {
        $twitterSettings = $this->getTwitterSettings();

        return Inertia::render('TrackSupervisor/Settings/Twitter', [
            'settings' => $twitterSettings
        ]);
    }

    /**
     * Update Twitter settings.
     */
    public function updateTwitter(Request $request)
    {
        try {
            $validated = $request->validate([
                'twitter_api_key' => 'nullable|string',
                'twitter_api_secret' => 'nullable|string',
                'twitter_access_token' => 'nullable|string',
                'twitter_access_token_secret' => 'nullable|string',
                'twitter_auto_post' => 'boolean',
            ]);

            DB::beginTransaction();

            foreach ($validated as $key => $value) {
                SystemSetting::updateOrCreate(
                    ['key' => $key],
                    ['value' => $value, 'group' => 'twitter']
                );
            }

            DB::commit();

            // Clear all settings cache
            SettingsServiceProvider::clearCache();

            // Clear config cache to apply new settings
            \Artisan::call('config:clear');

            return redirect()->route('track-supervisor.settings.index')
                ->with('success', 'Twitter settings updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Twitter settings update failed: ' . $e->getMessage());

            return redirect()->route('track-supervisor.settings.index')
                ->with('error', 'Failed to update Twitter settings.');
        }
    }

    /**
     * Display SMS settings.
     */
    public function sms()
    {
        $smsSettings = $this->getSmsSettings();

        return Inertia::render('TrackSupervisor/Settings/Sms', [
            'settings' => $smsSettings
        ]);
    }

    /**
     * Update SMS settings.
     */
    public function updateSms(Request $request)
    {
        try {
            $validated = $request->validate([
                'sms_provider' => 'nullable|in:twilio,nexmo,messagebird,custom',
                'sms_api_key' => 'nullable|string',
                'sms_api_secret' => 'nullable|string',
                'sms_from_number' => 'nullable|string',
            ]);

            DB::beginTransaction();

            foreach ($validated as $key => $value) {
                SystemSetting::updateOrCreate(
                    ['key' => $key],
                    ['value' => $value, 'group' => 'sms']
                );
            }

            DB::commit();

            // Clear all settings cache
            SettingsServiceProvider::clearCache();

            // Clear config cache to apply new settings
            \Artisan::call('config:clear');

            return redirect()->route('track-supervisor.settings.index')
                ->with('success', 'SMS settings updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('SMS settings update failed: ' . $e->getMessage());

            return redirect()->route('track-supervisor.settings.index')
                ->with('error', 'Failed to update SMS settings.');
        }
    }

    /**
     * Update notification settings.
     */
    public function updateNotifications(Request $request)
    {
        try {
            Log::info('Notification settings update request received', $request->all());

            DB::beginTransaction();

            // Process all notification settings
            $notifications = [
                'notification_email_enabled' => $request->boolean('email_enabled') ? '1' : '0',
                'notification_sms_enabled' => $request->boolean('sms_enabled') ? '1' : '0',
                'notification_push_enabled' => $request->boolean('push_enabled') ? '1' : '0',
                'notification_in_app_enabled' => $request->boolean('in_app_enabled') ? '1' : '0',
            ];

            foreach ($notifications as $key => $value) {
                SystemSetting::updateOrCreate(
                    ['key' => $key],
                    [
                        'value' => $value,
                        'group' => 'notifications',
                        'type' => 'boolean'
                    ]
                );

                Log::info("Notification setting updated: {$key} = {$value}");
            }

            DB::commit();

            // Clear all settings cache
            SettingsServiceProvider::clearCache();

            // Clear config cache to apply new settings
            \Artisan::call('config:clear');

            Log::info('Notification settings updated successfully');

            return redirect()->route('track-supervisor.settings.index')
                ->with('success', 'Notification settings updated successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Notification settings update failed: ' . $e->getMessage());

            return redirect()->route('track-supervisor.settings.index')
                ->with('error', 'Failed to update notification settings: ' . $e->getMessage());
        }
    }

    /**
     * Get SMTP settings.
     */
    private function getSmtpSettings(): array
    {
        return Cache::remember('smtp_settings', 3600, function () {
            $settings = SystemSetting::where('group', 'smtp')
                ->pluck('value', 'key')
                ->toArray();

            return [
                'mail_host' => $settings['mail_host'] ?? '',
                'mail_port' => $settings['mail_port'] ?? 587,
                'mail_username' => $settings['mail_username'] ?? '',
                'mail_password' => $settings['mail_password'] ?? '',
                'mail_encryption' => $settings['mail_encryption'] ?? 'tls',
                'mail_from_address' => $settings['mail_from_address'] ?? '',
                'mail_from_name' => $settings['mail_from_name'] ?? '',
            ];
        });
    }

    /**
     * Get branding settings.
     */
    private function getBrandingSettings(): array
    {
        return Cache::remember('branding_settings', 3600, function () {
            $settings = SystemSetting::where('group', 'branding')
                ->pluck('value', 'key')
                ->toArray();

            $logo = $settings['app_logo'] ?? '';

            return [
                'app_name' => $settings['app_name'] ?? 'GuacPanel',
                'app_logo' => $logo,
                'logo_url' => $logo ? asset('storage/' . $logo) : null,
                'app_favicon' => $settings['app_favicon'] ?? '',
                'primary_color' => $settings['primary_color'] ?? '#0d9488',
                'secondary_color' => $settings['secondary_color'] ?? '#14b8a6',
                'success_color' => $settings['success_color'] ?? '#10b981',
                'danger_color' => $settings['danger_color'] ?? '#ef4444',
            ];
        });
    }

    /**
     * Get Twitter settings.
     */
    private function getTwitterSettings(): array
    {
        return Cache::remember('twitter_settings', 3600, function () {
            $settings = SystemSetting::where('group', 'twitter')
                ->pluck('value', 'key')
                ->toArray();

            return [
                'twitter_api_key' => $settings['twitter_api_key'] ?? '',
                'twitter_api_secret' => $settings['twitter_api_secret'] ?? '',
                'twitter_access_token' => $settings['twitter_access_token'] ?? '',
                'twitter_access_token_secret' => $settings['twitter_access_token_secret'] ?? '',
                'twitter_auto_post' => isset($settings['twitter_auto_post']) && $settings['twitter_auto_post'] === '1',
            ];
        });
    }

    /**
     * Get SMS settings.
     */
    private function getSmsSettings(): array
    {
        return Cache::remember('sms_settings', 3600, function () {
            $settings = SystemSetting::where('group', 'sms')
                ->pluck('value', 'key')
                ->toArray();

            return [
                'sms_provider' => $settings['sms_provider'] ?? 'twilio',
                'sms_api_key' => $settings['sms_api_key'] ?? '',
                'sms_api_secret' => $settings['sms_api_secret'] ?? '',
                'sms_from_number' => $settings['sms_from_number'] ?? '',
            ];
        });
    }

    /**
     * Get notification settings.
     */
    private function getNotificationSettings(): array
    {
        return Cache::remember('notification_settings', 3600, function () {
            // Get all notification settings
            $settings = SystemSetting::where('group', 'notifications')
                ->pluck('value', 'key')
                ->toArray();

            return [
                'email_enabled' => isset($settings['notification_email_enabled']) && $settings['notification_email_enabled'] === '1',
                'sms_enabled' => isset($settings['notification_sms_enabled']) && $settings['notification_sms_enabled'] === '1',
                'push_enabled' => isset($settings['notification_push_enabled']) && $settings['notification_push_enabled'] === '1',
                'in_app_enabled' => isset($settings['notification_in_app_enabled']) && $settings['notification_in_app_enabled'] === '1',
            ];
        });
    }
}
