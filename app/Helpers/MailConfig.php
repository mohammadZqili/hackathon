<?php

namespace App\Helpers;

use App\Models\SystemSetting;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Cache;

class MailConfig
{
    /**
     * Configure mail settings from database before sending
     */
    public static function configure(): void
    {
        // Get cached mail settings or load from database
        $settings = Cache::remember('mail_settings_config', 300, function () {
            return [
                'host' => SystemSetting::get('mail_host'),
                'port' => SystemSetting::get('mail_port'),
                'username' => SystemSetting::get('mail_username'),
                'password' => SystemSetting::get('mail_password'),
                'encryption' => SystemSetting::get('mail_encryption'),
                'from_address' => SystemSetting::get('mail_from_address'),
                'from_name' => SystemSetting::get('mail_from_name'),
            ];
        });

        // Apply settings to mail configuration if they exist
        if ($settings['host']) {
            Config::set('mail.mailers.smtp.host', $settings['host']);
        }
        if ($settings['port']) {
            Config::set('mail.mailers.smtp.port', $settings['port']);
        }
        if ($settings['username']) {
            Config::set('mail.mailers.smtp.username', $settings['username']);
        }
        if ($settings['password']) {
            Config::set('mail.mailers.smtp.password', $settings['password']);
        }
        if ($settings['encryption'] !== null) {
            Config::set('mail.mailers.smtp.encryption', $settings['encryption'] ?: null);
        }
        if ($settings['from_address']) {
            Config::set('mail.from.address', $settings['from_address']);
        }
        if ($settings['from_name']) {
            Config::set('mail.from.name', $settings['from_name']);
        }
    }

    /**
     * Clear mail configuration cache
     */
    public static function clearCache(): void
    {
        Cache::forget('mail_settings_config');
    }
}