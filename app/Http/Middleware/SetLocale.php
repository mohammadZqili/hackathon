<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Cookie;

class SetLocale
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // Priority order for locale detection:
        // 1. Session (for authenticated users)
        // 2. Cookie (for guest users who have selected a language)
        // 3. User preference (from database)
        // 4. Browser preference
        // 5. Default to English

        $locale = null;

        // First check session
        $locale = Session::get('locale');

        // If not in session, check cookie
        if (!$locale) {
            $locale = Cookie::get('locale');
        }

        // If authenticated and no locale yet, check user preference
        if (!$locale && auth()->check()) {
            $locale = auth()->user()->locale;
        }

        // If still no locale, check browser preference
        if (!$locale) {
            $locale = $request->getPreferredLanguage(['en', 'ar']);
        }

        // Validate and default to English if invalid
        if (!in_array($locale, ['en', 'ar'])) {
            $locale = 'en';
        }

        // Set the locale in the application
        App::setLocale($locale);

        // Store in session for current request cycle
        Session::put('locale', $locale);

        // Set direction based on locale
        $direction = $locale === 'ar' ? 'rtl' : 'ltr';
        Session::put('direction', $direction);

        // Share with all views for Blade templates
        view()->share('locale', $locale);
        view()->share('direction', $direction);

        $response = $next($request);

        // Set cookie for guest users (expires in 1 year)
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
}