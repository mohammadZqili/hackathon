<?php

namespace App\Http\Middleware;

use App\Models\Personalisation;
use App\Models\SystemNotice;
use App\Helpers\Settings;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\App;
use Inertia\Middleware;
use Laravolt\Avatar\Avatar;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that's loaded on the first page visit.
     *
     * @see https://inertiajs.com/server-side-setup#root-template
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determines the current asset version.
     *
     * @see https://inertiajs.com/asset-versioning
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @see https://inertiajs.com/shared-data
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        $avatar = new Avatar(config('laravolt.avatar'));
        $personalisation = Personalisation::first() ?? new Personalisation();

        return array_merge(
            parent::share($request),
            [
                'auth' => [
                    'user' => $request->user() ? [
                        'id' => $request->user()->id,
                        'name' => $request->user()->name,
                        'email' => $request->user()->email,
                        'roles' => $request->user()->roles->pluck('name'),
                        'permissions' => $request->user()->getPermissionsViaRoles()->pluck('name'),
                        'primary_role' => $request->user()->getRoleNames()->first(),
                        'team_id' => $request->user()->team_id ?? null,
                        'track_id' => $request->user()->track_id ?? null,
                        'avatar' => $avatar
                            ->create($request->user()->name)
                            ->setTheme('pastel')
                            ->setFontSize(48)
                            ->setDimension(100, 100)
                            ->toBase64(),
                    ] : null,
                ],

                'csrf_token' => csrf_token(),

                'flash' => [
                    'message' => fn() => $request->session()->get('message'),
                    'success' => fn() => $request->session()->get('success'),
                    'error' => fn() => $request->session()->get('error'),
                    'status' => fn() => $request->session()->get('status'),
                    'warning' => fn() => $request->session()->get('warning'),
                    'info' => fn() => $request->session()->get('info'),
                    'danger' => fn() => $request->session()->get('danger'),
                    'recovery-codes-generated' => fn() => $request->session()->get('recovery-codes-generated'),
                    'two-factor-authentication-enabled' => fn() => $request->session()->get('two-factor-authentication-enabled'),
                    'two-factor-authentication-disabled' => fn() => $request->session()->get('two-factor-authentication-disabled'),
                    'verification-link-sent' => fn() => $request->session()->get('verification-link-sent'),
                    'profile-information-updated' => fn() => $request->session()->get('profile-information-updated'),
                ],

                'personalisation' => [
                    'app_name' => $personalisation->app_name,
                    'app_logo' => $personalisation->app_logo ? Storage::url($personalisation->app_logo) : null,
                    'app_logo_dark' => $personalisation->app_logo_dark ? Storage::url($personalisation->app_logo_dark) : null,
                    'favicon' => $personalisation->favicon ? Storage::url($personalisation->favicon) : null,
                    'footer_text' => $personalisation->footer_text,
                    'copyright_text' => $personalisation->copyright_text,
                ],

                'settings' => [
                    'passwordlessLogin' => DB::table('settings')->value('passwordless_login') ?? true,
                    'branding' => Settings::getBrandingColors(),
                    'app_name' => Settings::get('app.name', 'GuacPanel'),
                    'notifications' => [
                        'email_enabled' => Settings::emailNotificationsEnabled(),
                        'sms_enabled' => Settings::smsNotificationsEnabled(),
                        'push_enabled' => Settings::pushNotificationsEnabled(),
                        'in_app_enabled' => Settings::inAppNotificationsEnabled(),
                    ],
                ],

                'systemNotices' => SystemNotice::query()
                    ->where('is_active', true)
                    ->where(function ($query) {
                        $query->whereNull('visible_from')
                            ->orWhere('visible_from', '<=', now());
                    })
                    ->where(function ($query) {
                        $query->whereNull('expires_at')
                            ->orWhere('expires_at', '>', now());
                    })
                    ->orderBy('created_at', 'desc')
                    ->get(),

                // Localization data
                'locale' => App::getLocale(),
                'direction' => Session::get('direction', 'ltr'),
                'available_locales' => ['en', 'ar'],
                'translations' => $this->loadTranslations(),
            ],
        );
    }

    /**
     * Load translations for current locale
     */
    protected function loadTranslations()
    {
        $locale = App::getLocale();
        $translations = [];
        
        // Load dashboard translations
        if (file_exists(resource_path("lang/{$locale}/dashboard.php"))) {
            $translations = array_merge($translations, include resource_path("lang/{$locale}/dashboard.php"));
        }
        
        // You can add more translation files here as needed
        // if (file_exists(resource_path("lang/{$locale}/validation.php"))) {
        //     $translations['validation'] = include resource_path("lang/{$locale}/validation.php");
        // }
        
        return $translations;
    }
}
