<?php

namespace App\Http\Controllers\SystemAdmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Idea;

class IdeaController extends Controller
{
    public function index()
    {
        $ideas = Idea::with(['team.hackathon', 'track', 'reviewer', 'files'])
            ->latest()
            ->paginate(15);

        return Inertia::render('SystemAdmin/Ideas/Index', [
            'ideas' => $ideas
        ]);
    }

    public function show(Idea $idea)
    {
        $idea->load(['team.hackathon', 'track', 'reviewer', 'files', 'auditLogs']);

        return Inertia::render('SystemAdmin/Ideas/Show', [
            'idea' => $idea
        ]);
    }

    public function destroy(Idea $idea)
    {
        $idea->delete();

        return redirect()->route('system-admin.ideas.index')
            ->with('success', 'Idea deleted successfully.');
    }

    public function export()
    {
        // TODO: Implement export functionality
        return response()->json(['message' => 'Export functionality to be implemented']);
    }
}
