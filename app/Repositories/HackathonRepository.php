<?php

namespace App\Repositories;

use App\Models\Hackathon;
use App\Repositories\Contracts\HackathonRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class HackathonRepository extends BaseRepository implements HackathonRepositoryInterface
{
    public function __construct(Hackathon $model)
    {
        parent::__construct($model);
    }

    public function getCurrentHackathon(): ?Hackathon
    {
        return $this->model->where('is_current', true)
            ->where('is_active', true)
            ->first();
    }

    public function getActiveHackathons(): Collection
    {
        return $this->model->where('is_active', true)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function getHackathonsByYear(int $year): Collection
    {
        return $this->model->where('year', $year)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function setAsCurrent(int $hackathonId): bool
    {
        // First, unset all other hackathons as current
        $this->model->where('is_current', true)->update(['is_current' => false]);
        
        // Then set the specified hackathon as current
        return $this->model->whereId($hackathonId)->update([
            'is_current' => true,
            'is_active' => true
        ]);
    }

    public function getWithStats(int $hackathonId): ?Hackathon
    {
        return $this->model->with([
            'tracks' => function ($query) {
                $query->withCount('ideas');
            },
            'teams' => function ($query) {
                $query->withCount('members');
            },
            'workshops' => function ($query) {
                $query->withCount('registrations');
            }
        ])
        ->withCount(['teams', 'workshops'])
        ->find($hackathonId);
    }

    public function getPaginatedWithFilters(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = $this->model->newQuery();

        if (!empty($filters['search'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('name', 'like', '%' . $filters['search'] . '%')
                  ->orWhere('description', 'like', '%' . $filters['search'] . '%');
            });
        }

        if (!empty($filters['year'])) {
            $query->where('year', $filters['year']);
        }

        if (isset($filters['is_active'])) {
            $query->where('is_active', $filters['is_active']);
        }

        if (isset($filters['is_current'])) {
            $query->where('is_current', $filters['is_current']);
        }

        return $query->with('creator')
            ->orderBy('year', 'desc')
            ->paginate($perPage);
    }

    public function isRegistrationOpen(int $hackathonId): bool
    {
        $hackathon = $this->find($hackathonId);
        return $hackathon ? $hackathon->isRegistrationOpen() : false;
    }

    public function isIdeaSubmissionOpen(int $hackathonId): bool
    {
        $hackathon = $this->find($hackathonId);
        return $hackathon ? $hackathon->isIdeaSubmissionOpen() : false;
    }

    public function getUpcomingHackathons(): Collection
    {
        return $this->model->where('is_active', true)
            ->where('event_start_date', '>', now())
            ->orderBy('event_start_date', 'asc')
            ->get();
    }

    public function getPastHackathons(): Collection
    {
        return $this->model->where('event_end_date', '<', now())
            ->orderBy('event_end_date', 'desc')
            ->get();
    }

    /**
     * Get hackathon with all related data
     */
    public function getWithAllRelations(int $hackathonId): ?Hackathon
    {
        return $this->model->with([
            'tracks.supervisors',
            'tracks.ideas.team',
            'teams.leader',
            'teams.members',
            'workshops.speakers',
            'workshops.organizations'
        ])->find($hackathonId);
    }

    /**
     * Get hackathon statistics
     */
    public function getStatistics(int $hackathonId): array
    {
        $hackathon = $this->getWithStats($hackathonId);
        
        if (!$hackathon) {
            return [];
        }

        $totalParticipants = $hackathon->teams->sum(function ($team) {
            return $team->members_count;
        });

        $submittedIdeas = $hackathon->teams()->whereHas('idea', function ($query) {
            $query->where('status', 'submitted');
        })->count();

        $acceptedIdeas = $hackathon->teams()->whereHas('idea', function ($query) {
            $query->where('status', 'accepted');
        })->count();

        return [
            'total_teams' => $hackathon->teams_count,
            'total_participants' => $totalParticipants,
            'submitted_ideas' => $submittedIdeas,
            'accepted_ideas' => $acceptedIdeas,
            'total_workshops' => $hackathon->workshops_count,
            'workshop_registrations' => $hackathon->workshops->sum(function ($workshop) {
                return $workshop->registrations_count;
            })
        ];
    }
}
