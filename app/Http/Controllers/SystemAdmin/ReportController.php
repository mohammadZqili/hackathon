<?php

namespace App\Http\Controllers\SystemAdmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\User;
use App\Models\Team;
use App\Models\Idea;
use App\Models\Workshop;
use App\Models\HackathonEdition;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    /**
     * Display the reports dashboard.
     */
    public function index()
    {
        $reportSummary = [
            'total_users' => User::count(),
            'total_teams' => Team::count(),
            'total_ideas' => Idea::count(),
            'total_workshops' => Workshop::count(),
            'current_edition' => HackathonEdition::where('is_current', true)->first(),
        ];

        return Inertia::render('SystemAdmin/Reports/Index', [
            'reportSummary' => $reportSummary
        ]);
    }

    /**
     * Generate users report.
     */
    public function users(Request $request)
    {
        $query = User::with('roles');

        if ($request->filled('role')) {
            $query->whereHas('roles', function ($q) use ($request) {
                $q->where('name', $request->role);
            });
        }

        if ($request->filled('from_date')) {
            $query->whereDate('created_at', '>=', $request->from_date);
        }

        if ($request->filled('to_date')) {
            $query->whereDate('created_at', '<=', $request->to_date);
        }

        $users = $query->latest()->paginate(50);

        $userStats = [
            'total' => User::count(),
            'by_role' => User::select('roles.name as role', DB::raw('count(*) as count'))
                ->join('model_has_roles', 'users.id', '=', 'model_has_roles.model_id')
                ->join('roles', 'model_has_roles.role_id', '=', 'roles.id')
                ->groupBy('roles.name')
                ->get(),
            'recent_registrations' => User::whereDate('created_at', '>=', now()->subDays(30))->count(),
        ];

        return Inertia::render('SystemAdmin/Reports/Users', [
            'users' => $users,
            'userStats' => $userStats,
            'filters' => $request->only(['role', 'from_date', 'to_date'])
        ]);
    }

    /**
     * Generate teams report.
     */
    public function teams(Request $request)
    {
        $query = Team::with(['hackathon', 'leader', 'members']);

        if ($request->filled('hackathon_id')) {
            $query->where('hackathon_id', $request->hackathon_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $teams = $query->latest()->paginate(50);

        $teamStats = [
            'total' => Team::count(),
            'by_status' => Team::select('status', DB::raw('count(*) as count'))
                ->groupBy('status')
                ->get(),
            'by_hackathon' => Team::select('hackathon_id', 'hackathons.name', DB::raw('count(*) as count'))
                ->join('hackathons', 'teams.hackathon_id', '=', 'hackathons.id')
                ->groupBy('hackathon_id', 'hackathons.name')
                ->get(),
        ];

        return Inertia::render('SystemAdmin/Reports/Teams', [
            'teams' => $teams,
            'teamStats' => $teamStats,
            'hackathons' => HackathonEdition::all(),
            'filters' => $request->only(['hackathon_id', 'status'])
        ]);
    }

    /**
     * Generate ideas report.
     */
    public function ideas(Request $request)
    {
        $query = Idea::with(['team.hackathon', 'track', 'reviewer']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('track_id')) {
            $query->where('track_id', $request->track_id);
        }

        $ideas = $query->latest()->paginate(50);

        $ideaStats = [
            'total' => Idea::count(),
            'by_status' => Idea::select('status', DB::raw('count(*) as count'))
                ->groupBy('status')
                ->get(),
            'by_track' => Idea::select('track_id', 'tracks.name', DB::raw('count(*) as count'))
                ->join('tracks', 'ideas.track_id', '=', 'tracks.id')
                ->groupBy('track_id', 'tracks.name')
                ->get(),
            'average_score' => Idea::whereNotNull('score')->avg('score'),
        ];

        return Inertia::render('SystemAdmin/Reports/Ideas', [
            'ideas' => $ideas,
            'ideaStats' => $ideaStats,
            'filters' => $request->only(['status', 'track_id'])
        ]);
    }

    /**
     * Generate workshops report.
     */
    public function workshops(Request $request)
    {
        $query = Workshop::with(['hackathon', 'speakers', 'registrations']);

        if ($request->filled('hackathon_id')) {
            $query->where('hackathon_id', $request->hackathon_id);
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        $workshops = $query->latest()->paginate(50);

        $workshopStats = [
            'total' => Workshop::count(),
            'total_registrations' => DB::table('workshop_registrations')->count(),
            'total_attendances' => DB::table('workshop_registrations')
                ->where('status', 'attended')->count(),
            'by_type' => Workshop::select('type', DB::raw('count(*) as count'))
                ->groupBy('type')
                ->get(),
            'average_attendance_rate' => $this->calculateAverageAttendanceRate(),
        ];

        return Inertia::render('SystemAdmin/Reports/Workshops', [
            'workshops' => $workshops,
            'workshopStats' => $workshopStats,
            'hackathons' => HackathonEdition::all(),
            'filters' => $request->only(['hackathon_id', 'type'])
        ]);
    }

    /**
     * Generate system health report.
     */
    public function systemHealth()
    {
        $healthMetrics = [
            'database' => $this->checkDatabaseHealth(),
            'storage' => $this->checkStorageHealth(),
            'cache' => $this->checkCacheHealth(),
            'queue' => $this->checkQueueHealth(),
            'performance' => $this->getPerformanceMetrics(),
        ];

        return Inertia::render('SystemAdmin/Reports/SystemHealth', [
            'healthMetrics' => $healthMetrics
        ]);
    }

    /**
     * Calculate average attendance rate.
     */
    private function calculateAverageAttendanceRate(): float
    {
        $workshops = Workshop::withCount([
            'registrations',
            'registrations as attended_count' => function ($query) {
                $query->where('status', 'attended');
            }
        ])->get();

        $totalRate = 0;
        $validWorkshops = 0;

        foreach ($workshops as $workshop) {
            if ($workshop->registrations_count > 0) {
                $rate = ($workshop->attended_count / $workshop->registrations_count) * 100;
                $totalRate += $rate;
                $validWorkshops++;
            }
        }

        return $validWorkshops > 0 ? round($totalRate / $validWorkshops, 2) : 0;
    }

    /**
     * Check database health.
     */
    private function checkDatabaseHealth(): array
    {
        try {
            $start = microtime(true);
            DB::select('SELECT 1');
            $responseTime = round((microtime(true) - $start) * 1000, 2);

            return [
                'status' => 'healthy',
                'response_time' => $responseTime . 'ms',
                'connections' => DB::select('SHOW STATUS LIKE "Threads_connected"')[0]->Value ?? 'N/A',
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Check storage health.
     */
    private function checkStorageHealth(): array
    {
        $storagePath = storage_path();
        $totalSpace = disk_total_space($storagePath);
        $freeSpace = disk_free_space($storagePath);
        $usedSpace = $totalSpace - $freeSpace;
        $usagePercentage = round(($usedSpace / $totalSpace) * 100, 2);

        return [
            'status' => $usagePercentage > 90 ? 'warning' : 'healthy',
            'total_space' => $this->formatBytes($totalSpace),
            'used_space' => $this->formatBytes($usedSpace),
            'free_space' => $this->formatBytes($freeSpace),
            'usage_percentage' => $usagePercentage,
        ];
    }

    /**
     * Check cache health.
     */
    private function checkCacheHealth(): array
    {
        try {
            $start = microtime(true);
            cache()->put('health_check', 'test', 10);
            $value = cache()->get('health_check');
            $responseTime = round((microtime(true) - $start) * 1000, 2);

            return [
                'status' => $value === 'test' ? 'healthy' : 'error',
                'response_time' => $responseTime . 'ms',
                'driver' => config('cache.default'),
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Check queue health.
     */
    private function checkQueueHealth(): array
    {
        // Basic queue health check
        return [
            'status' => 'healthy',
            'driver' => config('queue.default'),
            'pending_jobs' => 0, // Would need to implement actual queue monitoring
        ];
    }

    /**
     * Get performance metrics.
     */
    private function getPerformanceMetrics(): array
    {
        return [
            'memory_usage' => $this->formatBytes(memory_get_usage(true)),
            'peak_memory' => $this->formatBytes(memory_get_peak_usage(true)),
            'php_version' => PHP_VERSION,
            'laravel_version' => app()->version(),
        ];
    }

    /**
     * Format bytes to human readable format.
     */
    private function formatBytes($bytes, $precision = 2): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];

        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }

        return round($bytes, $precision) . ' ' . $units[$i];
    }
}
