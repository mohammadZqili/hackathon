<?php

namespace App\Http\Controllers\SystemAdmin;

use App\Http\Controllers\Controller;
use App\Services\SettingService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;

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
            'app_name' => 'nullable|string|max:255',
            'app_logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'primary_color' => 'nullable|string|max:7',
            'secondary_color' => 'nullable|string|max:7',
            'footer_text' => 'nullable|string',
        ]);

        // Handle logo upload if present
        if ($request->hasFile('app_logo')) {
            $logoFile = $request->file('app_logo');

            // Store the logo in the public storage
            $logoPath = $logoFile->store('logos', 'public');
            $validated['app_logo'] = '/storage/' . $logoPath;
        }

        // Filter out empty values except for app_logo which might be a path
        $validated = array_filter($validated, function($value) {
            return $value !== null && $value !== '';
        });

        try {
            $result = $this->settingService->updateBrandingSettings($validated, auth()->user());

            // Clear config cache
            Artisan::call('config:clear');

            return redirect()->route('system-admin.settings.branding')
                ->with('success', $result['message'])
                ->with('settings', $this->settingService->getBrandingSettings(auth()->user()));
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
            'smtp_host' => 'nullable|string',
            'smtp_port' => 'nullable|integer',
            'smtp_username' => 'nullable|string',
            'smtp_password' => 'nullable|string',
            'smtp_encryption' => 'nullable|in:tls,ssl,',
            'smtp_from_address' => 'nullable|email',
            'smtp_from_name' => 'nullable|string',
        ]);

        // Filter out empty values
        $validated = array_filter($validated, function($value) {
            return $value !== null && $value !== '';
        });

        try {
            $result = $this->settingService->updateSmtpSettings($validated, auth()->user());

            // Clear config cache
            Artisan::call('config:clear');

            return redirect()->route('system-admin.settings.smtp')->with('success', $result['message']);
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    /**
     * Test SMTP connection
     */
    public function testSmtp(Request $request)
    {
        try {
            // Configure mail settings from database
            \App\Helpers\MailConfig::configure();

            // Send test email to current user
            $user = auth()->user();

            \Illuminate\Support\Facades\Mail::raw('This is a test email from ' . config('app.name') . ' to verify SMTP settings are working correctly.', function ($message) use ($user) {
                $message->to($user->email)
                    ->subject('SMTP Test Email - ' . config('app.name'));
            });

            return back()->with('success', 'Test email sent successfully to ' . $user->email);
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Failed to send test email: ' . $e->getMessage()]);
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
            'sms_provider' => 'nullable|in:twilio,nexmo,textlocal',
            'sms_api_key' => 'nullable|string',
            'sms_api_secret' => 'nullable|string',
            'sms_from_number' => 'nullable|string',
        ]);

        // Filter out empty values
        $validated = array_filter($validated, function($value) {
            return $value !== null && $value !== '';
        });

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
