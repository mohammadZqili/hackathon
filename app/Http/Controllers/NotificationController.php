<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use App\Models\Setting;

class NotificationController extends Controller
{
    /**
     * Get all notifications for the authenticated user
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        
        // Check user's personal notification preference
        if (!$user->notifications_enabled) {
            if ($request->wantsJson()) {
                return response()->json([
                    'notifications' => [],
                    'unread_count' => 0,
                    'has_more' => false,
                    'enabled' => false
                ]);
            }
            
            return Inertia::render('Notifications/Index', [
                'notifications' => [],
                'enabled' => false
            ]);
        }
        
        // Also check global system setting if exists
        $settings = Setting::first();
        $systemNotificationsEnabled = $settings ? $settings->notifications_enabled : true;
        
        if (!$systemNotificationsEnabled) {
            if ($request->wantsJson()) {
                return response()->json([
                    'notifications' => [],
                    'unread_count' => 0,
                    'has_more' => false,
                    'enabled' => false,
                    'disabled_by_admin' => true
                ]);
            }
            
            return Inertia::render('Notifications/Index', [
                'notifications' => [],
                'enabled' => false,
                'disabled_by_admin' => true
            ]);
        }
        
        $notifications = $user->notifications()
            ->latest()
            ->paginate(20);
            
        if ($request->wantsJson()) {
            return response()->json([
                'notifications' => $notifications->items(),
                'unread_count' => $user->unreadNotifications()->count(),
                'has_more' => $notifications->hasMorePages(),
                'enabled' => true
            ]);
        }
        
        return Inertia::render('Notifications/Index', [
            'notifications' => $notifications,
            'enabled' => true
        ]);
    }
    
    /**
     * Mark a notification as read
     */
    public function markAsRead(Request $request, $id)
    {
        $notification = Auth::user()->notifications()->findOrFail($id);
        $notification->markAsRead();
        
        return response()->json([
            'success' => true,
            'message' => 'Notification marked as read'
        ]);
    }
    
    /**
     * Mark all notifications as read
     */
    public function markAllAsRead(Request $request)
    {
        Auth::user()->unreadNotifications->markAsRead();
        
        return response()->json([
            'success' => true,
            'message' => 'All notifications marked as read'
        ]);
    }
    
    /**
     * Delete a notification
     */
    public function destroy(Request $request, $id)
    {
        $notification = Auth::user()->notifications()->findOrFail($id);
        $notification->delete();
        
        return response()->json([
            'success' => true,
            'message' => 'Notification deleted'
        ]);
    }
    
    /**
     * Get unread notifications count
     */
    public function unreadCount(Request $request)
    {
        return response()->json([
            'count' => Auth::user()->unreadNotifications()->count()
        ]);
    }
    
    /**
     * Toggle user's notification preferences
     */
    public function toggleNotifications(Request $request)
    {
        $user = Auth::user();
        $user->notifications_enabled = !$user->notifications_enabled;
        $user->save();
        
        return response()->json([
            'success' => true,
            'enabled' => $user->notifications_enabled,
            'message' => $user->notifications_enabled 
                ? 'Notifications enabled' 
                : 'Notifications disabled'
        ]);
    }
    
    /**
     * Update user's notification preferences
     */
    public function updatePreferences(Request $request)
    {
        $request->validate([
            'notifications_enabled' => 'boolean',
            'email_notifications_enabled' => 'boolean'
        ]);
        
        $user = Auth::user();
        
        if ($request->has('notifications_enabled')) {
            $user->notifications_enabled = $request->notifications_enabled;
        }
        
        if ($request->has('email_notifications_enabled')) {
            $user->email_notifications_enabled = $request->email_notifications_enabled;
        }
        
        $user->save();
        
        return response()->json([
            'success' => true,
            'preferences' => [
                'notifications_enabled' => $user->notifications_enabled,
                'email_notifications_enabled' => $user->email_notifications_enabled
            ],
            'message' => 'Notification preferences updated'
        ]);
    }
}