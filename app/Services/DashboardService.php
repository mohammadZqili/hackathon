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

        // Get tracks for current edition only
        $currentEdition = $this->editionRepo ? $this->editionRepo->getCurrent() : null;
        $tracks = $currentEdition
            ? $this->trackRepo->getActive(['edition_id' => $currentEdition->id])
            : collect();

        return [
            'team' => $team,
            'idea' => $idea,
            'workshops' => $workshops,
            'tracks' => $tracks,
            'currentEdition' => $currentEdition,
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

        // Get tracks for current edition only
        $currentEdition = $this->editionRepo ? $this->editionRepo->getCurrent() : null;
        $tracks = $currentEdition
            ? $this->trackRepo->getActive(['edition_id' => $currentEdition->id])
            : collect();

        return [
            'team' => $team,
            'idea' => $idea,
            'workshops' => $workshops,
            'tracks' => $tracks,
            'currentEdition' => $currentEdition,
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
            ],
            'tracks' => [
                'total' => 0,
                'supervised' => 0,
                'teams_in_tracks' => 0
            ]
        ];

        // Get team statistics
        $teamQuery = DB::table('teams');
        if (!empty($filters['edition_id'])) {
            $teamQuery->where('edition_id', $filters['edition_id']);
        }
        if (!empty($filters['track_ids'])) {
            $teamQuery->whereIn('track_id', $filters['track_ids']);
        }
        $stats['teams']['total'] = $teamQuery->count();
        $stats['teams']['active'] = (clone $teamQuery)->where('status', 'active')->count();

        // Get idea statistics
        $ideaQuery = DB::table('ideas');
        if (!empty($filters['edition_id']) || !empty($filters['track_ids'])) {
            $ideaQuery->whereExists(function ($query) use ($filters) {
                $query->select(DB::raw(1))
                    ->from('teams')
                    ->whereColumn('teams.id', 'ideas.team_id');

                if (!empty($filters['edition_id'])) {
                    $query->where('teams.edition_id', $filters['edition_id']);
                }
                if (!empty($filters['track_ids'])) {
                    $query->whereIn('teams.track_id', $filters['track_ids']);
                }
            });
        }
        $stats['ideas']['total'] = $ideaQuery->count();
        $stats['ideas']['submitted'] = (clone $ideaQuery)->where('status', 'submitted')->count();
        $stats['ideas']['approved'] = (clone $ideaQuery)->where('status', 'approved')->count();

        // Get user statistics (count team members in supervised tracks)
        if (!empty($filters['track_ids'])) {
            // For track supervisors, count users in teams within their tracks
            $userQuery = DB::table('users')
                ->whereExists(function ($query) use ($filters) {
                    $query->select(DB::raw(1))
                        ->from('team_members')
                        ->join('teams', 'teams.id', '=', 'team_members.team_id')
                        ->whereColumn('team_members.user_id', 'users.id')
                        ->whereIn('teams.track_id', $filters['track_ids']);
                });
            $stats['users']['total'] = $userQuery->count();
            $stats['users']['active'] = (clone $userQuery)->where('last_login_at', '>', Carbon::now()->subDays(30))->count();
            $stats['users']['new_today'] = (clone $userQuery)->whereDate('users.created_at', Carbon::today())->count();
        } else {
            $userQuery = DB::table('users');
            if (!empty($filters['edition_id'])) {
                $userQuery->where('edition_id', $filters['edition_id']);
            }
            $stats['users']['total'] = $userQuery->count();
            $stats['users']['active'] = (clone $userQuery)->where('last_login_at', '>', Carbon::now()->subDays(30))->count();
            $stats['users']['new_today'] = (clone $userQuery)->whereDate('created_at', Carbon::today())->count();
        }

        // Get workshop statistics
        $workshopQuery = DB::table('workshops');
        if (!empty($filters['edition_id'])) {
            $workshopQuery->where('hackathon_edition_id', $filters['edition_id']);
        }
        $stats['workshops']['total'] = $workshopQuery->count();
        $stats['workshops']['upcoming'] = (clone $workshopQuery)->where('is_active', true)->where('start_time', '>', now())->count();
        $stats['workshops']['completed'] = (clone $workshopQuery)->where('end_time', '<', now())->count();

        // Get track statistics for track supervisors
        if (!empty($filters['track_ids'])) {
            $stats['tracks']['supervised'] = count($filters['track_ids']);
            $stats['tracks']['teams_in_tracks'] = DB::table('teams')
                ->whereIn('track_id', $filters['track_ids'])
                ->count();

            // Get track names for display
            $trackNames = DB::table('tracks')
                ->whereIn('id', $filters['track_ids'])
                ->pluck('name')
                ->implode(', ');
            $stats['tracks']['names'] = $trackNames;
        }

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
            ->select('teams.*', 'users.name as leader_name', 'tracks.name as track_name')
            ->leftJoin('users', 'teams.leader_id', '=', 'users.id')
            ->leftJoin('tracks', 'teams.track_id', '=', 'tracks.id')
            ->orderBy('teams.created_at', 'desc')
            ->limit(5);

        if (!empty($filters['edition_id'])) {
            $teamsQuery->where('teams.edition_id', $filters['edition_id']);
        }

        if (!empty($filters['track_ids'])) {
            $teamsQuery->whereIn('teams.track_id', $filters['track_ids']);
        }

        $recentTeams = $teamsQuery->get();
        
        foreach ($recentTeams as $team) {
            $activities[] = [
                'id' => 'team_' . $team->id,
                'type' => 'team_created',
                'title' => 'New team registered',
                'description' => $team->name . ' has registered' . ($team->track_name ? ' in ' . $team->track_name : ''),
                'timestamp' => Carbon::parse($team->created_at),
                'icon' => 'users',
                'color' => 'green'
            ];
        }

        // Get recent ideas
        $ideasQuery = DB::table('ideas')
            ->select('ideas.*', 'teams.name as team_name', 'tracks.name as track_name')
            ->leftJoin('teams', 'ideas.team_id', '=', 'teams.id')
            ->leftJoin('tracks', 'teams.track_id', '=', 'tracks.id')
            ->orderBy('ideas.created_at', 'desc')
            ->limit(5);

        if (!empty($filters['edition_id'])) {
            $ideasQuery->where('teams.edition_id', $filters['edition_id']);
        }

        if (!empty($filters['track_ids'])) {
            $ideasQuery->whereIn('teams.track_id', $filters['track_ids']);
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
