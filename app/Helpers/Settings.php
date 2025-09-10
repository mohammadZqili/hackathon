<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Config;

class Settings
{
    /**
     * Get a setting value from config
     * 
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public static function get($key, $default = null)
    {
        // Check if it's a mail setting
        if (str_starts_with($key, 'mail.')) {
            return Config::get($key, $default);
        }
        
        // Check if it's an app setting
        if ($key === 'app.name') {
            return Config::get('app.name', $default);
        }
        
        // Check custom settings
        if (str_starts_with($key, 'branding.')) {
            $brandingKey = str_replace('branding.', '', $key);
            return Config::get("settings.branding.{$brandingKey}", $default);
        }
        
        if (str_starts_with($key, 'notifications.')) {
            $notificationKey = str_replace('notifications.', '', $key);
            return Config::get("settings.notifications.{$notificationKey}", $default);
        }
        
        if (str_starts_with($key, 'twitter.')) {
            $twitterKey = str_replace('twitter.', '', $key);
            return Config::get("services.twitter.{$twitterKey}", $default);
        }
        
        if (str_starts_with($key, 'sms.')) {
            $smsKey = str_replace('sms.', '', $key);
            return Config::get("services.sms.{$smsKey}", $default);
        }
        
        return Config::get($key, $default);
    }
    
    /**
     * Check if email notifications are enabled
     */
    public static function emailNotificationsEnabled(): bool
    {
        return (bool) self::get('notifications.email_enabled', true);
    }
    
    /**
     * Check if SMS notifications are enabled
     */
    public static function smsNotificationsEnabled(): bool
    {
        return (bool) self::get('notifications.sms_enabled', false);
    }
    
    /**
     * Check if push notifications are enabled
     */
    public static function pushNotificationsEnabled(): bool
    {
        return (bool) self::get('notifications.push_enabled', false);
    }
    
    /**
     * Check if in-app notifications are enabled
     */
    public static function inAppNotificationsEnabled(): bool
    {
        return (bool) self::get('notifications.in_app_enabled', true);
    }
    
    /**
     * Get branding colors
     */
    public static function getBrandingColors(): array
    {
        return [
            'primary' => self::get('branding.primary_color', '#0d9488'),
            'secondary' => self::get('branding.secondary_color', '#14b8a6'),
            'success' => self::get('branding.success_color', '#10b981'),
            'danger' => self::get('branding.danger_color', '#ef4444'),
        ];
    }
    
    /**
     * Get app logo URL
     */
    public static function getLogoUrl(): ?string
    {
        $logo = self::get('branding.logo');
        return $logo ? asset('storage/' . $logo) : null;
    }
    
    /**
     * Check if Twitter auto-post is enabled
     */
    public static function twitterAutoPostEnabled(): bool
    {
        return (bool) self::get('twitter.auto_post', false);
    }
    
    /**
     * Get SMTP configuration
     */
    public static function getSmtpConfig(): array
    {
        return [
            'host' => Config::get('mail.mailers.smtp.host'),
            'port' => Config::get('mail.mailers.smtp.port'),
            'username' => Config::get('mail.mailers.smtp.username'),
            'password' => Config::get('mail.mailers.smtp.password'),
            'encryption' => Config::get('mail.mailers.smtp.encryption'),
            'from_address' => Config::get('mail.from.address'),
            'from_name' => Config::get('mail.from.name'),
        ];
    }
}
