<?php

namespace App\Http\Controllers\TeamLead;

use App\Http\Controllers\Controller;
use App\Services\TeamService;
use App\Services\IdeaService;
use App\Repositories\IdeaRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class IdeaController extends Controller
{
    protected $teamService;
    protected $ideaService;
    protected $ideaRepository;

    public function __construct(
        TeamService $teamService,
        IdeaService $ideaService,
        IdeaRepository $ideaRepository
    ) {
        $this->teamService = $teamService;
        $this->ideaService = $ideaService;
        $this->ideaRepository = $ideaRepository;
    }

    public function index()
    {
        $user = auth()->user();
        $team = $this->teamService->getMyTeam($user);
        
        if (!$team) {
            return redirect()->route('team-lead.dashboard')
                ->with('error', 'You need to create a team first');
        }

        $idea = $this->teamService->getTeamIdea($team);
        
        if (!$idea) {
            return redirect()->route('team-lead.idea.create');
        }
        
        return redirect()->route('team-lead.idea.edit-my');
    }

    public function show()
    {
        $user = auth()->user();
        $team = $this->teamService->getMyTeam($user);
        
        if (!$team) {
            return redirect()->route('team-leader.dashboard')
                ->with('error', 'You need to create a team first');
        }

        $idea = $this->teamService->getTeamIdea($team);
        
        if (!$idea) {
            return redirect()->route('team-leader.idea.create');
        }
        
        return Inertia::render('TeamLead/Idea/Show', [
            'idea' => $idea,
            'team' => $team
        ]);
    }

    public function create()
    {
        $user = auth()->user();
        $team = $this->teamService->getMyTeam($user);
        
        if (!$team) {
            return redirect()->route('team-leader.dashboard')
                ->with('error', 'You need to create a team first');
        }

        $idea = $this->teamService->getTeamIdea($team);
        if ($idea) {
            return redirect()->route('team-leader.idea.show');
        }

        return Inertia::render('TeamLead/Idea/Create', [
            'team' => $team,
            'tracks' => [$team->track] // Team's assigned track
        ]);
    }

    public function store(Request $request)
    {
        $user = auth()->user();
        $team = $this->teamService->getMyTeam($user);
        
        if (!$team) {
            return redirect()->route('team-leader.dashboard')
                ->with('error', 'You need to create a team first');
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'files' => 'nullable|array',
            'files.*' => 'file|max:10240' // 10MB max per file
        ]);

        // Map the form data to match the database schema
        $ideaData = [
            'title' => $validated['title'],
            'description' => $validated['description'],
            'problem_statement' => null,
            'solution_approach' => null,
            'expected_impact' => null,
            'technologies' => [],
        ];

        $result = $this->teamService->submitIdea($team, $ideaData);
        
        if ($result['success'] && isset($validated['files'])) {
            // Handle file uploads
            $idea = $result['idea'];
            foreach ($validated['files'] as $file) {
                $path = $file->store('ideas/' . $idea->id, 'public');
                $idea->files()->create([
                    'filename' => $file->getClientOriginalName(),
                    'path' => $path,
                    'type' => 'document',
                    'size' => $file->getSize(),
                    'mime_type' => $file->getMimeType()
                ]);
            }
        }
        
        if ($result['success']) {
            return redirect()->route('team-leader.idea.show')
                ->with('success', 'Idea created successfully');
        } else {
            return back()->with('error', $result['message']);
        }
    }

    public function edit()
    {
        $user = auth()->user();
        $team = $this->teamService->getMyTeam($user);
        
        if (!$team) {
            return redirect()->route('team-leader.dashboard')
                ->with('error', 'You need to create a team first');
        }

        $idea = $this->teamService->getTeamIdea($team);
        
        if (!$idea) {
            return redirect()->route('team-leader.idea.create');
        }

        // Check if idea can be edited (not in review or accepted status)
        if (in_array($idea->status, ['reviewing', 'accepted', 'rejected'])) {
            return redirect()->route('team-leader.idea.show')
                ->with('error', 'Idea cannot be edited in current status');
        }

        return Inertia::render('TeamLead/Idea/Edit', [
            'idea' => $idea,
            'team' => $team
        ]);
    }

    public function update(Request $request)
    {
        $user = auth()->user();
        $team = $this->teamService->getMyTeam($user);
        
        if (!$team) {
            return redirect()->route('team-leader.dashboard')
                ->with('error', 'You need to create a team first');
        }

        $idea = $this->teamService->getTeamIdea($team);
        
        if (!$idea) {
            return redirect()->route('team-leader.idea.create');
        }

        // Check if idea can be edited
        if (in_array($idea->status, ['reviewing', 'accepted', 'rejected'])) {
            return redirect()->route('team-leader.idea.show')
                ->with('error', 'Idea cannot be edited in current status');
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'files' => 'nullable|array',
            'files.*' => 'file|max:10240' // 10MB max per file
        ]);

        // Map the form data to match the database schema
        $ideaData = [
            'title' => $validated['title'],
            'description' => $validated['description'],
            'problem_statement' => null,
            'solution_approach' => null,
            'expected_impact' => null,
            'technologies' => [],
        ];

        $updatedIdea = $this->teamService->updateIdea($idea, $ideaData);
        
        // Handle file uploads if present
        if (isset($validated['files'])) {
            foreach ($validated['files'] as $file) {
                $path = $file->store('ideas/' . $idea->id, 'public');
                $idea->files()->create([
                    'filename' => $file->getClientOriginalName(),
                    'path' => $path,
                    'type' => 'document',
                    'size' => $file->getSize(),
                    'mime_type' => $file->getMimeType()
                ]);
            }
        }
        
        return redirect()->route('team-leader.idea.show')
            ->with('success', 'Idea updated successfully');
    }

    public function submit()
    {
        $user = auth()->user();
        $team = $this->teamService->getMyTeam($user);
        
        if (!$team) {
            return back()->with('error', 'You need to create a team first');
        }

        $idea = $this->teamService->getTeamIdea($team);
        
        if (!$idea) {
            return back()->with('error', 'You need to create an idea first');
        }

        // Check if idea can be submitted
        if ($idea->status !== 'draft') {
            return back()->with('error', 'Idea has already been submitted');
        }

        // Update idea status to submitted
        $this->ideaRepository->update($idea->id, [
            'status' => 'submitted',
            'submitted_at' => now()
        ]);

        return redirect()->route('team-leader.idea.show')
            ->with('success', 'Idea submitted successfully for review');
    }

    public function withdraw()
    {
        $user = auth()->user();
        $team = $this->teamService->getMyTeam($user);
        
        if (!$team) {
            return back()->with('error', 'You need to create a team first');
        }

        $idea = $this->teamService->getTeamIdea($team);
        
        if (!$idea) {
            return back()->with('error', 'No idea found to withdraw');
        }

        // Check if idea can be withdrawn
        if (!in_array($idea->status, ['submitted', 'needs_revision'])) {
            return back()->with('error', 'Idea cannot be withdrawn in current status');
        }

        // Update idea status back to draft
        $this->ideaRepository->update($idea->id, [
            'status' => 'draft',
            'submitted_at' => null
        ]);

        return redirect()->route('team-leader.idea.edit')
            ->with('success', 'Idea withdrawn successfully. You can now edit it.');
    }

    public function uploadFile(Request $request)
    {
        $user = auth()->user();
        $team = $this->teamService->getMyTeam($user);
        
        if (!$team) {
            return response()->json(['error' => 'No team found'], 403);
        }

        $idea = $this->teamService->getTeamIdea($team);
        
        if (!$idea) {
            return response()->json(['error' => 'No idea found'], 403);
        }

        $request->validate([
            'file' => 'required|file|max:10240', // 10MB max
            'type' => 'nullable|string|in:presentation,document,image,other'
        ]);

        $file = $request->file('file');
        $type = $request->input('type', 'document');
        
        // Store file
        $path = $file->store('ideas/' . $idea->id, 'public');
        
        // Save file record in database
        $fileRecord = $idea->files()->create([
            'filename' => $file->getClientOriginalName(),
            'path' => $path,
            'type' => $type,
            'size' => $file->getSize(),
            'mime_type' => $file->getMimeType()
        ]);

        return response()->json([
            'success' => true,
            'file' => $fileRecord
        ]);
    }

    public function deleteFile($fileId)
    {
        $user = auth()->user();
        $team = $this->teamService->getMyTeam($user);
        
        if (!$team) {
            return back()->with('error', 'No team found');
        }

        $idea = $this->teamService->getTeamIdea($team);
        
        if (!$idea) {
            return back()->with('error', 'No idea found');
        }

        $file = $idea->files()->where('id', $fileId)->first();
        
        if (!$file) {
            return back()->with('error', 'File not found');
        }

        // Delete file from storage
        Storage::disk('public')->delete($file->path);
        
        // Delete database record
        $file->delete();

        return back()->with('success', 'File deleted successfully');
    }
}