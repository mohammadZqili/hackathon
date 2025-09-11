<?php

namespace App\Repositories;

use App\Models\Track;

class TrackRepository
{
    public function getAll()
    {
        return Track::with(['teams', 'supervisors'])->get();
    }

    public function getActive()
    {
        return Track::where('is_active', true)
            ->with(['teams', 'supervisors'])
            ->get();
    }

    public function getActiveWithTeams()
    {
        return Track::where('is_active', true)
            ->withCount('teams')
            ->with('supervisors')
            ->get();
    }

    public function find($id)
    {
        return Track::findOrFail($id);
    }

    public function findWithDetails($id)
    {
        return Track::with(['teams.members', 'supervisors', 'ideas'])
            ->findOrFail($id);
    }

    public function getSupervisors($trackId)
    {
        $track = Track::findOrFail($trackId);
        return $track->supervisors;
    }
}
