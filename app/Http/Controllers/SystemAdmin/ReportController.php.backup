<?php

namespace App\Http\Controllers\SystemAdmin;

use App\Http\Controllers\Controller;
use App\Models\Team;
use App\Models\User;
use App\Models\Idea;
use App\Models\Workshop;
use App\Models\HackathonEdition;
use App\Models\WorkshopRegistration;
use App\Models\WorkshopAttendance;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    /**
     * Display the reports dashboard
     */
    public function index(Request $request)
    {
        $selectedEditionId = $request->get('edition_id');
        
        // Get overall statistics
        $overallStats = $this->getOverallStatistics($selectedEditionId);
        
        // Get editions for filtering
        $editions = HackathonEdition::orderBy('event_start_date', 'desc')->get();
        
        // Get edition-specific statistics
        $editionStats = $this->getEditionStatistics();
        
        // Get workshop performance metrics
        $workshopMetrics = $this->getWorkshopMetrics($selectedEditionId);
        
        // Get recent activity data
        $recentActivity = $this->getRecentActivity();
        
        return Inertia::render('SystemAdmin/Reports/Index', [
            'overallStats' => $overallStats,
            'editions' => $editions,
            'editionStats' => $editionStats,
            'workshopMetrics' => $workshopMetrics,
            'recentActivity' => $recentActivity,
            'selectedEditionId' => $selectedEditionId,
        ]);
    }
    
    /**
     * Get overall statistics
     */
    private function getOverallStatistics($editionId = null)
    {
        $query = [];
        
        if ($editionId) {
            $teamsCount = Team::where('edition_id', $editionId)->count();
            $membersCount = User::whereHas('teams', function($q) use ($editionId) {
                $q->where('edition_id', $editionId);
            })->count();
            $ideasCount = Idea::where('edition_id', $editionId)->count();
            $workshopsCount = Workshop::where('edition_id', $editionId)->count();
            
            // Get submitted vs draft ideas
            $submittedIdeas = Idea::where('edition_id', $editionId)
                ->whereNotIn('status', ['draft'])
                ->count();
            $draftIdeas = Idea::where('edition_id', $editionId)
                ->where('status', 'draft')
                ->count();
        } else {
            $teamsCount = Team::count();
            $membersCount = User::count();
            $ideasCount = Idea::count();
            $workshopsCount = Workshop::count();
            
            // Get submitted vs draft ideas
            $submittedIdeas = Idea::whereNotIn('status', ['draft'])->count();
            $draftIdeas = Idea::where('status', 'draft')->count();
        }
        
        // Calculate growth percentages (compared to last month)
        $lastMonth = Carbon::now()->subMonth();
        
        $teamsLastMonth = Team::where('created_at', '<', $lastMonth)->count();
        $teamsGrowth = $teamsLastMonth > 0 
            ? round((($teamsCount - $teamsLastMonth) / $teamsLastMonth) * 100, 1)
            : 0;
            
        $membersLastMonth = User::where('created_at', '<', $lastMonth)->count();
        $membersGrowth = $membersLastMonth > 0
            ? round((($membersCount - $membersLastMonth) / $membersLastMonth) * 100, 1)
            : 0;
            
        $ideasLastMonth = Idea::where('created_at', '<', $lastMonth)->count();
        $ideasGrowth = $ideasLastMonth > 0
            ? round((($ideasCount - $ideasLastMonth) / $ideasLastMonth) * 100, 1)
            : 0;
            
        $workshopsLastMonth = Workshop::where('created_at', '<', $lastMonth)->count();
        $workshopsGrowth = $workshopsLastMonth > 0
            ? round((($workshopsCount - $workshopsLastMonth) / $workshopsLastMonth) * 100, 1)
            : 0;
        
        return [
            'teams' => [
                'count' => $teamsCount,
                'growth' => $teamsGrowth,
                'trend' => $teamsGrowth >= 0 ? 'up' : 'down'
            ],
            'members' => [
                'count' => $membersCount,
                'growth' => $membersGrowth,
                'trend' => $membersGrowth >= 0 ? 'up' : 'down'
            ],
            'ideas' => [
                'count' => $ideasCount,
                'submitted' => $submittedIdeas,
                'draft' => $draftIdeas,
                'growth' => $ideasGrowth,
                'trend' => $ideasGrowth >= 0 ? 'up' : 'down'
            ],
            'workshops' => [
                'count' => $workshopsCount,
                'growth' => $workshopsGrowth,
                'trend' => $workshopsGrowth >= 0 ? 'up' : 'down'
            ]
        ];
    }
    
    /**
     * Get edition-specific statistics
     */
    private function getEditionStatistics()
    {
        $editions = HackathonEdition::with(['teams', 'ideas', 'workshops'])
            ->orderBy('event_start_date', 'desc')
            ->take(10)
            ->get();
        
        $stats = [];
        
        foreach ($editions as $edition) {
            $teamsCount = $edition->teams->count();
            $ideasCount = $edition->ideas->count();
            $workshopsCount = $edition->workshops->count();
            
            // Calculate members count
            $membersCount = DB::table('team_members')
                ->join('teams', 'team_members.team_id', '=', 'teams.id')
                ->where('teams.edition_id', $edition->id)
                ->distinct('team_members.user_id')
                ->count('team_members.user_id');
            
            // Calculate participation rate (if max_participants is set)
            $participationRate = $edition->max_participants > 0
                ? round(($membersCount / $edition->max_participants) * 100, 1)
                : 0;
            
            // Determine status based on dates
            $now = Carbon::now();
            $startDate = Carbon::parse($edition->event_start_date);
            $endDate = Carbon::parse($edition->event_end_date);
            
            if ($now->lt($startDate)) {
                $status = 'upcoming';
                $statusColor = 'blue';
            } elseif ($now->between($startDate, $endDate)) {
                $status = 'active';
                $statusColor = 'green';
            } else {
                $status = 'completed';
                $statusColor = 'gray';
            }
            
            $stats[] = [
                'id' => $edition->id,
                'name' => $edition->name,
                'theme' => $edition->theme,
                'start_date' => $startDate->format('M d, Y'),
                'end_date' => $endDate->format('M d, Y'),
                'teams' => $teamsCount,
                'members' => $membersCount,
                'ideas' => $ideasCount,
                'workshops' => $workshopsCount,
                'participation_rate' => $participationRate,
                'status' => $status,
                'status_color' => $statusColor
            ];
        }
        
        return $stats;
    }
    
    /**
     * Get workshop performance metrics
     */
    private function getWorkshopMetrics($editionId = null)
    {
        $query = Workshop::query();
        
        if ($editionId) {
            $query->where('edition_id', $editionId);
        }
        
        $workshops = $query->with(['registrations', 'attendances'])->get();
        
        $totalWorkshops = $workshops->count();
        $totalRegistrations = 0;
        $totalAttendances = 0;
        $totalCapacity = 0;
        $totalDuration = 0;
        $totalSatisfactionScore = 0;
        $satisfactionCount = 0;
        
        foreach ($workshops as $workshop) {
            $registrations = $workshop->registrations->count();
            $attendances = $workshop->attendances->count();
            $capacity = $workshop->capacity ?? 0;
            
            $totalRegistrations += $registrations;
            $totalAttendances += $attendances;
            $totalCapacity += $capacity;
            
            // Calculate duration in hours
            if ($workshop->start_time && $workshop->end_time) {
                $start = Carbon::parse($workshop->start_time);
                $end = Carbon::parse($workshop->end_time);
                $totalDuration += $end->diffInHours($start);
            }
            
            // Calculate average satisfaction from attendances
            foreach ($workshop->attendances as $attendance) {
                if ($attendance->satisfaction_rating) {
                    $totalSatisfactionScore += $attendance->satisfaction_rating;
                    $satisfactionCount++;
                }
            }
        }
        
        // Calculate metrics
        $attendanceRate = $totalRegistrations > 0
            ? round(($totalAttendances / $totalRegistrations) * 100, 1)
            : 0;
            
        $capacityUtilization = $totalCapacity > 0
            ? round(($totalRegistrations / $totalCapacity) * 100, 1)
            : 0;
            
        $avgSatisfaction = $satisfactionCount > 0
            ? round($totalSatisfactionScore / $satisfactionCount, 1)
            : 0;
        
        // Get top workshops by attendance
        $topWorkshops = Workshop::query()
            ->when($editionId, function($q) use ($editionId) {
                $q->where('edition_id', $editionId);
            })
            ->withCount('attendances')
            ->orderBy('attendances_count', 'desc')
            ->take(5)
            ->get()
            ->map(function($workshop) {
                return [
                    'id' => $workshop->id,
                    'title' => $workshop->title,
                    'attendances' => $workshop->attendances_count,
                    'capacity' => $workshop->capacity,
                    'utilization' => $workshop->capacity > 0 
                        ? round(($workshop->attendances_count / $workshop->capacity) * 100, 1)
                        : 0
                ];
            });
        
        return [
            'total_workshops' => $totalWorkshops,
            'total_registrations' => $totalRegistrations,
            'total_attendances' => $totalAttendances,
            'attendance_rate' => $attendanceRate,
            'capacity_utilization' => $capacityUtilization,
            'total_hours' => $totalDuration,
            'avg_satisfaction' => $avgSatisfaction,
            'top_workshops' => $topWorkshops
        ];
    }
    
    /**
     * Get recent activity for the activity feed
     */
    private function getRecentActivity()
    {
        $activities = [];
        
        // Get recent teams
        $recentTeams = Team::with('leader')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();
            
        foreach ($recentTeams as $team) {
            $activities[] = [
                'type' => 'team',
                'message' => "New team '" . $team->name . "' created",
                'user' => $team->leader->name ?? 'Unknown',
                'time' => Carbon::parse($team->created_at)->diffForHumans(),
                'timestamp' => $team->created_at
            ];
        }
        
        // Get recent ideas
        $recentIdeas = Idea::with('team')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();
            
        foreach ($recentIdeas as $idea) {
            $activities[] = [
                'type' => 'idea',
                'message' => "New idea '" . $idea->title . "' submitted",
                'user' => $idea->team->name ?? 'Unknown Team',
                'time' => Carbon::parse($idea->created_at)->diffForHumans(),
                'timestamp' => $idea->created_at
            ];
        }
        
        // Get recent workshop registrations
        $recentRegistrations = WorkshopRegistration::with(['user', 'workshop'])
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();
            
        foreach ($recentRegistrations as $registration) {
            $activities[] = [
                'type' => 'registration',
                'message' => "Registered for workshop '" . ($registration->workshop->title ?? 'Unknown') . "'",
                'user' => $registration->user->name ?? 'Unknown',
                'time' => Carbon::parse($registration->created_at)->diffForHumans(),
                'timestamp' => $registration->created_at
            ];
        }
        
        // Sort activities by timestamp and take the most recent 10
        usort($activities, function($a, $b) {
            return $b['timestamp'] <=> $a['timestamp'];
        });
        
        return array_slice($activities, 0, 10);
    }
    
    /**
     * Generate a detailed report for an edition
     */
    public function generateReport(Request $request)
    {
        $editionId = $request->get('edition_id');
        
        // This would generate a detailed report
        // For now, we'll return a success message
        
        return response()->json([
            'success' => true,
            'message' => 'Report generation initiated. You will receive an email when ready.'
        ]);
    }
    
    /**
     * Export report to PDF
     */
    public function exportPdf(Request $request)
    {
        $editionId = $request->get('edition_id');
        
        // This would export to PDF
        // For now, we'll return a success message
        
        return response()->json([
            'success' => true,
            'message' => 'PDF export initiated. Download will start shortly.'
        ]);
    }
    
    /**
     * Schedule automated reports
     */
    public function scheduleReports(Request $request)
    {
        $frequency = $request->get('frequency', 'weekly');
        $recipients = $request->get('recipients', []);
        
        // This would schedule automated reports
        // For now, we'll return a success message
        
        return response()->json([
            'success' => true,
            'message' => "Reports scheduled {$frequency}. Recipients will receive reports automatically."
        ]);
    }
    
    /**
     * Users report page
     */
    public function users(Request $request)
    {
        return Inertia::render('SystemAdmin/Reports/Users');
    }
    
    /**
     * Teams report page
     */
    public function teams(Request $request)
    {
        return Inertia::render('SystemAdmin/Reports/Teams');
    }
    
    /**
     * Ideas report page
     */
    public function ideas(Request $request)
    {
        return Inertia::render('SystemAdmin/Reports/Ideas');
    }
    
    /**
     * Workshops report page
     */
    public function workshops(Request $request)
    {
        return Inertia::render('SystemAdmin/Reports/Workshops');
    }
    
    /**
     * System health report page
     */
    public function systemHealth(Request $request)
    {
        return Inertia::render('SystemAdmin/Reports/SystemHealth');
    }
}