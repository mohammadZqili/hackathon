<?php

namespace App\Http\Controllers\SystemAdmin;

use App\Http\Controllers\Controller;
use App\Services\DashboardService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DashboardController extends Controller
{
    protected DashboardService $dashboardService;
    
    public function __construct(DashboardService $dashboardService)
    {
        $this->dashboardService = $dashboardService;
    }

    /**
     * Display dashboard
     */
    public function index(Request $request)
    {
        $data = $this->dashboardService->getDashboardData(
            auth()->user(),
            $request->only(['edition_id', 'date_range'])
        );
        
        return Inertia::render('SystemAdmin/Dashboard/Index', $data);
    }

    /**
     * Get chart data
     */
    public function chartData(Request $request)
    {
        $data = $this->dashboardService->getChartData(
            auth()->user(),
            $request->get('type', 'registrations'),
            $request->only(['edition_id', 'date_range'])
        );
        
        return response()->json($data);
    }

    /**
     * Get activity feed
     */
    public function activity(Request $request)
    {
        $data = $this->dashboardService->getActivityFeed(
            auth()->user(),
            $request->get('limit', 20)
        );
        
        return response()->json($data);
    }
}
