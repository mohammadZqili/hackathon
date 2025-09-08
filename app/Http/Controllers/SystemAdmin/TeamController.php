<?php

namespace App\Http\Controllers\SystemAdmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Team;

class TeamController extends Controller
{
    public function index()
    {
        $teams = Team::with(['hackathon', 'leader', 'members', 'idea'])
            ->latest()
            ->paginate(15);

        return Inertia::render('SystemAdmin/Teams/Index', [
            'teams' => $teams
        ]);
    }

    public function show(Team $team)
    {
        $team->load(['hackathon', 'leader', 'members', 'idea']);

        return Inertia::render('SystemAdmin/Teams/Show', [
            'team' => $team
        ]);
    }

    public function destroy(Team $team)
    {
        $team->delete();

        return redirect()->route('system-admin.teams.index')
            ->with('success', 'Team deleted successfully.');
    }

    public function export()
    {
        // TODO: Implement export functionality
        return response()->json(['message' => 'Export functionality to be implemented']);
    }
}
