<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\Log;

class SocialAuthController extends Controller
{
    /**
     * Redirect to OAuth provider
     *
     * @param string $provider
     * @return \Illuminate\Http\RedirectResponse
     */
    public function redirect($provider)
    {
        // Validate provider
        if (!in_array($provider, ['github', 'google'])) {
            return redirect()->route('login')->with('error', 'Invalid provider');
        }

        try {
            return Socialite::driver($provider)->redirect();
        } catch (\Exception $e) {
            Log::error('OAuth redirect failed: ' . $e->getMessage());
            return redirect()->route('login')->with('error', 'Unable to redirect to ' . ucfirst($provider));
        }
    }

    /**
     * Handle OAuth provider callback
     *
     * @param string $provider
     * @return \Illuminate\Http\RedirectResponse
     */
    public function callback($provider)
    {
        // Validate provider
        if (!in_array($provider, ['github', 'google'])) {
            return redirect()->route('login')->with('error', 'Invalid provider');
        }

        try {
            // Get user data from OAuth provider
            $socialUser = Socialite::driver($provider)->user();
            
            // Check if user exists by email
            $user = User::where('email', $socialUser->getEmail())->first();
            
            if ($user) {
                // Update social provider information
                $this->updateSocialProviderData($user, $provider, $socialUser);
                
                // Log the user in
                Auth::login($user, true);
                
                // Log the login
                $this->logLogin($user, $provider);
                
                // Redirect to dashboard
                return redirect()->intended(route('dashboard'))->with('success', 'Successfully logged in with ' . ucfirst($provider));
            } else {
                // User not registered - show message
                return redirect()->route('login')->with('error', 'No account found with this email. Please contact your administrator to register.');
            }
            
        } catch (\Laravel\Socialite\Two\InvalidStateException $e) {
            Log::error('OAuth invalid state: ' . $e->getMessage());
            return redirect()->route('login')->with('error', 'Authentication failed. Please try again.');
        } catch (\Exception $e) {
            Log::error('OAuth callback failed: ' . $e->getMessage());
            return redirect()->route('login')->with('error', 'Authentication failed: ' . $e->getMessage());
        }
    }

    /**
     * Update user's social provider data
     *
     * @param User $user
     * @param string $provider
     * @param \Laravel\Socialite\Contracts\User $socialUser
     * @return void
     */
    protected function updateSocialProviderData($user, $provider, $socialUser)
    {
        $socialData = [
            'provider_id' => $socialUser->getId(),
            'provider_name' => $provider,
            'provider_avatar' => $socialUser->getAvatar(),
            'provider_email' => $socialUser->getEmail(),
            'provider_nickname' => $socialUser->getNickname(),
        ];

        // Store in JSON column or separate table based on your preference
        $existingSocialLogins = $user->social_logins ?? [];
        $existingSocialLogins[$provider] = $socialData;
        
        $user->update([
            'social_logins' => $existingSocialLogins
        ]);

        // Update avatar if not set
        if (!$user->avatar && $socialUser->getAvatar()) {
            $user->update(['avatar' => $socialUser->getAvatar()]);
        }
    }

    /**
     * Log the social login
     *
     * @param User $user
     * @param string $provider
     * @return void
     */
    protected function logLogin($user, $provider)
    {
        activity()
            ->performedOn($user)
            ->causedBy($user)
            ->log('User logged in via ' . ucfirst($provider));

        // You can also create a login history entry if you have that table
        if (class_exists('App\Models\LoginHistory')) {
            \App\Models\LoginHistory::create([
                'user_id' => $user->id,
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'login_type' => 'social',
                'provider' => $provider,
                'logged_in_at' => now(),
            ]);
        }
    }
}
