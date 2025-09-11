<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;

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
        // Get locale from session, user preference, or browser
        $locale = Session::get('locale');
        
        if (!$locale && auth()->check()) {
            $locale = auth()->user()->locale;
        }
        
        if (!$locale) {
            $locale = $request->getPreferredLanguage(['en', 'ar']);
        }
        
        // Default to English if no preference
        if (!in_array($locale, ['en', 'ar'])) {
            $locale = 'en';
        }
        
        // Set the locale
        App::setLocale($locale);
        Session::put('locale', $locale);
        
        // Set direction based on locale
        $direction = in_array($locale, ['ar']) ? 'rtl' : 'ltr';
        Session::put('direction', $direction);
        
        // Share with all views
        view()->share('locale', $locale);
        view()->share('direction', $direction);
        
        return $next($request);
    }
}