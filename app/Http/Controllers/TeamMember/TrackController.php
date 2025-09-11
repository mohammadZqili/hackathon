<?php

namespace App\Http\Controllers\TeamMember;

use App\Http\Controllers\Controller;
use App\Services\TrackService;
use Inertia\Inertia;

class TrackController extends Controller
{
    protected $trackService;

    public function __construct(TrackService $trackService)
    {
        $this->trackService = $trackService;
    }

    public function index()
    {
        $tracks = $this->trackService->getActiveTracksWithTeams();
        
        return Inertia::render('TeamMember/Tracks/Index', [
            'tracks' => $tracks
        ]);
    }

    public function show($id)
    {
        $track = $this->trackService->getTrackDetails($id);
        
        return Inertia::render('TeamMember/Tracks/Show', [
            'track' => $track
        ]);
    }
}
