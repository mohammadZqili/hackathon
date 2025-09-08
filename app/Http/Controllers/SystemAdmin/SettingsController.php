<?php

namespace App\Http\Controllers\SystemAdmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Setting;
use Illuminate\Support\Facades\Cache;

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
        ];

        return Inertia::render('SystemAdmin/Settings/Index', [
            'settings' => $settings
        ]);
    }

    /**
     * Display SMTP settings.
     */
    public function smtp()
    {
        $smtpSettings = $this->getSmtpSettings();

        return Inertia::render('SystemAdmin/Settings/Smtp', [
            'settings' => $smtpSettings
        ]);
    }

    /**
     * Update SMTP settings.
     */
    public function updateSmtp(Request $request)
    {
        $validated = $request->validate([
            'mail_host' => 'required|string',
            'mail_port' => 'required|integer',
            'mail_username' => 'required|string',
            'mail_password' => 'required|string',
            'mail_encryption' => 'required|in:tls,ssl',
            'mail_from_address' => 'required|email',
            'mail_from_name' => 'required|string',
        ]);

        foreach ($validated as $key => $value) {
            Setting::updateOrCreate(
                ['key' => $key],
                ['value' => $value]
            );
        }

        Cache::forget('smtp_settings');

        return redirect()->route('system-admin.settings.smtp')
            ->with('success', 'SMTP settings updated successfully.');
    }

    /**
     * Display branding settings.
     */
    public function branding()
    {
        $brandingSettings = $this->getBrandingSettings();

        return Inertia::render('SystemAdmin/Settings/Branding', [
            'settings' => $brandingSettings
        ]);
    }

    /**
     * Update branding settings.
     */
    public function updateBranding(Request $request)
    {
        $validated = $request->validate([
            'app_name' => 'required|string|max:255',
            'app_logo' => 'nullable|image|max:2048',
            'app_favicon' => 'nullable|image|max:1024',
            'primary_color' => 'required|string|max:7',
            'secondary_color' => 'required|string|max:7',
        ]);

        // Handle file uploads
        if ($request->hasFile('app_logo')) {
            $logoPath = $request->file('app_logo')->store('branding', 'public');
            $validated['app_logo'] = $logoPath;
        }

        if ($request->hasFile('app_favicon')) {
            $faviconPath = $request->file('app_favicon')->store('branding', 'public');
            $validated['app_favicon'] = $faviconPath;
        }

        foreach ($validated as $key => $value) {
            Setting::updateOrCreate(
                ['key' => $key],
                ['value' => $value]
            );
        }

        Cache::forget('branding_settings');

        return redirect()->route('system-admin.settings.branding')
            ->with('success', 'Branding settings updated successfully.');
    }

    /**
     * Display Twitter settings.
     */
    public function twitter()
    {
        $twitterSettings = $this->getTwitterSettings();

        return Inertia::render('SystemAdmin/Settings/Twitter', [
            'settings' => $twitterSettings
        ]);
    }

    /**
     * Update Twitter settings.
     */
    public function updateTwitter(Request $request)
    {
        $validated = $request->validate([
            'twitter_api_key' => 'required|string',
            'twitter_api_secret' => 'required|string',
            'twitter_access_token' => 'required|string',
            'twitter_access_token_secret' => 'required|string',
            'twitter_auto_post' => 'boolean',
        ]);

        foreach ($validated as $key => $value) {
            Setting::updateOrCreate(
                ['key' => $key],
                ['value' => $value]
            );
        }

        Cache::forget('twitter_settings');

        return redirect()->route('system-admin.settings.twitter')
            ->with('success', 'Twitter settings updated successfully.');
    }

    /**
     * Display SMS settings.
     */
    public function sms()
    {
        $smsSettings = $this->getSmsSettings();

        return Inertia::render('SystemAdmin/Settings/Sms', [
            'settings' => $smsSettings
        ]);
    }

    /**
     * Update SMS settings.
     */
    public function updateSms(Request $request)
    {
        $validated = $request->validate([
            'sms_provider' => 'required|in:twilio,nexmo,textlocal',
            'sms_api_key' => 'required|string',
            'sms_api_secret' => 'required|string',
            'sms_from_number' => 'required|string',
        ]);

        foreach ($validated as $key => $value) {
            Setting::updateOrCreate(
                ['key' => $key],
                ['value' => $value]
            );
        }

        Cache::forget('sms_settings');

        return redirect()->route('system-admin.settings.sms')
            ->with('success', 'SMS settings updated successfully.');
    }

    /**
     * Get SMTP settings.
     */
    private function getSmtpSettings(): array
    {
        return Cache::remember('smtp_settings', 3600, function () {
            return [
                'mail_host' => Setting::where('key', 'mail_host')->value('value') ?? '',
                'mail_port' => Setting::where('key', 'mail_port')->value('value') ?? '587',
                'mail_username' => Setting::where('key', 'mail_username')->value('value') ?? '',
                'mail_password' => Setting::where('key', 'mail_password')->value('value') ?? '',
                'mail_encryption' => Setting::where('key', 'mail_encryption')->value('value') ?? 'tls',
                'mail_from_address' => Setting::where('key', 'mail_from_address')->value('value') ?? '',
                'mail_from_name' => Setting::where('key', 'mail_from_name')->value('value') ?? '',
            ];
        });
    }

    /**
     * Get branding settings.
     */
    private function getBrandingSettings(): array
    {
        return Cache::remember('branding_settings', 3600, function () {
            return [
                'app_name' => Setting::where('key', 'app_name')->value('value') ?? 'Ruman Hackathon',
                'app_logo' => Setting::where('key', 'app_logo')->value('value') ?? '',
                'app_favicon' => Setting::where('key', 'app_favicon')->value('value') ?? '',
                'primary_color' => Setting::where('key', 'primary_color')->value('value') ?? '#3B82F6',
                'secondary_color' => Setting::where('key', 'secondary_color')->value('value') ?? '#10B981',
            ];
        });
    }

    /**
     * Get Twitter settings.
     */
    private function getTwitterSettings(): array
    {
        return Cache::remember('twitter_settings', 3600, function () {
            return [
                'twitter_api_key' => Setting::where('key', 'twitter_api_key')->value('value') ?? '',
                'twitter_api_secret' => Setting::where('key', 'twitter_api_secret')->value('value') ?? '',
                'twitter_access_token' => Setting::where('key', 'twitter_access_token')->value('value') ?? '',
                'twitter_access_token_secret' => Setting::where('key', 'twitter_access_token_secret')->value('value') ?? '',
                'twitter_auto_post' => Setting::where('key', 'twitter_auto_post')->value('value') ?? false,
            ];
        });
    }

    /**
     * Get SMS settings.
     */
    private function getSmsSettings(): array
    {
        return Cache::remember('sms_settings', 3600, function () {
            return [
                'sms_provider' => Setting::where('key', 'sms_provider')->value('value') ?? 'twilio',
                'sms_api_key' => Setting::where('key', 'sms_api_key')->value('value') ?? '',
                'sms_api_secret' => Setting::where('key', 'sms_api_secret')->value('value') ?? '',
                'sms_from_number' => Setting::where('key', 'sms_from_number')->value('value') ?? '',
            ];
        });
    }
}
