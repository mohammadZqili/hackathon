<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Cache;

class LanguageController extends Controller
{
    /**
     * Switch the application language
     */
    public function switch(Request $request, $locale)
    {
        // Validate locale
        if (!in_array($locale, ['en', 'ar'])) {
            if ($request->wantsJson()) {
                return response()->json(['error' => 'Invalid locale'], 400);
            }
            abort(404);
        }

        // Set locale for current request
        App::setLocale($locale);

        // Store in session
        Session::put('locale', $locale);

        // Set direction based on locale
        $direction = $locale === 'ar' ? 'rtl' : 'ltr';
        Session::put('direction', $direction);

        // Update user preference in database if logged in
        if (auth()->check()) {
            auth()->user()->update(['locale' => $locale]);
        }

        // Clear any cached translations
        Cache::forget('translations_' . $locale);

        // For AJAX/JSON requests
        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'locale' => $locale,
                'direction' => $direction,
                'message' => __('messages.language_switched')
            ]);
        }

        // For regular requests, redirect back with cookies
        $response = redirect()->back()->with('success', __('messages.language_switched'));

        // Set cookies for guest users (expires in 1 year)
        if (!auth()->check()) {
            $response->withCookie(
                Cookie::make('locale', $locale, 60 * 24 * 365)
            );
            $response->withCookie(
                Cookie::make('direction', $direction, 60 * 24 * 365)
            );
        }

        return $response;
    }
    
    /**
     * Get current language settings
     */
    public function current()
    {
        return response()->json([
            'locale' => app()->getLocale(),
            'direction' => Session::get('direction', 'ltr'),
            'available' => [
                'en' => [
                    'name' => 'English',
                    'native' => 'English',
                    'flag' => '🇬🇧',
                    'direction' => 'ltr'
                ],
                'ar' => [
                    'name' => 'Arabic',
                    'native' => 'العربية',
                    'flag' => '🇸🇦',
                    'direction' => 'rtl'
                ]
            ]
        ]);
    }
}