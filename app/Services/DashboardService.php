<?php

namespace App\Services;

use App\Models\User;
use App\Repositories\TeamRepository;
use App\Repositories\IdeaRepository;
use App\Repositories\WorkshopRepository;
use App\Repositories\TrackRepository;
use App\Repositories\UserRepository;
use App\Repositories\HackathonEditionRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;

class DashboardService extends BaseService
{
    protected $teamRepo;
    protected $ideaRepo;
    protected $workshopRepo;
    protected $trackRepo;
    protected $userRepo;
    protected $editionRepo;

    public function __construct(
        TeamRepository $teamRepo,
        IdeaRepository $ideaRepo,
        WorkshopRepository $workshopRepo,
        TrackRepository $trackRepo,
        UserRepository $userRepo = null,
        HackathonEditionRepository $editionRepo = null
    ) {
        $this->teamRepo = $teamRepo;
        $this->ideaRepo = $ideaRepo;
        $this->workshopRepo = $workshopRepo;
        $this->trackRepo = $trackRepo;
        $this->userRepo = $userRepo;
        $this->editionRepo = $editionRepo;
    }

    public function getTeamLeadDashboard($userId)
    {
        $team = $this->teamRepo->findByLeaderId($userId);
        $idea = $team ? $this->ideaRepo->findByTeamId($team->id) : null;
        $workshops = $this->workshopRepo->getUpcoming();
        $tracks = $this->trackRepo->getActive();

        return [
            'team' => $team,
            'idea' => $idea,
            'workshops' => $workshops,
            'tracks' => $tracks,
            'stats' => [
                'team_members' => $team ? $team->members()->count() : 0,
                'idea_status' => $idea ? $idea->status : 'pending',
                'workshops_registered' => $this->workshopRepo->countUserWorkshops($userId),
                'track' => $team && $team->track ? $team->track->name : null
            ]
        ];
    }

    public function getTeamMemberDashboard($userId)
    {
        $member = $this->teamRepo->findMemberTeam($userId);
        $team = $member ? $member->team : null;
        $idea = $team ? $this->ideaRepo->findByTeamId($team->id) : null;
        $workshops = $this->workshopRepo->getUpcoming();

        return [
            'team' => $team,
            'idea' => $idea,
            'workshops' => $workshops,
            'stats' => [
                'team_name' => $team ? $team->name : null,
                'idea_status' => $idea ? $idea->status : 'pending',
                'workshops_registered' => $this->workshopRepo->countUserWorkshops($userId),
                'role' => $member ? $member->role : null
            ]
        ];
    }

    public function getVisitorDashboard($userId)
    {
        $workshops = $this->workshopRepo->getUpcoming();
        $myWorkshops = $this->workshopRepo->getUserWorkshops($userId);

        return [
            'workshops' => $workshops,
            'myWorkshops' => $myWorkshops,
            'stats' => [
                'total_workshops' => $workshops->count(),
                'registered_workshops' => $myWorkshops->count()
            ]
        ];
    }

    /**
     * Get dashboard data for SystemAdmin and HackathonAdmin
     */
    public function getDashboardData(User $user, array $filters = []): array
    {
        // Build filters based on user role
        $roleFilters = $this->buildRoleFilters($user, $filters);
        
        // Get statistics
        $statistics = $this->getStatistics($user, $roleFilters);
        
        // Get recent activities
        $recentActivities = $this->getRecentActivities($user, $roleFilters);
        
        // Get chart data
        $chartData = $this->getDefaultChartData($user, $roleFilters);
        
        // Get editions for filter dropdown
        $editions = $this->editionRepo ? $this->getEditionsForUser($user) : [];
        
        return [
            'statistics' => $statistics,
            'recentActivities' => $recentActivities,
            'chartData' => $chartData,
            'editions' => $editions,
            'filters' => $filters
        ];
    }

    /**
     * Get statistics for dashboard
     */
    protected function getStatistics(User $user, array $filters): array
    {
        $stats = [
            'teams' => [
                'total' => 0,
                'active' => 0,
                'growth' => 0
            ],
            'ideas' => [
                'total' => 0,
                'submitted' => 0,
                'approved' => 0,
                'growth' => 0
            ],
            'users' => [
                'total' => 0,
                'active' => 0,
                'new_today' => 0,
                'growth' => 0
            ],
            'workshops' => [
                'total' => 0,
                'upcoming' => 0,
                'completed' => 0
            ]
        ];

        // Get team statistics
        $teamQuery = DB::table('teams');
        if (!empty($filters['edition_id'])) {
            $teamQuery->where('edition_id', $filters['edition_id']);
        }
        $stats['teams']['total'] = $teamQuery->count();
        $stats['teams']['active'] = $teamQuery->where('status', 'active')->count();

        // Get idea statistics
        $ideaQuery = DB::table('ideas');
        if (!empty($filters['edition_id'])) {
            $ideaQuery->whereExists(function ($query) use ($filters) {
                $query->select(DB::raw(1))
                    ->from('teams')
                    ->whereColumn('teams.id', 'ideas.team_id')
                    ->where('teams.edition_id', $filters['edition_id']);
            });
        }
        $stats['ideas']['total'] = $ideaQuery->count();
        $stats['ideas']['submitted'] = (clone $ideaQuery)->where('status', 'submitted')->count();
        $stats['ideas']['approved'] = (clone $ideaQuery)->where('status', 'approved')->count();

        // Get user statistics
        $userQuery = DB::table('users');
        if (!empty($filters['edition_id'])) {
            $userQuery->where('edition_id', $filters['edition_id']);
        }
        $stats['users']['total'] = $userQuery->count();
        // Count users who logged in recently as active (within last 30 days)
        $stats['users']['active'] = $userQuery->where('last_login_at', '>', Carbon::now()->subDays(30))->count();
        $stats['users']['new_today'] = $userQuery->whereDate('created_at', Carbon::today())->count();

        // Get workshop statistics
        $workshopQuery = DB::table('workshops');
        if (!empty($filters['edition_id'])) {
            $workshopQuery->where('hackathon_edition_id', $filters['edition_id']);
        }
        $stats['workshops']['total'] = $workshopQuery->count();
        $stats['workshops']['upcoming'] = $workshopQuery->where('is_active', true)->where('start_time', '>', now())->count();
        $stats['workshops']['completed'] = $workshopQuery->where('end_time', '<', now())->count();

        return $stats;
    }

    /**
     * Get recent activities
     */
    protected function getRecentActivities(User $user, array $filters): array
    {
        $activities = [];
        
        // Get recent teams
        $teamsQuery = DB::table('teams')
            ->select('teams.*', 'users.name as leader_name')
            ->leftJoin('users', 'teams.leader_id', '=', 'users.id')
            ->orderBy('teams.created_at', 'desc')
            ->limit(5);
            
        if (!empty($filters['edition_id'])) {
            $teamsQuery->where('teams.edition_id', $filters['edition_id']);
        }
        
        $recentTeams = $teamsQuery->get();
        
        foreach ($recentTeams as $team) {
            $activities[] = [
                'id' => 'team_' . $team->id,
                'type' => 'team_created',
                'title' => 'New team registered',
                'description' => $team->name . ' has registered',
                'timestamp' => Carbon::parse($team->created_at),
                'icon' => 'users',
                'color' => 'green'
            ];
        }
        
        // Get recent ideas
        $ideasQuery = DB::table('ideas')
            ->select('ideas.*', 'teams.name as team_name')
            ->leftJoin('teams', 'ideas.team_id', '=', 'teams.id')
            ->orderBy('ideas.created_at', 'desc')
            ->limit(5);
            
        if (!empty($filters['edition_id'])) {
            $ideasQuery->where('teams.edition_id', $filters['edition_id']);
        }
        
        $recentIdeas = $ideasQuery->get();
        
        foreach ($recentIdeas as $idea) {
            $activities[] = [
                'id' => 'idea_' . $idea->id,
                'type' => 'idea_submitted',
                'title' => 'New idea submitted',
                'description' => $idea->title . ' by ' . ($idea->team_name ?? 'Unknown Team'),
                'timestamp' => Carbon::parse($idea->created_at),
                'icon' => 'lightbulb',
                'color' => 'blue'
            ];
        }
        
        // Sort by timestamp
        usort($activities, function ($a, $b) {
            return $b['timestamp']->timestamp - $a['timestamp']->timestamp;
        });
        
        return array_slice($activities, 0, 10);
    }

    /**
     * Get default chart data
     */
    protected function getDefaultChartData(User $user, array $filters): array
    {
        return $this->getRegistrationChartData($filters);
    }

    /**
     * Get registration chart data
     */
    protected function getRegistrationChartData(array $filters): array
    {
        $days = 7;
        $data = [
            'labels' => [],
            'values' => []
        ];
        
        for ($i = $days - 1; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            
            $query = DB::table('users')
                ->whereDate('created_at', $date->format('Y-m-d'));
                
            if (!empty($filters['edition_id'])) {
                $query->where('edition_id', $filters['edition_id']);
            }
            
            $count = $query->count();
            
            $data['labels'][] = $date->format('M d');
            $data['values'][] = $count;
        }
        
        return [
            'type' => 'line',
            'title' => 'User Registrations (Last 7 Days)',
            'data' => $data
        ];
    }

    /**
     * Get chart data
     */
    public function getChartData(User $user, string $type, array $filters = []): array
    {
        $roleFilters = $this->buildRoleFilters($user, $filters);
        
        switch ($type) {
            case 'registrations':
                return $this->getRegistrationChartData($roleFilters);
            case 'ideas':
                return $this->getIdeasChartData($roleFilters);
            case 'workshops':
                return $this->getWorkshopsChartData($roleFilters);
            default:
                return $this->getRegistrationChartData($roleFilters);
        }
    }

    /**
     * Get ideas chart data
     */
    protected function getIdeasChartData(array $filters): array
    {
        $statuses = ['draft', 'submitted', 'approved', 'rejected'];
        $data = [
            'labels' => [],
            'values' => []
        ];
        
        foreach ($statuses as $status) {
            $query = DB::table('ideas')->where('status', $status);
            
            if (!empty($filters['edition_id'])) {
                $query->whereExists(function ($q) use ($filters) {
                    $q->select(DB::raw(1))
                        ->from('teams')
                        ->whereColumn('teams.id', 'ideas.team_id')
                        ->where('teams.edition_id', $filters['edition_id']);
                });
            }
            
            $count = $query->count();
            $data['labels'][] = ucfirst($status);
            $data['values'][] = $count;
        }
        
        return [
            'type' => 'pie',
            'title' => 'Ideas by Status',
            'data' => $data
        ];
    }

    /**
     * Get workshops chart data
     */
    protected function getWorkshopsChartData(array $filters): array
    {
        $query = DB::table('tracks')
            ->select('tracks.name', DB::raw('COUNT(workshops.id) as count'))
            ->leftJoin('workshops', 'tracks.id', '=', 'workshops.track_id')
            ->groupBy('tracks.id', 'tracks.name');
            
        if (!empty($filters['edition_id'])) {
            $query->where('workshops.hackathon_edition_id', $filters['edition_id']);
        }
        
        $tracks = $query->get();
        
        $data = [
            'labels' => $tracks->pluck('name')->toArray(),
            'values' => $tracks->pluck('count')->toArray()
        ];
        
        return [
            'type' => 'bar',
            'title' => 'Workshops by Track',
            'data' => $data
        ];
    }

    /**
     * Get activity feed
     */
    public function getActivityFeed(User $user, int $limit = 20): array
    {
        $roleFilters = $this->buildRoleFilters($user, []);
        return $this->getRecentActivities($user, $roleFilters);
    }

    /**
     * Build filters based on user role
     */
    protected function buildRoleFilters(User $user, array $filters): array
    {
        $roleFilters = $filters;
        
        switch ($user->user_type) {
            case 'hackathon_admin':
                if ($user->edition_id) {
                    $roleFilters['edition_id'] = $user->edition_id;
                }
                break;
            case 'track_supervisor':
                $trackIds = DB::table('track_supervisors')
                    ->where('user_id', $user->id)
                    ->pluck('track_id')
                    ->toArray();
                if (!empty($trackIds)) {
                    $roleFilters['track_ids'] = $trackIds;
                }
                break;
            case 'system_admin':
                // No additional filters
                break;
            default:
                $roleFilters['force_empty'] = true;
                break;
        }
        
        return $roleFilters;
    }

    /**
     * Get editions available to user
     */
    protected function getEditionsForUser(User $user): \Illuminate\Support\Collection
    {
        if (!$this->editionRepo) {
            return collect();
        }
        
        switch ($user->user_type) {
            case 'system_admin':
                return $this->editionRepo->all();
            case 'hackathon_admin':
                if ($user->edition_id) {
                    return collect([$this->editionRepo->find($user->edition_id)]);
                }
                return collect();
            default:
                return collect();
        }
    }
}
