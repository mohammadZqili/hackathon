<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\SystemSetting;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Schema;

class SettingsServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Only load settings if the database is connected and table exists
        try {
            if (Schema::hasTable('system_settings')) {
                $this->loadSettings();
            }
        } catch (\Exception $e) {
            // Silently fail if database is not available (during migrations, etc.)
        }
    }

    /**
     * Load settings from database and apply to config
     */
    protected function loadSettings(): void
    {
        // Cache the settings for performance
        $settings = cache()->remember('app_settings', 3600, function () {
            return SystemSetting::all()->pluck('value', 'key')->toArray();
        });

        // Apply SMTP settings to mail configuration
        if (isset($settings['mail_host'])) {
            Config::set('mail.mailers.smtp.host', $settings['mail_host']);
        }
        if (isset($settings['mail_port'])) {
            Config::set('mail.mailers.smtp.port', $settings['mail_port']);
        }
        if (isset($settings['mail_username'])) {
            Config::set('mail.mailers.smtp.username', $settings['mail_username']);
        }
        if (isset($settings['mail_password'])) {
            Config::set('mail.mailers.smtp.password', $settings['mail_password']);
        }
        if (isset($settings['mail_encryption'])) {
            Config::set('mail.mailers.smtp.encryption', $settings['mail_encryption']);
        }
        if (isset($settings['mail_from_address'])) {
            Config::set('mail.from.address', $settings['mail_from_address']);
        }
        if (isset($settings['mail_from_name'])) {
            Config::set('mail.from.name', $settings['mail_from_name']);
        }

        // Apply branding settings to app configuration
        if (isset($settings['app_name'])) {
            Config::set('app.name', $settings['app_name']);
        }

        // Store other settings in a custom config namespace
        Config::set('settings.branding', [
            'primary_color' => $settings['primary_color'] ?? '#0d9488',
            'secondary_color' => $settings['secondary_color'] ?? '#14b8a6',
            'success_color' => $settings['success_color'] ?? '#10b981',
            'danger_color' => $settings['danger_color'] ?? '#ef4444',
            'logo' => $settings['app_logo'] ?? null,
            'favicon' => $settings['app_favicon'] ?? null,
        ]);

        // Store notification settings
        Config::set('settings.notifications', [
            'email_enabled' => filter_var($settings['notification_email_enabled'] ?? true, FILTER_VALIDATE_BOOLEAN),
            'sms_enabled' => filter_var($settings['notification_sms_enabled'] ?? false, FILTER_VALIDATE_BOOLEAN),
            'push_enabled' => filter_var($settings['notification_push_enabled'] ?? false, FILTER_VALIDATE_BOOLEAN),
            'in_app_enabled' => filter_var($settings['notification_in_app_enabled'] ?? true, FILTER_VALIDATE_BOOLEAN),
        ]);

        // Store Twitter settings (if needed)
        Config::set('services.twitter', [
            'api_key' => $settings['twitter_api_key'] ?? null,
            'api_secret' => $settings['twitter_api_secret'] ?? null,
            'access_token' => $settings['twitter_access_token'] ?? null,
            'access_token_secret' => $settings['twitter_access_token_secret'] ?? null,
            'auto_post' => filter_var($settings['twitter_auto_post'] ?? false, FILTER_VALIDATE_BOOLEAN),
        ]);

        // Store SMS settings (for future use)
        Config::set('services.sms', [
            'provider' => $settings['sms_provider'] ?? 'twilio',
            'api_key' => $settings['sms_api_key'] ?? null,
            'api_secret' => $settings['sms_api_secret'] ?? null,
            'from_number' => $settings['sms_from_number'] ?? null,
        ]);
    }

    /**
     * Clear settings cache when settings are updated
     */
    public static function clearCache(): void
    {
        cache()->forget('app_settings');
        cache()->forget('smtp_settings');
        cache()->forget('branding_settings');
        cache()->forget('twitter_settings');
        cache()->forget('sms_settings');
        cache()->forget('notification_settings');
    }
}
