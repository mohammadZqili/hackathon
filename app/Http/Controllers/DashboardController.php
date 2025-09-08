<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Cache;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        
        // Redirect to role-specific dashboard
        if ($user->hasRole('system_admin')) {
            return redirect()->route('system-admin.dashboard');
        }
        
        if ($user->hasRole('hackathon_admin')) {
            return redirect()->route('hackathon-admin.dashboard');
        }
        
        if ($user->hasRole('track_supervisor')) {
            return redirect()->route('track-supervisor.dashboard');
        }
        
        if ($user->hasRole('team_leader')) {
            return redirect()->route('team-leader.dashboard');
        }
        
        if ($user->hasRole('team_member')) {
            return redirect()->route('team-member.dashboard');
        }
        
        // Fallback to generic dashboard
        return Inertia::render('Dashboard', [
            'stats' => $this->getStats(),
        ]);
    }


    public function getStats()
    {
        return Cache::remember('dashboard_stats', 60, function () {
            $totalMembers = User::count();
            $newMembersToday = User::whereDate('created_at', today())->count();
            $memberGrowth = $this->calculateGrowthPercentage(
                User::whereDate('created_at', '>=', now()->subDays(7))->count(),
                User::whereDate('created_at', '>=', now()->subDays(14))->whereDate('created_at', '<', now()->subDays(7))->count()
            );

            return [
                [
                    'title' => 'Total Members',
                    'value' => number_format($totalMembers),
                    'growth' => sprintf('%+.1f%%', $memberGrowth),
                ],
                [
                    'title' => 'New Members Today',
                    'value' => number_format($newMembersToday),
                    'growth' => sprintf('%+.1f%%', $newMembersToday > 0 ? 100 : 0),
                ],
                [
                    'title' => 'Weekly Growth',
                    'value' => sprintf('%+.1f%%', $memberGrowth),
                    'growth' => sprintf('%+.1f%%', $memberGrowth),
                ],
                [
                    'title' => 'Total Sessions',
                    'value' => number_format(rand(5000, 15000)),
                    'growth' => sprintf('%+.1f%%', rand(5, 15)),
                ]
            ];
        });
    }


    private function calculateGrowthPercentage($current, $previous)
    {
        if ($previous == 0) {
            return $current > 0 ? 100 : 0;
        }
        return (($current - $previous) / $previous) * 100;
    }
}
