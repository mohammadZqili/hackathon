<?php

namespace App\Services;

use App\Models\User;
use App\Repositories\ReportRepository;
use App\Repositories\HackathonEditionRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Collection;

class ReportService extends BaseService
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
     * Get paginated report_PLURAL based on user role and filters
     */
    public function getPaginatedReports(User $user, array $filters = [], int $perPage = 15): array
    {
        // Build filters based on user role
        $roleFilters = $this->buildRoleFilters($user, $filters);

        // Get paginated report_PLURAL
        $report_PLURAL = $this->reportRepository->getPaginatedWithFilters($roleFilters, $perPage);

        // Get statistics
        $statistics = $this->reportRepository->getStatistics($roleFilters);

        // Get editions for filter dropdown
        $editions = $this->getEditionsForUser($user);

        return [
            'report_PLURAL' => $report_PLURAL,
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
     * Create a new report
     */
    public function createReport(array $data, User $user): array
    {
        // Check permissions
        if (!$this->userCanCreateReport($user)) {
            throw new \Exception('You do not have permission to create report_PLURAL.');
        }

        // Validate edition access for non-system admin
        if (!$user->hasRole('system_admin') && !empty($data['edition_id'])) {
            if (!$this->userCanAccessEdition($user, $data['edition_id'])) {
                throw new \Exception('You do not have access to this edition.');
            }
        }

        DB::beginTransaction();
        try {
            // Create report
            $report = $this->reportRepository->create($data);

            // Log activity
            Log::info('Report created', [
                'report_id' => $report->id,
                'user_id' => $user->id,
                'data' => $data
            ]);

            DB::commit();

            return [
                'success' => true,
                'report' => $report,
                'message' => 'Report created successfully.'
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to create report', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
                'data' => $data
            ]);
            throw $e;
        }
    }

    /**
     * Update a report
     */
    public function updateReport(int $reportId, array $data, User $user): array
    {
        $report = $this->reportRepository->find($reportId);

        if (!$report) {
            throw new \Exception('Report not found.');
        }

        // Check permissions
        if (!$this->userCanEditReport($user, $report)) {
            throw new \Exception('You do not have permission to edit this report.');
        }

        DB::beginTransaction();
        try {
            // Update report
            $this->reportRepository->update($reportId, $data);

            // Refresh report data
            $report = $this->reportRepository->findWithFullDetails($reportId);

            // Log activity
            Log::info('Report updated', [
                'report_id' => $reportId,
                'user_id' => $user->id,
                'data' => $data
            ]);

            DB::commit();

            return [
                'success' => true,
                'report' => $report,
                'message' => 'Report updated successfully.'
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to update report', [
                'report_id' => $reportId,
                'user_id' => $user->id,
                'error' => $e->getMessage(),
                'data' => $data
            ]);
            throw $e;
        }
    }

    /**
     * Delete a report
     */
    public function deleteReport(int $reportId, User $user): array
    {
        $report = $this->reportRepository->find($reportId);

        if (!$report) {
            throw new \Exception('Report not found.');
        }

        // Check permissions
        if (!$this->userCanDeleteReport($user, $report)) {
            throw new \Exception('You do not have permission to delete this report.');
        }

        // Check dependencies
        if ($this->reportRepository->hasDependencies($reportId)) {
            throw new \Exception('Cannot delete report with dependencies.');
        }

        DB::beginTransaction();
        try {
            // Delete report
            $this->reportRepository->delete($reportId);

            // Log activity
            Log::info('Report deleted', [
                'report_id' => $reportId,
                'user_id' => $user->id
            ]);

            DB::commit();

            return [
                'success' => true,
                'message' => 'Report deleted successfully.'
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to delete report', [
                'report_id' => $reportId,
                'user_id' => $user->id,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Build filters based on user role
     */
    protected function buildRoleFilters(User $user, array $filters): array
    {
        $roleFilters = $filters;

        switch ($user->user_type) {
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
        switch ($user->user_type) {
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
        switch ($user->user_type) {
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
        switch ($user->user_type) {
            case 'system_admin':
                return true;

            case 'hackathon_admin':
                return $user->edition_id == $editionId;

            default:
                return false;
        }
    }

    /**
     * Check if user can create report_PLURAL
     */
    protected function userCanCreateReport(User $user): bool
    {
        return in_array($user->user_type, ['system_admin', 'hackathon_admin']);
    }

    /**
     * Check if user can edit a report
     */
    protected function userCanEditReport(User $user, $report): bool
    {
        if (!$this->userCanAccessReport($user, $report)) {
            return false;
        }

        return in_array($user->user_type, ['system_admin', 'hackathon_admin']);
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
