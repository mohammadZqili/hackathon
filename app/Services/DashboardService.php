<?php

namespace App\Services;

use App\Repositories\TeamRepository;
use App\Repositories\IdeaRepository;
use App\Repositories\WorkshopRepository;
use App\Repositories\TrackRepository;
use Illuminate\Support\Facades\Auth;

class DashboardService extends BaseService
{
    protected $teamRepo;
    protected $ideaRepo;
    protected $workshopRepo;
    protected $trackRepo;

    public function __construct(
        TeamRepository $teamRepo,
        IdeaRepository $ideaRepo,
        WorkshopRepository $workshopRepo,
        TrackRepository $trackRepo
    ) {
        $this->teamRepo = $teamRepo;
        $this->ideaRepo = $ideaRepo;
        $this->workshopRepo = $workshopRepo;
        $this->trackRepo = $trackRepo;
    }

    public function getTeamLeadDashboard($userId)
    {
        $team = $this->teamRepo->findByLeaderId($userId);
        $idea = $team ? $this->ideaRepo->findByTeamId($team->id) : null;
        $workshops = $this->workshopRepo->getUpcoming();
        $tracks = $this->trackRepo->getActive();

        return [
            'team' => $team,
            'idea' => $idea,
            'workshops' => $workshops,
            'tracks' => $tracks,
            'stats' => [
                'team_members' => $team ? $team->members()->count() : 0,
                'idea_status' => $idea ? $idea->status : 'pending',
                'workshops_registered' => $this->workshopRepo->countUserWorkshops($userId),
                'track' => $team && $team->track ? $team->track->name : null
            ]
        ];
    }

    public function getTeamMemberDashboard($userId)
    {
        $member = $this->teamRepo->findMemberTeam($userId);
        $team = $member ? $member->team : null;
        $idea = $team ? $this->ideaRepo->findByTeamId($team->id) : null;
        $workshops = $this->workshopRepo->getUpcoming();

        return [
            'team' => $team,
            'idea' => $idea,
            'workshops' => $workshops,
            'stats' => [
                'team_name' => $team ? $team->name : null,
                'idea_status' => $idea ? $idea->status : 'pending',
                'workshops_registered' => $this->workshopRepo->countUserWorkshops($userId),
                'role' => $member ? $member->role : null
            ]
        ];
    }

    public function getVisitorDashboard($userId)
    {
        $workshops = $this->workshopRepo->getUpcoming();
        $myWorkshops = $this->workshopRepo->getUserWorkshops($userId);

        return [
            'workshops' => $workshops,
            'myWorkshops' => $myWorkshops,
            'stats' => [
                'total_workshops' => $workshops->count(),
                'registered_workshops' => $myWorkshops->count()
            ]
        ];
    }
}
