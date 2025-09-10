<?php

namespace App\Http\Controllers;

use App\Helpers\Settings;
use App\Notifications\ExampleNotification;
use Illuminate\Http\Request;

/**
 * Example controller showing how to use the Settings helper
 */
class ExampleSettingsUsageController extends Controller
{
    /**
     * Example 1: Send email with configured SMTP settings
     */
    public function sendEmail(Request $request)
    {
        // The SMTP settings are automatically applied from the database
        // via the SettingsServiceProvider, so you can just send mail normally
        
        $user = auth()->user();
        
        // Check if email notifications are enabled
        if (Settings::emailNotificationsEnabled()) {
            $user->notify(new ExampleNotification([
                'message' => 'This is a test notification',
                'type' => 'info'
            ]));
            
            return response()->json(['message' => 'Email sent successfully']);
        }
        
        return response()->json(['message' => 'Email notifications are disabled']);
    }
    
    /**
     * Example 2: Get branding colors for API response
     */
    public function getBranding()
    {
        return response()->json([
            'app_name' => Settings::get('app.name'),
            'colors' => Settings::getBrandingColors(),
            'logo_url' => Settings::getLogoUrl(),
        ]);
    }
    
    /**
     * Example 3: Check Twitter auto-post before posting
     */
    public function postToTwitter(Request $request)
    {
        if (!Settings::twitterAutoPostEnabled()) {
            return response()->json(['message' => 'Twitter auto-post is disabled']);
        }
        
        // Get Twitter credentials from settings
        $twitterConfig = [
            'api_key' => Settings::get('twitter.api_key'),
            'api_secret' => Settings::get('twitter.api_secret'),
            'access_token' => Settings::get('twitter.access_token'),
            'access_token_secret' => Settings::get('twitter.access_token_secret'),
        ];
        
        // Use Twitter API with these credentials
        // ... Twitter posting logic here ...
        
        return response()->json(['message' => 'Posted to Twitter']);
    }
    
    /**
     * Example 4: Using SMTP configuration directly
     */
    public function getMailConfig()
    {
        $smtpConfig = Settings::getSmtpConfig();
        
        // You can use this config for custom mail sending
        // or debugging purposes
        
        return response()->json([
            'smtp_configured' => !empty($smtpConfig['host']),
            'from_address' => $smtpConfig['from_address'],
            'from_name' => $smtpConfig['from_name'],
        ]);
    }
    
    /**
     * Example 5: Conditional notification channels
     */
    public function sendMultiChannelNotification(Request $request)
    {
        $user = auth()->user();
        $channels = [];
        
        // Build channels array based on settings
        if (Settings::emailNotificationsEnabled()) {
            $channels[] = 'Email';
        }
        
        if (Settings::smsNotificationsEnabled()) {
            $channels[] = 'SMS';
        }
        
        if (Settings::pushNotificationsEnabled()) {
            $channels[] = 'Push';
        }
        
        if (Settings::inAppNotificationsEnabled()) {
            $channels[] = 'In-App';
        }
        
        // The notification class will automatically check these settings
        // and send via appropriate channels
        $user->notify(new ExampleNotification([
            'message' => $request->input('message'),
            'type' => $request->input('type', 'info')
        ]));
        
        return response()->json([
            'message' => 'Notification sent',
            'channels' => $channels
        ]);
    }
}
