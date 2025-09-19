<?php

namespace App\Services;

use App\Models\User;
use App\Models\Idea;
use App\Repositories\IdeaRepository;
use App\Repositories\TeamRepository;
use App\Repositories\HackathonEditionRepository;
use App\Notifications\IdeaSubmittedNotification;
use App\Notifications\IdeaReviewedNotification;
use App\Notifications\IdeaStatusChangedNotification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Collection;

class IdeaService extends BaseService
{
    protected $ideaRepo;
    protected $teamRepo;
    protected HackathonEditionRepository $editionRepository;

    public function __construct(
        IdeaRepository $ideaRepo,
        TeamRepository $teamRepo,
        HackathonEditionRepository $editionRepository
    ) {
        $this->ideaRepo = $ideaRepo;
        $this->teamRepo = $teamRepo;
        $this->editionRepository = $editionRepository;
    }

    /**
     * Get idea statistics
     */
    public function getStatistics(): array
    {
        return $this->ideaRepo->getStatistics([]);
    }

    public function getTeamIdea($userId)
    {
        $team = $this->teamRepo->findByLeaderId($userId);
        if (!$team) {
            $member = $this->teamRepo->findMemberTeam($userId);
            $team = $member ? $member->team : null;
        }

        return $team ? $this->ideaRepo->findByTeamId($team->id) : null;
    }

    public function submitIdea($userId, array $data)
    {
        return DB::transaction(function () use ($userId, $data) {
            $team = $this->teamRepo->findByLeaderId($userId);
            if (!$team) {
                throw new \Exception('Only team leaders can submit ideas');
            }

            $data['team_id'] = $team->id;
            $data['submitted_by'] = $userId;
            $data['status'] = 'submitted';

            return $this->ideaRepo->create($data);
        });
    }

    /**
     * Get paginated ideas based on user role and filters
     */
    public function getPaginatedIdeas(User $user, array $filters = [], int $perPage = 15): array
    {
        // Build filters based on user role
        $roleFilters = $this->buildRoleFilters($user, $filters);

        // Get paginated ideas
        $ideas = $this->ideaRepo->getPaginatedWithFilters($roleFilters, $perPage);

        // Get statistics
        $statistics = $this->ideaRepo->getStatistics($roleFilters);

        // Get editions for filter dropdown
        $editions = $this->getEditionsForUser($user);

        return [
            'ideas' => $ideas,
            'statistics' => $statistics,
            'editions' => $editions,
            'filters' => $filters
        ];
    }

    /**
     * Get idea details
     */
    public function getIdeaDetails(int $ideaId, User $user): ?array
    {
        $idea = $this->ideaRepo->findWithFullDetails($ideaId);

        if (!$idea) {
            return null;
        }

        // Check if user has access to this idea
        if (!$this->userCanAccessIdea($user, $idea)) {
            return null;
        }

        return [
            'idea' => $idea,
            'permissions' => $this->getIdeaPermissions($user, $idea)
        ];
    }

    /**
     * Process idea review
     */
    public function processReview(int $ideaId, array $data, User $user): array
    {
        $idea = $this->ideaRepo->find($ideaId);

        if (!$idea) {
            throw new \Exception('Idea not found.');
        }

        // Check permissions
        if (!$this->userCanReviewIdea($user, $idea)) {
            throw new \Exception('You do not have permission to review this idea.');
        }

        DB::beginTransaction();
        try {
            // Update idea status
            $updateData = [
                'status' => $data['status'],
                'reviewed_by' => $data['reviewed_by'] ?? $user->id,
                'reviewed_at' => now()
            ];

            if (isset($data['feedback'])) {
                $updateData['feedback'] = $data['feedback'];
            }

            if (isset($data['scores'])) {
                $updateData['evaluation_scores'] = $data['scores'];
                $updateData['score'] = array_sum($data['scores']);
            }

            $this->ideaRepo->update($ideaId, $updateData);

            // Add review record
            if (isset($data['scores']) || isset($data['feedback'])) {
                $this->ideaRepo->addReview($ideaId, [
                    'reviewer_id' => $user->id,
                    'status' => $data['status'],
                    'feedback' => $data['feedback'] ?? null,
                    'score' => isset($data['scores']) ? array_sum($data['scores']) : null,
                    'criteria_scores' => $data['scores'] ?? null
                ]);
            }

            // Log activity
            Log::info('Idea reviewed', [
                'idea_id' => $ideaId,
                'user_id' => $user->id,
                'status' => $data['status']
            ]);

            // Send notifications to team members
            $this->notifyTeamOfReview($idea, $user, $data['status'], $data['feedback'] ?? null);

            DB::commit();

            return [
                'success' => true,
                'message' => 'Idea review completed successfully. Team has been notified.'
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to review idea', [
                'idea_id' => $ideaId,
                'user_id' => $user->id,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Delete an idea
     */
    public function deleteIdea(int $ideaId, User $user): array
    {
        $idea = $this->ideaRepo->find($ideaId);

        if (!$idea) {
            throw new \Exception('Idea not found.');
        }

        // Check permissions
        if (!$this->userCanDeleteIdea($user, $idea)) {
            throw new \Exception('You do not have permission to delete this idea.');
        }

        DB::beginTransaction();
        try {
            // Delete associated files from storage
            if ($idea->files) {
                foreach ($idea->files as $file) {
                    \Storage::disk('public')->delete($file->path);
                }
            }

            // Delete idea
            $this->ideaRepo->delete($ideaId);

            // Log activity
            Log::info('Idea deleted', [
                'idea_id' => $ideaId,
                'user_id' => $user->id
            ]);

            DB::commit();

            return [
                'success' => true,
                'message' => 'Idea deleted successfully.'
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to delete idea', [
                'idea_id' => $ideaId,
                'user_id' => $user->id,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Export ideas to CSV
     */
    public function exportIdeas(User $user, array $filters = []): array
    {
        // Build filters based on user role
        $roleFilters = $this->buildRoleFilters($user, $filters);

        // Get ideas for export
        $ideas = $this->ideaRepo->getForExport($roleFilters);

        // Build CSV data
        $csvData = $this->buildExportData($ideas);

        return [
            'data' => $csvData,
            'filename' => 'ideas-export-' . date('Y-m-d') . '.csv'
        ];
    }

    /**
     * Build filters based on user role
     */
    protected function buildRoleFilters(User $user, array $filters): array
    {
        $roleFilters = $filters;

        if ($user->hasRole('hackathon_admin')) {
            // Limit to user's edition
            if ($user->edition_id) {
                $roleFilters['edition_id'] = $user->edition_id;
            }
        } elseif ($user->hasRole('track_supervisor')) {
            // Get supervised tracks
            $trackIds = DB::table('track_supervisors')
                ->where('user_id', $user->id)
                ->pluck('track_id')
                ->toArray();

            if (!empty($trackIds)) {
                $roleFilters['track_id'] = $trackIds;
            }
        } elseif ($user->hasRole('team_leader')) {
            // Limit to their team's ideas
            $team = $this->teamRepo->findByLeaderId($user->id);
            if ($team) {
                $roleFilters['team_id'] = $team->id;
            }
        } elseif ($user->hasRole('team_member')) {
            // Limit to their team's ideas
            $teams = DB::table('team_user')
                ->where('user_id', $user->id)
                ->pluck('team_id')
                ->toArray();

            if (!empty($teams)) {
                $roleFilters['team_id'] = $teams;
            }
        } elseif ($user->hasRole('system_admin')) {
            // No additional filters - can see everything
        } else {
            // Other roles - force empty result
            $roleFilters['force_empty'] = true;
        }

        return $roleFilters;
    }

    /**
     * Get editions available to user
     */
    protected function getEditionsForUser(User $user): Collection
    {
        if ($user->hasRole('system_admin')) {
            return $this->editionRepository->all();
        } elseif ($user->hasRole('hackathon_admin')) {
            if ($user->edition_id) {
                return collect([$this->editionRepository->find($user->edition_id)]);
            }
            return collect();
        } else {
            return collect();
        }
    }

    /**
     * Check if user can access a specific idea
     */
    protected function userCanAccessIdea(User $user, $idea): bool
    {
        if ($user->hasRole('system_admin')) {
            return true;
        } elseif ($user->hasRole('hackathon_admin')) {
            return $idea->team && $idea->team->edition_id == $user->edition_id;
        } elseif ($user->hasRole('track_supervisor')) {
            $trackIds = DB::table('track_supervisors')
                ->where('user_id', $user->id)
                ->pluck('track_id')
                ->toArray();
            return in_array($idea->track_id, $trackIds);
        } elseif ($user->hasRole('team_leader')) {
            return $idea->team_id == $this->teamRepo->findByLeaderId($user->id)?->id;
        } else {
            return false;
        }
    }

    /**
     * Check if user can review ideas
     */
    protected function userCanReviewIdea(User $user, $idea): bool
    {
        if (!$this->userCanAccessIdea($user, $idea)) {
            return false;
        }

        return $user->hasAnyRole(['system_admin', 'hackathon_admin', 'track_supervisor']);
    }

    /**
     * Check if user can delete ideas
     */
    protected function userCanDeleteIdea(User $user, $idea): bool
    {
        if (!$this->userCanAccessIdea($user, $idea)) {
            return false;
        }

        // Only system admin can delete
        return $user->hasRole('system_admin');
    }

    /**
     * Get permissions for an idea
     */
    protected function getIdeaPermissions(User $user, $idea): array
    {
        return [
            'canReview' => $this->userCanReviewIdea($user, $idea),
            'canDelete' => $this->userCanDeleteIdea($user, $idea),
            'canExport' => true,
        ];
    }

    /**
     * Build export data
     */
    protected function buildExportData(Collection $ideas): array
    {
        $csvData = [];
        $csvData[] = ['ID', 'Title', 'Team', 'Track', 'Status', 'Score', 'Reviewer', 'Submitted At', 'Reviewed At'];

        foreach ($ideas as $idea) {
            $csvData[] = [
                $idea->id,
                $idea->title,
                $idea->team?->name ?? 'N/A',
                $idea->track?->name ?? 'N/A',
                $idea->status,
                $idea->score ?? 'N/A',
                $idea->reviewer?->name ?? 'Not Assigned',
                $idea->submitted_at?->format('Y-m-d H:i') ?? 'N/A',
                $idea->reviewed_at?->format('Y-m-d H:i') ?? 'N/A',
            ];
        }

        return $csvData;
    }

    // Keep existing TeamLead methods below
    public function updateIdea($ideaId, $userId, array $data)
    {
        return DB::transaction(function () use ($ideaId, $userId, $data) {
            $idea = $this->ideaRepo->find($ideaId);
            $team = $this->teamRepo->findByLeaderId($userId);

            if (!$team || $idea->team_id !== $team->id) {
                throw new \Exception('Unauthorized to update this idea');
            }

            return $this->ideaRepo->update($ideaId, $data);
        });
    }

    public function addComment($ideaId, $userId, $comment, $parentId = null)
    {
        $idea = $this->ideaRepo->find($ideaId);
        $user = User::find($userId);

        if (!$idea) {
            throw new \Exception('Idea not found');
        }

        // Check if user has permission to comment
        $canComment = false;
        $isSupervisor = false;

        // Check if user is a supervisor with access to this idea
        if ($user->hasRole('track_supervisor')) {
            // Check if supervisor has access to this idea's track
            $edition = app(\App\Services\EditionContext::class)->current();
            $assignedTracks = $user->tracksInEdition($edition->id)->pluck('tracks.id');
            if ($assignedTracks->contains($idea->track_id)) {
                $canComment = true;
                $isSupervisor = true;
            }
        }

        // Check if user is part of the team
        if (!$canComment) {
            $team = $this->teamRepo->findByLeaderId($userId);
            if (!$team) {
                $member = $this->teamRepo->findMemberTeam($userId);
                $team = $member ? $member->team : null;
            }

            if ($team && $idea->team_id === $team->id) {
                $canComment = true;
            }
        }

        if (!$canComment) {
            throw new \Exception('Unauthorized to comment on this idea');
        }

        return $this->ideaRepo->addComment($ideaId, [
            'user_id' => $userId,
            'comment' => $comment,
            'is_supervisor' => $isSupervisor,
            'parent_id' => $parentId,
            'created_at' => now()
        ]);
    }

    /**
     * Get team comments (non-supervisor comments) for an idea
     */
    public function getTeamComments($ideaId)
    {
        $idea = $this->ideaRepo->find($ideaId);
        if (!$idea) {
            return collect([]);
        }

        return $idea->comments()
            ->where('is_supervisor', false)
            ->with('user')
            ->orderBy('created_at', 'asc')
            ->get();
    }

    /**
     * Get supervisor feedback (supervisor comments) for an idea
     */
    public function getSupervisorFeedback($ideaId)
    {
        $idea = $this->ideaRepo->find($ideaId);
        if (!$idea) {
            return collect([]);
        }

        return $idea->comments()
            ->where('is_supervisor', true)
            ->with('user')
            ->orderBy('created_at', 'asc')
            ->get();
    }

    /**
     * Notify team members about idea submission
     */
    protected function notifyTeamOfSubmission(Idea $idea, User $submitter): void
    {
        if (!$idea->team) {
            return;
        }

        // Get all team members
        $teamMembers = $idea->team->members()->get();

        foreach ($teamMembers as $member) {
            // Don't notify the submitter themselves
            if ($member->id !== $submitter->id) {
                $member->notify(new IdeaSubmittedNotification($idea, $submitter));
            }
        }

        // Also notify track supervisors
        if ($idea->team->track && $idea->team->track->supervisors) {
            foreach ($idea->team->track->supervisors as $supervisor) {
                $supervisor->notify(new IdeaSubmittedNotification($idea, $submitter));
            }
        }
    }

    /**
     * Notify team members about idea review
     */
    protected function notifyTeamOfReview(Idea $idea, User $reviewer, string $status, ?string $feedback = null): void
    {
        if (!$idea->team) {
            return;
        }

        // Store old status for comparison
        $oldStatus = $idea->getOriginal('status');

        // Get all team members
        $teamMembers = $idea->team->members()->get();

        foreach ($teamMembers as $member) {
            // Send review notification
            $member->notify(new IdeaReviewedNotification($idea, $reviewer, $status, $feedback));

            // Send status change notification if status changed
            if ($oldStatus && $oldStatus !== $status) {
                $member->notify(new IdeaStatusChangedNotification($idea, $oldStatus, $status, $reviewer));
            }
        }

        // Also notify the team leader if not already in members
        if ($idea->team->leader && !$teamMembers->contains('id', $idea->team->leader->id)) {
            $idea->team->leader->notify(new IdeaReviewedNotification($idea, $reviewer, $status, $feedback));
            if ($oldStatus && $oldStatus !== $status) {
                $idea->team->leader->notify(new IdeaStatusChangedNotification($idea, $oldStatus, $status, $reviewer));
            }
        }
    }

    /**
     * Notify team members about idea status change
     */
    protected function notifyTeamOfStatusChange(Idea $idea, string $oldStatus, string $newStatus, User $changedBy): void
    {
        if (!$idea->team) {
            return;
        }

        // Get all team members
        $teamMembers = $idea->team->members()->get();

        foreach ($teamMembers as $member) {
            $member->notify(new IdeaStatusChangedNotification($idea, $oldStatus, $newStatus, $changedBy));
        }

        // Also notify track supervisors if status is significant
        if (in_array($newStatus, ['submitted', 'approved', 'rejected'])) {
            if ($idea->team->track && $idea->team->track->supervisors) {
                foreach ($idea->team->track->supervisors as $supervisor) {
                    $supervisor->notify(new IdeaStatusChangedNotification($idea, $oldStatus, $newStatus, $changedBy));
                }
            }
        }
    }
}
