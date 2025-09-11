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
        
        return Inertia::render('TeamMember/Idea/Index', [
            'idea' => $idea
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
}
