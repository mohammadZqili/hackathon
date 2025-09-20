<?php

namespace App\Http\Controllers\HackathonAdmin;

use App\Http\Controllers\Controller;
use App\Services\ReportService;
use App\Models\HackathonEdition;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ReportController extends Controller
{
    protected ReportService $reportService;

    public function __construct(ReportService $reportService)
    {
        $this->reportService = $reportService;
    }

    /**
     * Display the reports dashboard
     */
    public function index(Request $request)
    {
        $selectedEditionId = $request->get('edition_id');

        // Get overall statistics
        $overallStats = $this->reportService->getOverallStatistics();

        // Get editions for filtering
        $editions = HackathonEdition::orderBy('event_start_date', 'desc')->get();

        // Get edition-specific statistics table data
        $editionStats = $this->reportService->getEditionStatistics($request->get('edition_filter'));

        // Get detailed edition report if edition is selected
        $editionReport = null;
        $selectedEdition = null;
        if ($selectedEditionId) {
            $editionReport = $this->reportService->getEditionReport($selectedEditionId);
            $selectedEdition = HackathonEdition::find($selectedEditionId);
        }

        // Get recent activity data
        $recentActivity = $this->reportService->getRecentActivity();

        return Inertia::render('HackathonAdmin/Reports/Index', [
            'overallStats' => $overallStats,
            'editions' => $editions,
            'editionStats' => $editionStats,
            'editionReport' => $editionReport,
            'selectedEdition' => $selectedEdition,
            'recentActivity' => $recentActivity,
            'selectedEditionId' => $selectedEditionId,
        ]);
    }

    /**
     * Export reports to Excel/CSV
     */
    public function export(Request $request)
    {
        $editionId = $request->get('edition_id');
        $data = $this->reportService->generateExportData($editionId);

        $fileName = 'hackathon_report_' . date('Y-m-d_H-i-s') . '.csv';

        // Create CSV content
        $csvContent = $this->generateCSVContent($data, $editionId);

        // Return CSV download response
        return response($csvContent)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', 'attachment; filename="' . $fileName . '"')
            ->header('Cache-Control', 'no-cache, no-store, must-revalidate')
            ->header('Pragma', 'no-cache')
            ->header('Expires', '0');
    }

    /**
     * Generate PDF report
     */
    public function exportPdf(Request $request)
    {
        $editionId = $request->get('edition_id');
        $data = $this->reportService->generateExportData($editionId);

        // For now, we'll create a simple HTML report that can be printed as PDF
        $html = $this->generateHTMLReport($data, $editionId);

        return response($html)
            ->header('Content-Type', 'text/html')
            ->header('Content-Disposition', 'inline; filename="hackathon_report_' . date('Y-m-d_H-i-s') . '.html"');
    }

    /**
     * Generate HTML report for PDF printing
     */
    private function generateHTMLReport(array $data, ?int $editionId = null): string
    {
        $html = '<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Hackathon Report</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        h1 { color: #333; border-bottom: 2px solid #0d9488; padding-bottom: 10px; }
        h2 { color: #0d9488; margin-top: 30px; }
        table { width: 100%; border-collapse: collapse; margin: 20px 0; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; font-weight: bold; }
        .metric { display: inline-block; margin: 10px 20px 10px 0; }
        .metric-label { font-weight: bold; color: #666; }
        .metric-value { font-size: 24px; color: #0d9488; }
        @media print { body { margin: 0; } }
    </style>
</head>
<body>
    <h1>Hackathon Report</h1>
    <p>Generated on: ' . date('Y-m-d H:i:s') . '</p>';

        if ($editionId) {
            // Edition-specific report
            if (!empty($data['edition'])) {
                $html .= '<h2>' . htmlspecialchars($data['edition']['name']) . ' - Edition Report</h2>';
            }

            // Overview metrics
            $html .= '<h2>Overview</h2><div>';
            $html .= '<span class="metric"><span class="metric-label">Teams: </span><span class="metric-value">' . ($data['overview']['teams'] ?? 0) . '</span></span>';
            $html .= '<span class="metric"><span class="metric-label">Members: </span><span class="metric-value">' . ($data['overview']['members'] ?? 0) . '</span></span>';
            $html .= '<span class="metric"><span class="metric-label">Ideas: </span><span class="metric-value">' . ($data['overview']['ideas'] ?? 0) . '</span></span>';
            $html .= '<span class="metric"><span class="metric-label">Workshops: </span><span class="metric-value">' . ($data['overview']['workshops'] ?? 0) . '</span></span>';
            $html .= '</div>';

            // Idea Status
            if (!empty($data['idea_status'])) {
                $html .= '<h2>Idea Status Distribution</h2>';
                $html .= '<table><tr><th>Status</th><th>Count</th></tr>';
                foreach ($data['idea_status'] as $status) {
                    $html .= '<tr><td>' . htmlspecialchars($status['status']) . '</td><td>' . $status['count'] . '</td></tr>';
                }
                $html .= '</table>';
            }

            // Team Performance
            if (!empty($data['team_performance'])) {
                $html .= '<h2>Team Performance</h2>';
                $html .= '<table><tr><th>Team</th><th>Members</th><th>Idea</th><th>Status</th><th>Score</th></tr>';
                foreach ($data['team_performance'] as $team) {
                    $html .= '<tr>';
                    $html .= '<td>' . htmlspecialchars($team['team_name']) . '</td>';
                    $html .= '<td>' . $team['members'] . '</td>';
                    $html .= '<td>' . htmlspecialchars($team['idea_title']) . '</td>';
                    $html .= '<td>' . htmlspecialchars($team['status']) . '</td>';
                    $html .= '<td>' . $team['score'] . '</td>';
                    $html .= '</tr>';
                }
                $html .= '</table>';
            }
        } else {
            // Overall report
            $html .= '<h2>Overall Statistics</h2><div>';
            $html .= '<span class="metric"><span class="metric-label">Total Teams: </span><span class="metric-value">' . ($data['overall']['teams'] ?? 0) . '</span></span>';
            $html .= '<span class="metric"><span class="metric-label">Total Members: </span><span class="metric-value">' . ($data['overall']['members'] ?? 0) . '</span></span>';
            $html .= '<span class="metric"><span class="metric-label">Total Ideas: </span><span class="metric-value">' . ($data['overall']['ideas'] ?? 0) . '</span></span>';
            $html .= '<span class="metric"><span class="metric-label">Total Workshops: </span><span class="metric-value">' . ($data['overall']['workshops'] ?? 0) . '</span></span>';
            $html .= '</div>';

            // Edition Statistics Table
            if (!empty($data['editions'])) {
                $html .= '<h2>Edition Statistics</h2>';
                $html .= '<table><tr><th>Edition</th><th>Teams</th><th>Members</th><th>Ideas</th><th>Status</th></tr>';
                foreach ($data['editions'] as $edition) {
                    $html .= '<tr>';
                    $html .= '<td>' . htmlspecialchars($edition['name'] ?? '') . '</td>';
                    $html .= '<td>' . ($edition['teams'] ?? 0) . '</td>';
                    $html .= '<td>' . ($edition['members'] ?? 0) . '</td>';
                    $html .= '<td>' . ($edition['ideas'] ?? 0) . '</td>';
                    $html .= '<td>' . htmlspecialchars($edition['status'] ?? '') . '</td>';
                    $html .= '</tr>';
                }
                $html .= '</table>';
            }
        }

        $html .= '</body></html>';
        return $html;
    }

    /**
     * Get user statistics report
     */
    public function users(Request $request)
    {
        // This would show user-specific statistics
        return Inertia::render('HackathonAdmin/Reports/Users');
    }

    /**
     * Get team statistics report
     */
    public function teams(Request $request)
    {
        // This would show team-specific statistics
        return Inertia::render('HackathonAdmin/Reports/Teams');
    }

    /**
     * Get idea statistics report
     */
    public function ideas(Request $request)
    {
        // This would show idea-specific statistics
        return Inertia::render('HackathonAdmin/Reports/Ideas');
    }

    /**
     * Get workshop statistics report
     */
    public function workshops(Request $request)
    {
        // This would show workshop-specific statistics
        return Inertia::render('HackathonAdmin/Reports/Workshops');
    }

    /**
     * Get system health report
     */
    public function systemHealth(Request $request)
    {
        // This would show system health metrics
        return Inertia::render('HackathonAdmin/Reports/SystemHealth');
    }

    /**
     * Generate a report
     */
    public function generateReport(Request $request)
    {
        // Placeholder for report generation
        return response()->json([
            'success' => true,
            'message' => 'Report generation initiated.'
        ]);
    }

    /**
     * Schedule reports
     */
    public function scheduleReports(Request $request)
    {
        // Placeholder for scheduling reports
        return response()->json([
            'success' => true,
            'message' => 'Reports scheduled successfully.'
        ]);
    }

    /**
     * Generate CSV content for export
     */
    private function generateCSVContent(array $data, ?int $editionId = null): string
    {
        $output = fopen('php://temp', 'r+');

        if ($editionId) {
            // Edition-specific report
            fputcsv($output, ['Hackathon Edition Report']);
            fputcsv($output, ['Generated on: ' . date('Y-m-d H:i:s')]);
            fputcsv($output, []);

            // Overview section
            fputcsv($output, ['Overview']);
            fputcsv($output, ['Metric', 'Value']);
            fputcsv($output, ['Teams', $data['overview']['teams'] ?? 0]);
            fputcsv($output, ['Members', $data['overview']['members'] ?? 0]);
            fputcsv($output, ['Ideas', $data['overview']['ideas'] ?? 0]);
            fputcsv($output, ['Workshops', $data['overview']['workshops'] ?? 0]);
            fputcsv($output, []);

            // Idea Status Distribution
            if (!empty($data['idea_status'])) {
                fputcsv($output, ['Idea Status Distribution']);
                fputcsv($output, ['Status', 'Count']);
                foreach ($data['idea_status'] as $status) {
                    fputcsv($output, [$status['status'], $status['count']]);
                }
                fputcsv($output, []);
            }

            // Workshop Statistics
            if (!empty($data['workshop_stats'])) {
                fputcsv($output, ['Workshop Statistics']);
                fputcsv($output, ['Metric', 'Value']);
                fputcsv($output, ['Total Workshops', $data['workshop_stats']['total_workshops'] ?? 0]);
                fputcsv($output, ['Total Speakers', $data['workshop_stats']['total_speakers'] ?? 0]);
                fputcsv($output, ['Average Attendance', $data['workshop_stats']['avg_attendance'] ?? 0]);
                fputcsv($output, ['Total Organizations', $data['workshop_stats']['total_organizations'] ?? 0]);
                fputcsv($output, []);
            }

            // Team Performance
            if (!empty($data['team_performance'])) {
                fputcsv($output, ['Team Performance']);
                fputcsv($output, ['Team Name', 'Members', 'Idea Title', 'Status', 'Score']);
                foreach ($data['team_performance'] as $team) {
                    fputcsv($output, [
                        $team['team_name'],
                        $team['members'],
                        $team['idea_title'],
                        $team['status'],
                        $team['score']
                    ]);
                }
                fputcsv($output, []);
            }

            // Registrations Trend
            if (!empty($data['registrations_trend'])) {
                fputcsv($output, ['Workshop Registrations Trend']);
                fputcsv($output, ['Workshop', 'Date', 'Registrations']);
                foreach ($data['registrations_trend'] as $trend) {
                    fputcsv($output, [
                        $trend['workshop'],
                        $trend['date'],
                        $trend['registrations']
                    ]);
                }
            }
        } else {
            // Overall statistics report
            fputcsv($output, ['Hackathon Overall Statistics Report']);
            fputcsv($output, ['Generated on: ' . date('Y-m-d H:i:s')]);
            fputcsv($output, []);

            // Overall stats
            if (!empty($data['overall'])) {
                fputcsv($output, ['Overall Statistics']);
                fputcsv($output, ['Metric', 'Value']);
                fputcsv($output, ['Total Teams', $data['overall']['teams'] ?? 0]);
                fputcsv($output, ['Total Members', $data['overall']['members'] ?? 0]);
                fputcsv($output, ['Total Ideas', $data['overall']['ideas'] ?? 0]);
                fputcsv($output, ['Total Workshops', $data['overall']['workshops'] ?? 0]);
                fputcsv($output, []);
            }

            // Edition-specific statistics
            if (!empty($data['editions'])) {
                fputcsv($output, ['Edition-Specific Statistics']);
                fputcsv($output, ['Edition', 'Teams', 'Members', 'Ideas', 'Status', 'Workshop Attendance', 'Registrations', 'Website Visitors']);

                foreach ($data['editions'] as $edition) {
                    fputcsv($output, [
                        $edition['name'] ?? '',
                        $edition['teams'] ?? 0,
                        $edition['members'] ?? 0,
                        $edition['ideas'] ?? 0,
                        $edition['status'] ?? '',
                        $edition['workshop_attendance'] ?? '',
                        $edition['registrations'] ?? 0,
                        $edition['website_visitors'] ?? 0
                    ]);
                }
            }
        }

        rewind($output);
        $csvContent = stream_get_contents($output);
        fclose($output);

        return $csvContent;
    }
}