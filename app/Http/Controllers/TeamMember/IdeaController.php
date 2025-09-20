<?php

namespace App\Http\Controllers\TeamMember;

use App\Http\Controllers\Controller;
use App\Services\IdeaService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class IdeaController extends Controller
{
    protected $ideaService;

    public function __construct(IdeaService $ideaService)
    {
        $this->ideaService = $ideaService;
    }

    public function index()
    {
        $idea = $this->ideaService->getTeamIdea(auth()->id());

        // Load additional relationships for full idea display
        if ($idea) {
            $idea->load(['track', 'team', 'files']);

            // Get separated comments
            $teamComments = $this->ideaService->getTeamComments($idea->id);
            $supervisorFeedback = $this->ideaService->getSupervisorFeedback($idea->id);
            $instructions = $this->ideaService->getActiveInstructions($idea->id);
        }

        return Inertia::render('TeamMember/Idea/Index', [
            'idea' => $idea,
            'teamComments' => $idea ? $teamComments : [],
            'supervisorFeedback' => $idea ? $supervisorFeedback : [],
            'instructions' => $idea ? ($instructions ?? []) : []
        ]);
    }

    public function addComment(Request $request, $id)
    {
        $validated = $request->validate([
            'comment' => 'required|string'
        ]);

        try {
            $this->ideaService->addComment($id, auth()->id(), $validated['comment']);
            return back()->with('success', 'Comment added successfully');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function downloadFile($ideaId, $fileId)
    {
        $idea = $this->ideaService->getTeamIdea(auth()->id());

        if (!$idea || $idea->id != $ideaId) {
            return redirect()->route('team-member.idea.index')
                ->with('error', 'You don\'t have access to this idea.');
        }

        $file = $idea->files()->findOrFail($fileId);
        $filePath = storage_path('app/public/' . $file->file_path);

        if (!file_exists($filePath)) {
            return redirect()->back()->with('error', 'File not found.');
        }

        return response()->download($filePath, $file->original_name);
    }
}
