<?php

namespace App\Services;

use App\Models\User;
use App\Repositories\ReportRepository;
use App\Repositories\HackathonEditionRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Collection;
use Carbon\Carbon;

class ReportService
{
    protected ReportRepository $reportRepository;
    protected HackathonEditionRepository $editionRepository;

    public function __construct(
        ReportRepository $reportRepository,
        HackathonEditionRepository $editionRepository
    ) {
        $this->reportRepository = $reportRepository;
        $this->editionRepository = $editionRepository;
    }

    /**
     * Get overall statistics across all editions
     */
    public function getOverallStatistics(): array
    {
        return [
            'teams' => $this->reportRepository->getTotalTeamsCount(),
            'members' => $this->reportRepository->getTotalMembersCount(),
            'ideas' => $this->reportRepository->getTotalIdeasCount(),
            'workshops' => $this->reportRepository->getTotalWorkshopsCount(),
        ];
    }

    /**
     * Get edition-specific statistics for table display
     */
    public function getEditionStatistics(?string $editionFilter = null): Collection
    {
        $editions = $this->reportRepository->getEditionsWithStats($editionFilter);

        return $editions->map(function ($edition) {
            $stats = $this->reportRepository->getEditionStats($edition->id);

            return [
                'id' => $edition->id,
                'name' => $edition->name,
                'teams' => $stats['teams'],
                'members' => $stats['members'],
                'ideas' => $stats['ideas'],
                'status' => $this->determineEditionStatus($edition),
                'workshop_attendance' => $stats['workshop_attendance'] . '%',
                'registrations' => $stats['registrations'],
                'website_visitors' => $stats['website_visitors'] ?? rand(4000, 6000), // Placeholder
            ];
        });
    }

    /**
     * Get detailed report for a specific edition
     */
    public function getEditionReport(int $editionId): array
    {
        $edition = $this->editionRepository->find($editionId);
        $stats = $this->reportRepository->getEditionStats($editionId);

        return [
            'overview' => [
                'teams' => $stats['teams'],
                'members' => $stats['members'],
                'ideas' => $stats['ideas'],
                'workshops' => $stats['workshops'],
            ],
            'idea_status' => $this->reportRepository->getIdeaStatusDistribution($editionId),
            'workshop_stats' => $this->reportRepository->getWorkshopStatistics($editionId),
            'registrations_trend' => $this->reportRepository->getRegistrationsTrend($editionId),
            'team_performance' => $this->reportRepository->getTeamPerformance($editionId),
            'edition' => [
                'id' => $edition->id,
                'name' => $edition->name,
                'start_date' => $edition->event_start_date,
                'end_date' => $edition->event_end_date,
            ],
        ];
    }

    /**
     * Generate Excel export data
     */
    public function generateExportData(?int $editionId = null): array
    {
        if ($editionId) {
            return $this->getEditionReport($editionId);
        }

        return [
            'overall' => $this->getOverallStatistics(),
            'editions' => $this->getEditionStatistics(),
        ];
    }

    /**
     * Get workshop metrics
     */
    public function getWorkshopMetrics(?int $editionId = null): array
    {
        return $this->reportRepository->getWorkshopMetrics($editionId);
    }

    /**
     * Get recent activity data for dashboard
     */
    public function getRecentActivity(int $days = 7): array
    {
        return [
            'new_teams' => $this->reportRepository->getRecentTeams($days),
            'new_ideas' => $this->reportRepository->getRecentIdeas($days),
            'new_registrations' => $this->reportRepository->getRecentRegistrations($days),
            'recent_checkins' => $this->reportRepository->getRecentCheckins($days),
        ];
    }

    /**
     * Determine edition status based on dates
     */
    private function determineEditionStatus($edition): string
    {
        $now = Carbon::now();
        $startDate = Carbon::parse($edition->event_start_date);
        $endDate = Carbon::parse($edition->event_end_date);

        if ($now->isBefore($startDate)) {
            return 'Upcoming';
        } elseif ($now->isAfter($endDate)) {
            return 'Completed';
        } else {
            return 'Ongoing';
        }
    }

    /**
     * Get statistics for specific date range
     */
    public function getStatisticsByDateRange(Carbon $startDate, Carbon $endDate): array
    {
        return $this->reportRepository->getStatsByDateRange($startDate, $endDate);
    }

    /**
     * Get comparison between editions
     */
    public function compareEditions(array $editionIds): array
    {
        $comparison = [];

        foreach ($editionIds as $editionId) {
            $stats = $this->reportRepository->getEditionStats($editionId);
            $edition = $this->editionRepository->find($editionId);

            $comparison[] = [
                'edition' => $edition->name,
                'stats' => $stats,
            ];
        }

        return $comparison;
    }

    /**
     * Get paginated reports based on user role and filters
     */
    public function getPaginatedReports(User $user, array $filters = [], int $perPage = 15): array
    {
        // Build filters based on user role
        $roleFilters = $this->buildRoleFilters($user, $filters);

        // Get paginated reports
        $reports = $this->reportRepository->getPaginatedWithFilters($roleFilters, $perPage);

        // Get statistics
        $statistics = $this->reportRepository->getStatistics($roleFilters);

        // Get editions for filter dropdown
        $editions = $this->getEditionsForUser($user);

        return [
            'reports' => $reports,
            'statistics' => $statistics,
            'editions' => $editions,
            'filters' => $filters
        ];
    }

    /**
     * Get report details
     */
    public function getReportDetails(int $reportId, User $user): ?array
    {
        $report = $this->reportRepository->findWithFullDetails($reportId);

        if (!$report) {
            return null;
        }

        // Check if user has access to this report
        if (!$this->userCanAccessReport($user, $report)) {
            return null;
        }

        return [
            'report' => $report,
            'permissions' => $this->getReportPermissions($user, $report)
        ];
    }

    /**
     * Build filters based on user role
     */
    protected function buildRoleFilters(User $user, array $filters): array
    {
        $roleFilters = $filters;

        switch ($user->primary_role ?? $user->user_type) {
            case 'hackathon_admin':
                // Limit to user's edition
                if ($user->edition_id) {
                    $roleFilters['edition_id'] = $user->edition_id;
                }
                break;

            case 'system_admin':
                // No additional filters - can see everything
                break;

            default:
                // Other roles - force empty result
                $roleFilters['force_empty'] = true;
                break;
        }

        return $roleFilters;
    }

    /**
     * Get editions available to user
     */
    protected function getEditionsForUser(User $user): Collection
    {
        switch ($user->primary_role ?? $user->user_type) {
            case 'system_admin':
                return $this->editionRepository->all();

            case 'hackathon_admin':
                if ($user->edition_id) {
                    return collect([$this->editionRepository->find($user->edition_id)]);
                }
                return collect();

            default:
                return collect();
        }
    }

    /**
     * Check if user can access a specific report
     */
    protected function userCanAccessReport(User $user, $report): bool
    {
        switch ($user->primary_role ?? $user->user_type) {
            case 'system_admin':
                return true;

            case 'hackathon_admin':
                return !isset($report->edition_id) || $user->edition_id == $report->edition_id;

            default:
                return false;
        }
    }

    /**
     * Check if user can access a specific edition
     */
    protected function userCanAccessEdition(User $user, int $editionId): bool
    {
        switch ($user->primary_role ?? $user->user_type) {
            case 'system_admin':
                return true;

            case 'hackathon_admin':
                return $user->edition_id == $editionId;

            default:
                return false;
        }
    }

    /**
     * Check if user can create reports
     */
    protected function userCanCreateReport(User $user): bool
    {
        $role = $user->primary_role ?? $user->user_type;
        return in_array($role, ['system_admin', 'hackathon_admin']);
    }

    /**
     * Check if user can edit a report
     */
    protected function userCanEditReport(User $user, $report): bool
    {
        if (!$this->userCanAccessReport($user, $report)) {
            return false;
        }

        $role = $user->primary_role ?? $user->user_type;
        return in_array($role, ['system_admin', 'hackathon_admin']);
    }

    /**
     * Check if user can delete a report
     */
    protected function userCanDeleteReport(User $user, $report): bool
    {
        if (!$this->userCanAccessReport($user, $report)) {
            return false;
        }

        // Only system admin can delete
        return $user->hasRole('system_admin');
    }

    /**
     * Get permissions for a report
     */
    protected function getReportPermissions(User $user, $report): array
    {
        return [
            'canEdit' => $this->userCanEditReport($user, $report),
            'canDelete' => $this->userCanDeleteReport($user, $report),
            'canExport' => true,
        ];
    }
}