<?php

namespace App\Http\Controllers\TrackSupervisor;

use App\Http\Controllers\Controller;
use App\Models\HackathonEdition;
use App\Models\Team;
use App\Models\Idea;
use App\Models\User;
use App\Models\Workshop;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function index(): Response
    {
        $currentEdition = HackathonEdition::where('is_current', true)->first();

        $statistics = [
            'total_editions' => HackathonEdition::count(),
            'total_users' => User::count(),
            'total_teams' => Team::count(),
            'total_ideas' => Idea::count(),
            'total_workshops' => Workshop::count(),
            'current_edition' => $currentEdition ? [
                'name' => $currentEdition->name,
                'year' => $currentEdition->year,
                'status' => $currentEdition->status,
                'teams_count' => Team::where('hackathon_id', $currentEdition->id)->count(),
                'ideas_count' => Idea::whereHas('team', function($q) use ($currentEdition) {
                    $q->where('hackathon_id', $currentEdition->id);
                })->count(),
            ] : null,
            'recent_activities' => $this->getRecentActivities(),
        ];

        return Inertia::render('TrackSupervisor/Dashboard', [
            'statistics' => $statistics,
        ]);
    }

    private function getRecentActivities(): array
    {
        $activities = [];

        // Recent teams
        $recentTeams = Team::with('leader')
            ->latest()
            ->take(5)
            ->get()
            ->map(function ($team) {
                return [
                    'type' => 'team_created',
                    'message' => "New team \"{$team->name}\" created by {$team->leader->name}",
                    'time' => $team->created_at->diffForHumans(),
                    'icon' => 'users',
                ];
            });

        // Recent ideas
        $recentIdeas = Idea::with('team')
            ->latest()
            ->take(5)
            ->get()
            ->map(function ($idea) {
                return [
                    'type' => 'idea_submitted',
                    'message' => "New idea \"{$idea->title}\" submitted by team {$idea->team->name}",
                    'time' => $idea->created_at->diffForHumans(),
                    'icon' => 'lightbulb',
                ];
            });

        // Merge and sort by time
        $activities = $recentTeams->merge($recentIdeas)
            ->sortByDesc(function ($item) {
                return $item['time'];
            })
            ->take(10)
            ->values()
            ->toArray();

        return $activities;
    }
}
