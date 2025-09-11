<?php

namespace App\Http\Controllers\SystemAdmin;

use App\Http\Controllers\Controller;
use App\Services\SettingService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Artisan;

class SettingsController extends Controller
{
    protected SettingService $settingService;
    
    public function __construct(SettingService $settingService)
    {
        $this->settingService = $settingService;
    }

    /**
     * Display settings page
     */
    public function index()
    {
        $settings = $this->settingService->getAllSettings(auth()->user());
        
        return Inertia::render('SystemAdmin/Settings/Index', [
            'settings' => $settings
        ]);
    }

    /**
     * Display branding settings
     */
    public function branding()
    {
        $settings = $this->settingService->getBrandingSettings(auth()->user());
        
        return Inertia::render('SystemAdmin/Settings/Branding', [
            'settings' => $settings
        ]);
    }

    /**
     * Update branding settings
     */
    public function updateBranding(Request $request)
    {
        $validated = $request->validate([
            'app_name' => 'required|string|max:255',
            'app_logo' => 'nullable|string',
            'primary_color' => 'required|string|max:7',
            'secondary_color' => 'required|string|max:7',
            'footer_text' => 'nullable|string',
        ]);
        
        try {
            $result = $this->settingService->updateBrandingSettings($validated, auth()->user());
            
            // Clear config cache
            Artisan::call('config:clear');
            
            return back()->with('success', $result['message']);
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    /**
     * Display SMTP settings
     */
    public function smtp()
    {
        $settings = $this->settingService->getSmtpSettings(auth()->user());
        
        return Inertia::render('SystemAdmin/Settings/Smtp', [
            'settings' => $settings
        ]);
    }

    /**
     * Update SMTP settings
     */
    public function updateSmtp(Request $request)
    {
        $validated = $request->validate([
            'smtp_host' => 'required|string',
            'smtp_port' => 'required|integer',
            'smtp_username' => 'required|string',
            'smtp_password' => 'nullable|string',
            'smtp_encryption' => 'nullable|in:tls,ssl',
            'smtp_from_address' => 'required|email',
            'smtp_from_name' => 'required|string',
        ]);
        
        try {
            $result = $this->settingService->updateSmtpSettings($validated, auth()->user());
            
            // Clear config cache
            Artisan::call('config:clear');
            
            return back()->with('success', $result['message']);
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    /**
     * Display notification settings
     */
    public function notifications()
    {
        $settings = $this->settingService->getNotificationSettings(auth()->user());
        
        return Inertia::render('SystemAdmin/Settings/Notifications', [
            'settings' => $settings
        ]);
    }

    /**
     * Update notification settings
     */
    public function updateNotifications(Request $request)
    {
        $validated = $request->validate([
            'email_enabled' => 'boolean',
            'sms_enabled' => 'boolean',
            'push_enabled' => 'boolean',
            'in_app_enabled' => 'boolean',
            'email_digest' => 'nullable|in:daily,weekly,never',
            'quiet_hours_enabled' => 'boolean',
            'quiet_hours_start' => 'nullable|date_format:H:i',
            'quiet_hours_end' => 'nullable|date_format:H:i',
        ]);
        
        try {
            $result = $this->settingService->updateNotificationSettings($validated, auth()->user());
            
            // Clear config cache
            Artisan::call('config:clear');
            
            return back()->with('success', $result['message']);
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    /**
     * Display SMS settings
     */
    public function sms()
    {
        $settings = $this->settingService->getSmsSettings(auth()->user());
        
        return Inertia::render('SystemAdmin/Settings/Sms', [
            'settings' => $settings
        ]);
    }

    /**
     * Update SMS settings
     */
    public function updateSms(Request $request)
    {
        $validated = $request->validate([
            'sms_provider' => 'required|in:twilio,nexmo,textlocal',
            'sms_api_key' => 'required|string',
            'sms_api_secret' => 'nullable|string',
            'sms_from_number' => 'required|string',
        ]);
        
        try {
            $result = $this->settingService->updateSmsSettings($validated, auth()->user());
            
            // Clear config cache
            Artisan::call('config:clear');
            
            return back()->with('success', $result['message']);
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    /**
     * Display Twitter settings
     */
    public function twitter()
    {
        $settings = $this->settingService->getTwitterSettings(auth()->user());
        
        return Inertia::render('SystemAdmin/Settings/Twitter', [
            'settings' => $settings
        ]);
    }

    /**
     * Update Twitter settings
     */
    public function updateTwitter(Request $request)
    {
        $validated = $request->validate([
            'twitter_api_key' => 'nullable|string',
            'twitter_api_secret' => 'nullable|string',
            'twitter_access_token' => 'nullable|string',
            'twitter_access_token_secret' => 'nullable|string',
            'twitter_auto_post' => 'boolean',
        ]);
        
        try {
            $result = $this->settingService->updateTwitterSettings($validated, auth()->user());
            
            // Clear config cache
            Artisan::call('config:clear');
            
            return back()->with('success', $result['message']);
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }
}
