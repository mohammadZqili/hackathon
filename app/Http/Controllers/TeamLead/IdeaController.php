<?php

namespace App\Http\Controllers\TeamLead;

use App\Http\Controllers\Controller;
use App\Services\TeamService;
use App\Services\IdeaService;
use App\Services\EditionContext;
use App\Repositories\IdeaRepository;
use App\Repositories\TrackRepository;
use App\Notifications\IdeaSubmittedNotification;
use App\Notifications\IdeaStatusChangedNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Notification;
use Inertia\Inertia;

class IdeaController extends Controller
{
    protected $teamService;
    protected $ideaService;
    protected $ideaRepository;
    protected $trackRepository;
    protected $editionContext;

    public function __construct(
        TeamService $teamService,
        IdeaService $ideaService,
        IdeaRepository $ideaRepository,
        TrackRepository $trackRepository,
        EditionContext $editionContext
    ) {
        $this->teamService = $teamService;
        $this->ideaService = $ideaService;
        $this->ideaRepository = $ideaRepository;
        $this->trackRepository = $trackRepository;
        $this->editionContext = $editionContext;
    }

    public function index()
    {
        $user = auth()->user();
        $team = $this->teamService->getMyTeam($user);

        if (!$team) {
            return redirect()->route('team-lead.idea.create')
                ->with('info', 'Please create an idea first. A team will be created automatically.');
        }

        $idea = $this->teamService->getTeamIdea($team);

        if (!$idea) {
            return redirect()->route('team-lead.idea.create');
        }

        // Load idea with comments and other relationships
        $idea->load(['comments.user', 'files', 'track', 'team.members']);

        return Inertia::render('TeamLead/Idea/Edit', [
            'idea' => $idea,
            'team' => $team,
            'comments' => $idea->comments
        ]);
    }

    public function show()
    {
        $user = auth()->user();
        $team = $this->teamService->getMyTeam($user);

        if (!$team) {
            return redirect()->route('team-lead.idea.create')
                ->with('info', 'Please create an idea first. A team will be created automatically.');
        }

        $idea = $this->teamService->getTeamIdea($team);

        if (!$idea) {
            return redirect()->route('team-lead.idea.create')
                ->with('info', 'Please create an idea for your team.');
        }

        // Load idea with relationships
        $idea->load(['files', 'track', 'team.members']);

        // Get separated comments
        $teamComments = $this->ideaService->getTeamComments($idea->id);
        $supervisorFeedback = $this->ideaService->getSupervisorFeedback($idea->id);

        return Inertia::render('TeamLead/Idea/Show', [
            'idea' => $idea,
            'team' => $team,
            'teamComments' => $teamComments,
            'supervisorFeedback' => $supervisorFeedback
        ]);
    }

    public function create()
    {
        $user = auth()->user();
        $team = $this->teamService->getMyTeam($user);

        // If no team exists, redirect to team creation
        if (!$team) {
            return redirect()->route('team-lead.team.create')
                ->with('info', 'Please create a team first before submitting an idea.');
        }

        // Get available tracks for team leaders
        $edition = $this->editionContext->current();
        $tracks = $this->trackRepository->getActive(['edition_id' => $edition->id]);

        // Debug log tracks
        \Log::info('Available tracks for idea creation:', ['tracks' => $tracks->toArray()]);

        // If team exists and already has an idea, redirect to show
        $idea = $this->teamService->getTeamIdea($team);
        if ($idea) {
            return redirect()->route('team-lead.idea.show')
                ->with('info', 'You already have an idea created.');
        }

        // Load team with track relationship
        $team->load('track');

        return Inertia::render('TeamLead/Idea/Create', [
            'team' => $team,
            'tracks' => $tracks,
            'requiresTeamCreation' => false
        ]);
    }

    public function store(Request $request)
    {
        $user = auth()->user();
        $team = $this->teamService->getMyTeam($user);

        // Require team to exist before submitting idea
        if (!$team) {
            return redirect()->route('team-lead.team.create')
                ->with('error', 'You must create a team first before submitting an idea.');
        }

        // Debug log to see what's received
        \Log::info('Idea submission request data:', $request->all());

        // Convert empty string to null for track_id
        if ($request->has('track_id') && $request->track_id === '') {
            $request->merge(['track_id' => null]);
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'track_id' => 'required|exists:tracks,id',
            'files' => 'nullable|array',
            'files.*' => 'file|max:10240' // 10MB max per file
        ]);

        // Map the form data to match the database schema
        // Extra check to ensure track_id is not null
        if (!isset($validated['track_id']) || !$validated['track_id']) {
            return back()->withErrors(['track_id' => 'Please select a track for your idea.'])->withInput();
        }

        $ideaData = [
            'title' => $validated['title'],
            'description' => $validated['description'],
            'track_id' => (int)$validated['track_id'], // Ensure it's an integer
            'problem_statement' => null,
            'solution_approach' => null,
            'expected_impact' => null,
            'technologies' => [],
            'status' => 'draft' // Start as draft, user can submit later
        ];

        $result = $this->teamService->submitIdea($team, $ideaData);


        if ($result['success']) {
            $idea = $result['idea'];

            // Handle file uploads if present
            if (isset($validated['files'])) {
                foreach ($validated['files'] as $file) {
                    $path = $file->store('ideas/' . $idea->id, 'public');
                    $idea->files()->create([
                        'original_name' => $file->getClientOriginalName(),
                        'file_name' => basename($path),
                        'file_path' => $path,
                        'file_type' => $file->getExtension() ?? 'unknown',
                        'mime_type' => $file->getMimeType(),
                        'file_size' => $file->getSize(),
                        'file_category' => 'document',
                        'uploaded_by' => $user->id
                    ]);
                }
            }

            // Notify all team members about the new idea (if there are any besides the leader)
            $teamMembers = $team->members()->where('users.id', '!=', $user->id)->get();
            foreach ($teamMembers as $member) {
                $member->notify(new IdeaSubmittedNotification($idea, $user));
            }

            // Load the track with supervisors
            $team->load('track.supervisors');

            // Notify track supervisors about the new idea
            if ($team->track && $team->track->supervisors) {
                foreach ($team->track->supervisors as $supervisor) {
                    $supervisor->notify(new IdeaSubmittedNotification($idea, $user));
                }
            }

            return redirect()->route('team-lead.idea.show')
                ->with('success', 'Idea created successfully! Track supervisors have been notified.');
        } else {
            return back()->with('error', $result['message']);
        }
    }

    public function edit()
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

        // Check if idea can be edited (not in review or accepted status)
        if (in_array($idea->status, ['reviewing', 'accepted', 'rejected'])) {
            return redirect()->route('team-lead.idea.show')
                ->with('error', 'Idea cannot be edited in current status');
        }

        // Load idea with comments and other relationships
        $idea->load(['comments.user', 'files', 'track', 'team.members']);

        // Load team with track relationship
        $team->load('track');

        return Inertia::render('TeamLead/Idea/Edit', [
            'idea' => $idea,
            'team' => $team,
            'comments' => $idea->comments
        ]);
    }

    public function update(Request $request)
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

        // Check if idea can be edited
        if (in_array($idea->status, ['reviewing', 'accepted', 'rejected'])) {
            return redirect()->route('team-lead.idea.show')
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
                    'original_name' => $file->getClientOriginalName(),
                    'file_name' => basename($path),
                    'file_path' => $path,
                    'file_type' => $file->getExtension() ?? 'unknown',
                    'mime_type' => $file->getMimeType(),
                    'file_size' => $file->getSize(),
                    'file_category' => 'document',
                    'uploaded_by' => $user->id
                ]);
            }
        }

        return redirect()->route('team-lead.idea.show')
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

        // Store old status for notification
        $oldStatus = $idea->status;

        // Update idea status to submitted
        $this->ideaRepository->update($idea->id, [
            'status' => 'submitted',
            'submitted_at' => now()
        ]);

        // Notify all team members about the status change (except the submitter)
        $teamMembers = $team->members()->where('users.id', '!=', $user->id)->get();
        foreach ($teamMembers as $member) {
            $member->notify(new IdeaStatusChangedNotification($idea, $oldStatus, 'submitted', $user));
        }

        // Load the track with supervisors
        $team->load('track.supervisors');

        // Notify track supervisors about the idea submission
        if ($team->track && $team->track->supervisors) {
            foreach ($team->track->supervisors as $supervisor) {
                // Send both submission and status change notifications
                $supervisor->notify(new IdeaSubmittedNotification($idea, $user));
                $supervisor->notify(new IdeaStatusChangedNotification($idea, $oldStatus, 'submitted', $user));
            }
        }

        return redirect()->route('team-lead.idea.show')
            ->with('success', 'Idea submitted successfully for review! Track supervisors have been notified.');
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

        // Store old status for notification
        $oldStatus = $idea->status;

        // Update idea status back to draft
        $this->ideaRepository->update($idea->id, [
            'status' => 'draft',
            'submitted_at' => null
        ]);

        // Notify all team members about the withdrawal
        $teamMembers = $team->members()->get();
        foreach ($teamMembers as $member) {
            if ($member->id !== $user->id) {
                $member->notify(new IdeaStatusChangedNotification($idea, $oldStatus, 'draft', $user));
            }
        }

        return redirect()->route('team-lead.idea.edit-my')
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
            'original_name' => $file->getClientOriginalName(),
            'file_name' => basename($path),
            'file_path' => $path,
            'file_type' => $file->getExtension() ?? 'unknown',
            'mime_type' => $file->getMimeType(),
            'file_size' => $file->getSize(),
            'file_category' => $type ?? 'document',
            'uploaded_by' => $user->id
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
        Storage::disk('public')->delete($file->file_path);

        // Delete database record
        $file->delete();

        return back()->with('success', 'File deleted successfully');
    }

    public function addComment(Request $request, $id)
    {
        $user = auth()->user();
        $team = $this->teamService->getMyTeam($user);

        if (!$team) {
            return back()->with('error', 'You need to create a team first');
        }

        $idea = $this->teamService->getTeamIdea($team);

        if (!$idea || $idea->id != $id) {
            return back()->with('error', 'Invalid idea');
        }

        $request->validate([
            'comment' => 'required|string|max:2000',
            'parent_id' => 'nullable|exists:idea_comments,id'
        ]);

        try {
            $comment = $this->ideaService->addComment(
                $idea->id,
                $user->id,
                $request->comment,
                $request->parent_id
            );

            return back()->with('success', 'Comment added successfully.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function downloadFile($ideaId, $fileId)
    {
        $user = auth()->user();
        $team = $this->teamService->getMyTeam($user);

        if (!$team) {
            return redirect()->route('team-lead.idea.index')
                ->with('error', 'You don\'t have access to this idea.');
        }

        $idea = $this->teamService->getTeamIdea($team);

        if (!$idea || $idea->id != $ideaId) {
            return redirect()->route('team-lead.idea.index')
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
