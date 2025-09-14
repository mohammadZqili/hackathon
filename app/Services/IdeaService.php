<?php

namespace App\Services;

use App\Models\User;
use App\Repositories\IdeaRepository;
use App\Repositories\TeamRepository;
use App\Repositories\HackathonEditionRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
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

            DB::commit();

            return [
                'success' => true,
                'message' => 'Idea review completed successfully.'
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

        switch ($user->user_type) {
            case 'hackathon_admin':
                // Limit to user's edition
                if ($user->edition_id) {
                    $roleFilters['edition_id'] = $user->edition_id;
                }
                break;

            case 'track_supervisor':
                // Get supervised tracks
                $trackIds = DB::table('track_supervisors')
                    ->where('user_id', $user->id)
                    ->pluck('track_id')
                    ->toArray();

                if (!empty($trackIds)) {
                    $roleFilters['track_id'] = $trackIds;
                }
                break;

            case 'team_leader':
                // Limit to their team's ideas
                $team = $this->teamRepo->findByLeaderId($user->id);
                if ($team) {
                    $roleFilters['team_id'] = $team->id;
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
     * Check if user can access a specific idea
     */
    protected function userCanAccessIdea(User $user, $idea): bool
    {
        switch ($user->user_type) {
            case 'system_admin':
                return true;

            case 'hackathon_admin':
                return $idea->team && $idea->team->edition_id == $user->edition_id;

            case 'track_supervisor':
                $trackIds = DB::table('track_supervisors')
                    ->where('user_id', $user->id)
                    ->pluck('track_id')
                    ->toArray();
                return in_array($idea->track_id, $trackIds);

            case 'team_leader':
                return $idea->team_id == $this->teamRepo->findByLeaderId($user->id)?->id;

            default:
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

        return in_array($user->user_type, ['system_admin', 'hackathon_admin', 'track_supervisor']);
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

    public function addComment($ideaId, $userId, $comment)
    {
        $idea = $this->ideaRepo->find($ideaId);

        // Check if user is part of the team
        $team = $this->teamRepo->findByLeaderId($userId);
        if (!$team) {
            $member = $this->teamRepo->findMemberTeam($userId);
            $team = $member ? $member->team : null;
        }

        if (!$team || $idea->team_id !== $team->id) {
            throw new \Exception('Unauthorized to comment on this idea');
        }

        return $this->ideaRepo->addComment($ideaId, [
            'user_id' => $userId,
            'comment' => $comment,
            'created_at' => now()
        ]);
    }
}
