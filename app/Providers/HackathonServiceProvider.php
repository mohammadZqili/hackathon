<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

// Service Interfaces
use App\Services\Contracts\HackathonServiceInterface;
use App\Services\Contracts\HackathonEditionServiceInterface;
use App\Services\Contracts\TeamServiceInterface;
use App\Services\Contracts\IdeaServiceInterface;
use App\Services\Contracts\WorkshopServiceInterface;
use App\Services\Contracts\TrackServiceInterface;
use App\Services\Contracts\NewsServiceInterface;

// Service Implementations
use App\Services\HackathonService;
use App\Services\HackathonEditionService;
use App\Services\TeamService;
use App\Services\IdeaService;
use App\Services\WorkshopService;
use App\Services\TrackService;
use App\Services\NewsService;

// Repository Interfaces
use App\Repositories\Contracts\HackathonRepositoryInterface;
use App\Repositories\Contracts\HackathonEditionRepositoryInterface;
use App\Repositories\Contracts\TeamRepositoryInterface;
use App\Repositories\Contracts\IdeaRepositoryInterface;
use App\Repositories\Contracts\WorkshopRepositoryInterface;
use App\Repositories\Contracts\TrackRepositoryInterface;
use App\Repositories\Contracts\NewsRepositoryInterface;
use App\Repositories\Contracts\UserRepositoryInterface;

// Repository Implementations
use App\Repositories\HackathonRepository;
use App\Repositories\HackathonEditionRepository;
use App\Repositories\TeamRepository;
use App\Repositories\IdeaRepository;
use App\Repositories\WorkshopRepository;
use App\Repositories\TrackRepository;
use App\Repositories\NewsRepository;
use App\Repositories\UserRepository;

// Models
use App\Models\Hackathon;
use App\Models\HackathonEdition;
use App\Models\Team;
use App\Models\Idea;
use App\Models\Workshop;
use App\Models\Track;
use App\Models\News;
use App\Models\User;

class HackathonServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // =====================================================
        // Repository Bindings
        // =====================================================
        
        // Bind repository interfaces to implementations
        $this->app->bind(HackathonRepositoryInterface::class, function ($app) {
            return new HackathonRepository(new Hackathon());
        });

        $this->app->bind(HackathonEditionRepositoryInterface::class, function ($app) {
            return new HackathonEditionRepository(new HackathonEdition());
        });

        $this->app->bind(TeamRepositoryInterface::class, function ($app) {
            return new TeamRepository(new Team());
        });

        $this->app->bind(IdeaRepositoryInterface::class, function ($app) {
            return new IdeaRepository(new Idea());
        });

        $this->app->bind(WorkshopRepositoryInterface::class, function ($app) {
            return new WorkshopRepository(new Workshop());
        });

        $this->app->bind(TrackRepositoryInterface::class, function ($app) {
            return new TrackRepository(new Track());
        });

        $this->app->bind(NewsRepositoryInterface::class, function ($app) {
            return new NewsRepository(new News());
        });

        $this->app->bind(UserRepositoryInterface::class, function ($app) {
            return new UserRepository(new User());
        });

        // =====================================================
        // Service Bindings
        // =====================================================
        
        // Bind HackathonService
        $this->app->bind(HackathonServiceInterface::class, HackathonService::class);

        // Bind HackathonEditionService
        $this->app->bind(HackathonEditionServiceInterface::class, HackathonEditionService::class);

        // Bind TeamService
        $this->app->bind(TeamServiceInterface::class, TeamService::class);

        // Bind IdeaService
        $this->app->bind(IdeaServiceInterface::class, IdeaService::class);

        // Bind WorkshopService
        $this->app->bind(WorkshopServiceInterface::class, WorkshopService::class);

        // Bind TrackService
        $this->app->bind(TrackServiceInterface::class, TrackService::class);

        // Bind NewsService
        $this->app->bind(NewsServiceInterface::class, NewsService::class);

        // =====================================================
        // Singleton Services (if needed for caching/state)
        // =====================================================
        
        // Example: If you want HackathonService to be a singleton
        // $this->app->singleton(HackathonServiceInterface::class, HackathonService::class);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // You can add event listeners, view composers, etc. here if needed
        
        // Example: Clear hackathon cache when hackathon is updated
        Hackathon::updated(function ($hackathon) {
            \Cache::forget('current_hackathon');
            \Cache::forget("hackathon_stats_{$hackathon->id}");
        });

        // Example: Log team creation
        Team::created(function ($team) {
            \Log::info('New team created', [
                'team_id' => $team->id,
                'name' => $team->name,
                'leader_id' => $team->leader_id,
                'hackathon_id' => $team->hackathon_id,
            ]);
        });
    }
}
