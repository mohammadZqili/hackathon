<?php

namespace App\Http\Controllers\TeamMember;

use App\Http\Controllers\Controller;
use App\Services\TeamService;
use Inertia\Inertia;

class TeamController extends Controller
{
    protected $teamService;

    public function __construct(TeamService $teamService)
    {
        $this->teamService = $teamService;
    }

    public function index()
    {
        $team = $this->teamService->getMemberTeam(auth()->id());
        
        return Inertia::render('TeamMember/Team/Index', [
            'team' => $team
        ]);
    }
}
