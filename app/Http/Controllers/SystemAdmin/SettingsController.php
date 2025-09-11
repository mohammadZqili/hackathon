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
}
