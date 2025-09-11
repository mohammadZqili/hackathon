<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\App;

class LanguageController extends Controller
{
    /**
     * Switch the application language
     */
    public function switch(Request $request, $locale)
    {
        if (!in_array($locale, ['en', 'ar'])) {
            abort(404);
        }
        
        // Set locale
        App::setLocale($locale);
        Session::put('locale', $locale);
        
        // Set direction
        $direction = $locale === 'ar' ? 'rtl' : 'ltr';
        Session::put('direction', $direction);
        
        // Update user preference if logged in
        if (auth()->check()) {
            auth()->user()->update(['locale' => $locale]);
        }
        
        return redirect()->back()->with('success', __('messages.language_switched'));
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