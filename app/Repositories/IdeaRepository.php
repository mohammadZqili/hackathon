<?php

namespace App\Repositories;

use App\Models\Idea;
use App\Models\IdeaReview;
use App\Models\IdeaComment;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class IdeaRepository extends BaseRepository
{
    public function __construct()
    {
        parent::__construct(new Idea());
    }

    /**
     * Get paginated ideas with filters
     */
    public function getPaginatedWithFilters(array $filters, int $perPage = 15): LengthAwarePaginator
    {
        $query = $this->query()
            ->with(['team.leader', 'team.members', 'track', 'reviewer'])
            ->withCount(['reviews']);

        // Apply filters
        if (!empty($filters['edition_id'])) {
            $query->whereHas('team', function ($q) use ($filters) {
                $q->where('edition_id', $filters['edition_id']);
            });
        }

        if (!empty($filters['track_id'])) {
            // Handle both single track ID and array of track IDs
            if (is_array($filters['track_id'])) {
                $query->whereIn('track_id', $filters['track_id']);
            } else {
                $query->where('track_id', $filters['track_id']);
            }
        }

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['team_id'])) {
            $query->where('team_id', $filters['team_id']);
        }

        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhereHas('team', function ($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%");
                  });
            });
        }

        if (!empty($filters['idea_ids'])) {
            $query->whereIn('id', $filters['idea_ids']);
        }

        // Filter by review status
        if (!empty($filters['review_status'])) {
            switch ($filters['review_status']) {
                case 'pending':
                    $query->whereDoesntHave('reviews');
                    break;
                case 'reviewed':
                    $query->whereHas('reviews');
                    break;
                case 'approved':
                    $query->whereHas('reviews', function ($q) {
                        $q->where('status', 'approved');
                    });
                    break;
                case 'rejected':
                    $query->whereHas('reviews', function ($q) {
                        $q->where('status', 'rejected');
                    });
                    break;
            }
        }

        return $query->latest()->paginate($perPage)->withQueryString();
    }

    /**
     * Get idea with full details
     */
    public function findWithFullDetails(string $id): ?Idea
    {
        return $this->query()
            ->with([
                'team.leader',
                'team.members.user',
                'track',
                'reviewer',
                'files',
                'comments.user',
                'auditLogs.user'
            ])
            ->withCount(['reviews', 'comments'])
            ->find($id);
    }

    /**
     * Get statistics for ideas
     */
    public function getStatistics(array $filters = []): array
    {
        $query = $this->query();

        // Apply filters
        if (!empty($filters['edition_id'])) {
            $query->whereHas('team', function ($q) use ($filters) {
                $q->where('edition_id', $filters['edition_id']);
            });
        }

        if (!empty($filters['track_id'])) {
            $query->where('track_id', $filters['track_id']);
        }

        if (!empty($filters['idea_ids'])) {
            $query->whereIn('id', $filters['idea_ids']);
        }

        return [
            'total' => $query->count(),
            'submitted' => (clone $query)->where('status', 'submitted')->count(),
            'draft' => (clone $query)->where('status', 'draft')->count(),
            'approved' => (clone $query)->where('status', 'approved')->count(),
            'rejected' => (clone $query)->where('status', 'rejected')->count(),
            'pending_review' => (clone $query)->whereDoesntHave('reviews')->where('status', 'submitted')->count(),
            'reviewed' => (clone $query)->whereHas('reviews')->count(),
        ];
    }

    /**
     * Find idea by team ID
     */
    public function findByTeamId(string $teamId): ?Idea
    {
        return $this->query()
            ->where('team_id', $teamId)
            ->with(['files', 'track', 'reviewer', 'auditLogs.user'])
            ->first();
    }

    /**
     * Get ideas for export
     */
    public function getForExport(array $filters = []): Collection
    {
        $query = $this->query()
            ->with(['team.leader', 'track', 'reviewer'])
            ->withCount(['files']);

        if (!empty($filters['edition_id'])) {
            $query->whereHas('team', function ($q) use ($filters) {
                $q->where('edition_id', $filters['edition_id']);
            });
        }

        if (!empty($filters['track_id'])) {
            $query->where('track_id', $filters['track_id']);
        }

        if (!empty($filters['idea_ids'])) {
            $query->whereIn('id', $filters['idea_ids']);
        }

        return $query->get();
    }

    /**
     * Get ideas by team
     */
    public function getByTeam(string $teamId): Collection
    {
        return $this->query()
            ->where('team_id', $teamId)
            ->with(['track', 'reviews'])
            ->get();
    }

    /**
     * Get ideas by track
     */
    public function getByTrack(int $trackId): Collection
    {
        return $this->query()
            ->where('track_id', $trackId)
            ->with(['team', 'reviews'])
            ->get();
    }

    /**
     * Get ideas pending review
     */
    public function getPendingReview(array $filters = []): Collection
    {
        $query = $this->query()
            ->where('status', 'submitted')
            ->whereDoesntHave('reviews')
            ->with(['team', 'track']);

        if (!empty($filters['track_id'])) {
            $query->where('track_id', $filters['track_id']);
        }

        if (!empty($filters['edition_id'])) {
            $query->whereHas('team', function ($q) use ($filters) {
                $q->where('edition_id', $filters['edition_id']);
            });
        }

        return $query->orderBy('submitted_at')->get();
    }

    /**
     * Add review to idea
     */
    public function addReview(int $ideaId, array $reviewData): IdeaReview
    {
        return IdeaReview::create([
            'idea_id' => $ideaId,
            'reviewer_id' => $reviewData['reviewer_id'],
            'score' => $reviewData['score'] ?? null,
            'feedback' => $reviewData['feedback'] ?? null,
            'status' => $reviewData['status'],
            'criteria_scores' => $reviewData['criteria_scores'] ?? null,
            'reviewed_at' => now()
        ]);
    }

    /**
     * Add comment to idea
     */
    public function addComment(int $ideaId, array $commentData): IdeaComment
    {
        return IdeaComment::create([
            'idea_id' => $ideaId,
            'user_id' => $commentData['user_id'],
            'comment' => $commentData['comment'],
            'is_supervisor' => $commentData['is_supervisor'] ?? false,
            'parent_id' => $commentData['parent_id'] ?? null,
            'created_at' => $commentData['created_at'] ?? now()
        ]);
    }

    /**
     * Get comments for idea
     */
    public function getComments(int $ideaId): Collection
    {
        return IdeaComment::where('idea_id', $ideaId)
            ->with('user')
            ->topLevel() // Only get top-level comments
            ->orderBy('created_at', 'asc')
            ->get();
    }

    /**
     * Update idea status
     */
    public function updateStatus(int $id, string $status): bool
    {
        $idea = $this->find($id);
        if (!$idea) {
            return false;
        }

        $updateData = ['status' => $status];
        
        // Set submitted_at when status changes to submitted
        if ($status === 'submitted' && !$idea->submitted_at) {
            $updateData['submitted_at'] = now();
        }

        return $this->update($id, $updateData);
    }

    /**
     * Get top scored ideas
     */
    public function getTopScored(int $limit = 10, array $filters = []): Collection
    {
        $query = $this->query()
            ->with(['team', 'track', 'reviews'])
            ->withAvg('reviews', 'score')
            ->whereHas('reviews')
            ->orderByDesc('reviews_avg_score');

        if (!empty($filters['edition_id'])) {
            $query->whereHas('team', function ($q) use ($filters) {
                $q->where('edition_id', $filters['edition_id']);
            });
        }

        if (!empty($filters['track_id'])) {
            $query->where('track_id', $filters['track_id']);
        }

        return $query->limit($limit)->get();
    }

    /**
     * Check if idea can be edited
     */
    public function canEdit(string $id): bool
    {
        $idea = $this->find($id);
        if (!$idea) {
            return false;
        }

        // Can only edit if in draft status or submitted but not reviewed
        return $idea->status === 'draft' || 
               ($idea->status === 'submitted' && !$idea->reviews()->exists());
    }

    /**
     * Check if idea has reviews
     */
    public function hasReviews(string $id): bool
    {
        $idea = $this->find($id);
        if (!$idea) {
            return false;
        }

        return $idea->reviews()->exists();
    }

    /**
     * Count ideas with filters
     */
    public function count(array $filters = []): int
    {
        $query = $this->query();

        if (!empty($filters['edition_id'])) {
            $query->whereHas('team', function ($q) use ($filters) {
                $q->where('edition_id', $filters['edition_id']);
            });
        }

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['created_date'])) {
            $query->whereDate('created_at', $filters['created_date']);
        }

        if (!empty($filters['force_empty'])) {
            $query->whereRaw('1 = 0');
        }

        return $query->count();
    }

    /**
     * Get recent ideas
     */
    public function getRecent(int $limit, array $filters = []): Collection
    {
        $query = $this->query()->with(['team.leader', 'track']);

        if (!empty($filters['edition_id'])) {
            $query->whereHas('team', function ($q) use ($filters) {
                $q->where('edition_id', $filters['edition_id']);
            });
        }

        if (!empty($filters['force_empty'])) {
            $query->whereRaw('1 = 0');
        }

        return $query->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }
}