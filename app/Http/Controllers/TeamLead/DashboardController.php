<?php

namespace App\Http\Controllers\TeamLead;

use App\Http\Controllers\Controller;
use App\Services\DashboardService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DashboardController extends Controller
{
    protected $dashboardService;

    public function __construct(DashboardService $dashboardService)
    {
        $this->dashboardService = $dashboardService;
    }

    public function index()
    {
        $user = auth()->user();
        $data = $this->dashboardService->getTeamLeadDashboard($user->id);

        // Check if team leader has an idea
        if (!isset($data['idea']) || !$data['idea']) {
            // Check if team leader has a team
            if (!isset($data['team']) || !$data['team']) {
                // No team and no idea - redirect to idea creation
                return redirect()->route('team-lead.idea.create')
                    ->with('info', 'Welcome! Please create your first idea to get started. A team will be created automatically.');
            } else {
                // Has team but no idea - redirect to idea creation
                return redirect()->route('team-lead.idea.create')
                    ->with('info', 'Please create an idea for your team to proceed.');
            }
        }

        return Inertia::render('TeamLead/Dashboard', $data);
    }
}
