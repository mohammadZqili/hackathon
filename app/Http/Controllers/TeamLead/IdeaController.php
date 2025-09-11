<?php

namespace App\Http\Controllers\TeamLead;

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
        
        return Inertia::render('TeamLead/Idea/Index', [
            'idea' => $idea,
            'canSubmit' => !$idea
        ]);
    }

    public function create()
    {
        $idea = $this->ideaService->getTeamIdea(auth()->id());
        if ($idea) {
            return redirect()->route('team-lead.idea.index');
        }

        return Inertia::render('TeamLead/Idea/Submit');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'problem_statement' => 'required|string',
            'solution' => 'required|string',
            'target_audience' => 'required|string',
            'unique_value' => 'required|string',
            'technical_feasibility' => 'required|string',
            'business_model' => 'nullable|string',
            'attachments' => 'nullable|array',
            'attachments.*' => 'file|max:10240'
        ]);

        try {
            $idea = $this->ideaService->submitIdea(auth()->id(), $validated);
            
            return redirect()->route('team-lead.idea.index')
                ->with('success', 'Idea submitted successfully');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function edit($id)
    {
        $idea = $this->ideaService->getTeamIdea(auth()->id());
        
        if (!$idea || $idea->id != $id) {
            return redirect()->route('team-lead.idea.index');
        }

        return Inertia::render('TeamLead/Idea/Edit', [
            'idea' => $idea
        ]);
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'problem_statement' => 'required|string',
            'solution' => 'required|string',
            'target_audience' => 'required|string',
            'unique_value' => 'required|string',
            'technical_feasibility' => 'required|string',
            'business_model' => 'nullable|string'
        ]);

        try {
            $idea = $this->ideaService->updateIdea($id, auth()->id(), $validated);
            
            return redirect()->route('team-lead.idea.index')
                ->with('success', 'Idea updated successfully');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
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
