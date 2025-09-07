<?php

namespace App\Repositories;

use App\Models\Idea;
use App\Models\IdeaFile;
use App\Models\User;
use App\Repositories\Contracts\IdeaRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class IdeaRepository extends BaseRepository implements IdeaRepositoryInterface
{
    public function __construct(Idea $model)
    {
        parent::__construct($model);
    }

    public function getIdeasByTrack(int $trackId): Collection
    {
        return $this->model->where('track_id', $trackId)
            ->with(['team.leader', 'team.members'])
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function getIdeasByStatus(string $status): Collection
    {
        return $this->model->where('status', $status)
            ->with(['team.leader', 'track'])
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function getIdeasForSupervisorReview(int $supervisorId): Collection
    {
        return $this->model->whereHas('track.supervisors', function ($query) use ($supervisorId) {
            $query->where('user_id', $supervisorId);
        })
        ->whereIn('status', ['submitted', 'under_review'])
        ->with(['team.leader', 'track', 'files'])
        ->orderBy('submitted_at', 'asc')
        ->get();
    }

    public function getPaginatedWithFilters(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = $this->model->newQuery();

        if (!empty($filters['search'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('title', 'like', '%' . $filters['search'] . '%')
                  ->orWhere('description', 'like', '%' . $filters['search'] . '%');
            });
        }

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['track_id'])) {
            $query->where('track_id', $filters['track_id']);
        }

        if (!empty($filters['team_name'])) {
            $query->whereHas('team', function ($q) use ($filters) {
                $q->where('name', 'like', '%' . $filters['team_name'] . '%');
            });
        }

        if (!empty($filters['supervisor_id'])) {
            $query->whereHas('track.supervisors', function ($q) use ($filters) {
                $q->where('user_id', $filters['supervisor_id']);
            });
        }

        return $query->with(['team.leader', 'track', 'reviewer'])
            ->orderBy('submitted_at', 'desc')
            ->paginate($perPage);
    }

    public function submitForReview(int $ideaId): bool
    {
        return $this->model->whereId($ideaId)->update([
            'status' => 'submitted',
            'submitted_at' => now(),
        ]);
    }

    public function evaluateIdea(int $ideaId, string $status, ?string $feedback = null, ?float $score = null, ?int $reviewerId = null): bool
    {
        return $this->model->whereId($ideaId)->update([
            'status' => $status,
            'feedback' => $feedback,
            'score' => $score,
            'reviewed_by' => $reviewerId,
            'reviewed_at' => now(),
        ]);
    }

    public function getIdeaWithRelations(int $ideaId): ?Idea
    {
        return $this->model->with([
            'team.leader',
            'team.members',
            'track',
            'files',
            'auditLogs.user',
            'reviewer'
        ])->find($ideaId);
    }

    public function getIdeasNeedingReview(): Collection
    {
        return $this->model->whereIn('status', ['submitted', 'under_review'])
            ->with(['team.leader', 'track'])
            ->orderBy('submitted_at', 'asc')
            ->get();
    }

    public function getAcceptedIdeas(): Collection
    {
        return $this->model->where('status', 'accepted')
            ->with(['team.leader', 'track'])
            ->orderBy('reviewed_at', 'desc')
            ->get();
    }

    public function getRejectedIdeas(): Collection
    {
        return $this->model->where('status', 'rejected')
            ->with(['team.leader', 'track'])
            ->orderBy('reviewed_at', 'desc')
            ->get();
    }

    public function addFile(int $ideaId, array $fileData): bool
    {
        $idea = $this->find($ideaId);
        if (!$idea) {
            return false;
        }

        $idea->files()->create($fileData);
        return true;
    }

    public function removeFile(int $ideaId, int $fileId): bool
    {
        $idea = $this->find($ideaId);
        if (!$idea) {
            return false;
        }

        return $idea->files()->where('id', $fileId)->delete() > 0;
    }

    public function getStatistics(): array
    {
        $totalIdeas = $this->count();
        
        $statusCounts = $this->model->selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        $avgScore = $this->model->whereNotNull('score')->avg('score');

        return [
            'total_ideas' => $totalIdeas,
            'status_distribution' => [
                'draft' => $statusCounts['draft'] ?? 0,
                'submitted' => $statusCounts['submitted'] ?? 0,
                'under_review' => $statusCounts['under_review'] ?? 0,
                'needs_revision' => $statusCounts['needs_revision'] ?? 0,
                'accepted' => $statusCounts['accepted'] ?? 0,
                'rejected' => $statusCounts['rejected'] ?? 0,
            ],
            'average_score' => round($avgScore ?? 0, 2),
            'ideas_needing_review' => $statusCounts['submitted'] ?? 0 + $statusCounts['under_review'] ?? 0,
        ];
    }

    /**
     * Get ideas by hackathon
     */
    public function getIdeasByHackathon(int $hackathonId): Collection
    {
        return $this->model->whereHas('team', function ($query) use ($hackathonId) {
            $query->where('hackathon_id', $hackathonId);
        })
        ->with(['team.leader', 'track'])
        ->get();
    }

    /**
     * Update idea status with audit log
     */
    public function updateStatusWithAudit(int $ideaId, string $status, ?string $feedback = null, ?int $userId = null): bool
    {
        $idea = $this->find($ideaId);
        if (!$idea) {
            return false;
        }

        $idea->update([
            'status' => $status,
            'feedback' => $feedback,
            'reviewed_by' => $userId,
            'reviewed_at' => now(),
        ]);

        // Create audit log
        $idea->auditLogs()->create([
            'user_id' => $userId,
            'action' => 'status_changed',
            'field_name' => 'status',
            'new_value' => $status,
            'notes' => $feedback,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);

        return true;
    }

    /**
     * Get top rated ideas
     */
    public function getTopRatedIdeas(int $limit = 10): Collection
    {
        return $this->model->whereNotNull('score')
            ->where('status', 'accepted')
            ->with(['team.leader', 'track'])
            ->orderBy('score', 'desc')
            ->limit($limit)
            ->get();
    }
}
