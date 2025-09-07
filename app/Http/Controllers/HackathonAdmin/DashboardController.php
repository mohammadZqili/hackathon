<?php

namespace App\Http\Controllers\HackathonAdmin;

use App\Http\Controllers\Controller;
use App\Models\HackathonEdition;
use App\Models\Team;
use App\Models\Idea;
use App\Models\Workshop;
use App\Models\Track;
use App\Models\News;
use Inertia\Inertia;
use Inertia\Response;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index(): Response
    {
        $currentEdition = HackathonEdition::where('is_current', true)->first();
        
        if (!$currentEdition) {
            return Inertia::render('HackathonAdmin/NoEdition');
        }

        // Get statistics for current edition
        $statistics = [
            'teams' => [
                'total' => Team::where('hackathon_id', $currentEdition->id)->count(),
                'pending' => Team::where('hackathon_id', $currentEdition->id)->where('status', 'pending')->count(),
                'approved' => Team::where('hackathon_id', $currentEdition->id)->where('status', 'approved')->count(),
                'rejected' => Team::where('hackathon_id', $currentEdition->id)->where('status', 'rejected')->count(),
            ],
            'ideas' => [
                'total' => Idea::whereHas('team', function($q) use ($currentEdition) {
                    $q->where('hackathon_id', $currentEdition->id);
                })->count(),
                'pending' => Idea::whereHas('team', function($q) use ($currentEdition) {
                    $q->where('hackathon_id', $currentEdition->id);
                })->where('status', 'pending')->count(),
                'approved' => Idea::whereHas('team', function($q) use ($currentEdition) {
                    $q->where('hackathon_id', $currentEdition->id);
                })->where('status', 'approved')->count(),
            ],
            'workshops' => [
                'total' => Workshop::where('hackathon_edition_id', $currentEdition->id)->count(),
                'upcoming' => Workshop::where('hackathon_edition_id', $currentEdition->id)
                    ->where('start_time', '>', Carbon::now())
                    ->count(),
                'completed' => Workshop::where('hackathon_edition_id', $currentEdition->id)
                    ->where('end_time', '<', Carbon::now())
                    ->count(),
            ],
            'tracks' => Track::where('hackathon_edition_id', $currentEdition->id)->count(),
        ];

        // Get recent activities
        $recentTeams = Team::where('hackathon_id', $currentEdition->id)
            ->with(['leader', 'track'])
            ->latest()
            ->take(5)
            ->get();

        $recentIdeas = Idea::whereHas('team', function($q) use ($currentEdition) {
                $q->where('hackathon_id', $currentEdition->id);
            })
            ->with(['team', 'track'])
            ->latest()
            ->take(5)
            ->get();

        // Get upcoming workshops
        $upcomingWorkshops = Workshop::where('hackathon_edition_id', $currentEdition->id)
            ->where('start_time', '>', Carbon::now())
            ->orderBy('start_time')
            ->take(5)
            ->get();

        // Get teams by track
        $teamsByTrack = Track::where('hackathon_edition_id', $currentEdition->id)
            ->withCount(['teams' => function($q) use ($currentEdition) {
                $q->where('hackathon_id', $currentEdition->id);
            }])
            ->get();

        return Inertia::render('HackathonAdmin/Dashboard/Index', [
            'currentEdition' => $currentEdition,
            'statistics' => $statistics,
            'recentTeams' => $recentTeams,
            'recentIdeas' => $recentIdeas,
            'upcomingWorkshops' => $upcomingWorkshops,
            'teamsByTrack' => $teamsByTrack,
        ]);
    }
}
