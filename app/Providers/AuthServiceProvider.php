<?php

namespace App\Providers;

use App\Models\Team;
use App\Models\Idea;
use App\Models\Track;
use App\Policies\TeamPolicy;
use App\Policies\IdeaPolicy;
use App\Policies\TrackPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        Team::class => TeamPolicy::class,
        Idea::class => IdeaPolicy::class,
        Track::class => TrackPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();

        Gate::before(function ($user, $ability) {
            return $user->hasRole('superuser') ? true : null;
        });
    }
}
