<?php

namespace App\Http\Controllers\TeamLeader;

use App\Http\Controllers\Controller;
use App\Models\Idea;
use App\Models\Team;
use App\Models\Track;
use App\Models\Hackathon;
use App\Models\IdeaFile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

class IdeaController extends Controller
{
    /**
     * Display the team's idea.
     */
    public function show(): Response
    {
        $team = auth()->user()->leadingTeam()->with(['idea.track', 'idea.files'])->first();
        
        if (!$team) {
            return redirect()->route('team-leader.dashboard')
                ->with('error', 'You need to create a team first.');
        }

        if (!$team->idea) {
            return redirect()->route('team-leader.idea.create');
        }

        $idea = $team->idea;
        $idea->load(['track', 'files', 'reviewer']);

        // Get review history
        $reviewHistory = $idea->auditLogs()
            ->where('action', 'status_changed')
            ->with('user')
            ->latest()
            ->get()
            ->map(function ($log) {
                return [
                    'id' => $log->id,
                    'status' => $log->new_value,
                    'feedback' => $log->notes,
                    'reviewer_name' => $log->user?->name ?? 'System',
                    'created_at' => $log->created_at,
                ];
            });

        return Inertia::render('TeamLeader/Idea/Show', [
            'idea' => $idea,
            'team' => $team,
            'reviewHistory' => $reviewHistory,
            'canEdit' => $idea->canBeEdited(),
            'canSubmit' => $idea->canBeSubmitted(),
        ]);
    }

    /**
     * Show the form for creating a new idea.
     */
    public function create(): Response
    {
        $team = auth()->user()->leadingTeam()->first();
        
        if (!$team) {
            return redirect()->route('team-leader.dashboard')
                ->with('error', 'You need to create a team first.');
        }

        if ($team->idea) {
            return redirect()->route('team-leader.idea.show')
                ->with('info', 'Your team already has an idea.');
        }

        // Get current hackathon and its tracks
        $currentHackathon = Hackathon::where('is_current', true)->first();
        
        if (!$currentHackathon) {
            return redirect()->route('team-leader.dashboard')
                ->with('error', 'No active hackathon found.');
        }

        if (!$currentHackathon->isIdeaSubmissionOpen()) {
            return redirect()->route('team-leader.dashboard')
                ->with('error', 'Idea submission period has ended.');
        }

        $tracks = Track::where('hackathon_id', $currentHackathon->id)
            ->where('is_active', true)
            ->get();

        return Inertia::render('TeamLeader/Idea/Create', [
            'team' => $team,
            'tracks' => $tracks,
            'maxFileSize' => 15 * 1024 * 1024, // 15MB in bytes
            'maxFiles' => 8,
            'allowedFileTypes' => ['pdf', 'ppt', 'pptx', 'doc', 'docx', 'xls', 'xlsx'],
        ]);
    }

    /**
     * Store a newly created idea.
     */
    public function store(Request $request)
    {
        $team = auth()->user()->leadingTeam()->first();
        
        if (!$team) {
            return redirect()->route('team-leader.dashboard')
                ->with('error', 'You need to create a team first.');
        }

        if ($team->idea) {
            return redirect()->route('team-leader.idea.show')
                ->with('error', 'Your team already has an idea.');
        }

        $validated = $request->validate([
            'track_id' => 'required|exists:tracks,id',
            'title' => 'required|string|max:255',
            'description' => 'required|string|min:100|max:5000',
            'problem_statement' => 'required|string|min:50|max:2000',
            'solution_approach' => 'required|string|min:50|max:3000',
            'expected_impact' => 'required|string|min:50|max:2000',
            'technologies' => 'nullable|array',
            'technologies.*' => 'string|max:100',
            'files' => 'nullable|array|max:8',
            'files.*' => 'file|mimes:pdf,ppt,pptx,doc,docx,xls,xlsx|max:15360', // 15MB
        ]);

        // Create the idea
        $idea = $team->idea()->create([
            'track_id' => $validated['track_id'],
            'title' => $validated['title'],
            'description' => $validated['description'],
            'problem_statement' => $validated['problem_statement'],
            'solution_approach' => $validated['solution_approach'],
            'expected_impact' => $validated['expected_impact'],
            'technologies' => $validated['technologies'] ?? [],
            'status' => 'draft',
        ]);

        // Handle file uploads
        if ($request->hasFile('files')) {
            foreach ($request->file('files') as $file) {
                $path = $file->store('ideas/' . $idea->id, 'public');
                
                $idea->files()->create([
                    'filename' => $file->getClientOriginalName(),
                    'path' => $path,
                    'mime_type' => $file->getMimeType(),
                    'size' => $file->getSize(),
                    'uploaded_by' => auth()->id(),
                ]);
            }
        }

        // Log the creation
        $idea->logAction('created', null, null, 'Idea created');

        return redirect()->route('team-leader.idea.show')
            ->with('success', 'Idea created successfully. You can now submit it for review.');
    }

    /**
     * Show the form for editing the idea.
     */
    public function edit(): Response
    {
        $team = auth()->user()->leadingTeam()->with(['idea.track', 'idea.files'])->first();
        
        if (!$team || !$team->idea) {
            return redirect()->route('team-leader.dashboard')
                ->with('error', 'No idea found.');
        }

        $idea = $team->idea;

        if (!$idea->canBeEdited()) {
            return redirect()->route('team-leader.idea.show')
                ->with('error', 'This idea cannot be edited in its current status.');
        }

        // Get current hackathon and its tracks
        $currentHackathon = Hackathon::where('is_current', true)->first();
        $tracks = Track::where('hackathon_id', $currentHackathon->id)
            ->where('is_active', true)
            ->get();

        return Inertia::render('TeamLeader/Idea/Edit', [
            'idea' => $idea,
            'team' => $team,
            'tracks' => $tracks,
            'maxFileSize' => 15 * 1024 * 1024, // 15MB in bytes
            'maxFiles' => 8,
            'allowedFileTypes' => ['pdf', 'ppt', 'pptx', 'doc', 'docx', 'xls', 'xlsx'],
            'existingFiles' => $idea->files,
        ]);
    }

    /**
     * Update the idea.
     */
    public function update(Request $request)
    {
        $team = auth()->user()->leadingTeam()->with('idea')->first();
        
        if (!$team || !$team->idea) {
            return redirect()->route('team-leader.dashboard')
                ->with('error', 'No idea found.');
        }

        $idea = $team->idea;

        if (!$idea->canBeEdited()) {
            return redirect()->route('team-leader.idea.show')
                ->with('error', 'This idea cannot be edited in its current status.');
        }

        $validated = $request->validate([
            'track_id' => 'required|exists:tracks,id',
            'title' => 'required|string|max:255',
            'description' => 'required|string|min:100|max:5000',
            'problem_statement' => 'required|string|min:50|max:2000',
            'solution_approach' => 'required|string|min:50|max:3000',
            'expected_impact' => 'required|string|min:50|max:2000',
            'technologies' => 'nullable|array',
            'technologies.*' => 'string|max:100',
        ]);

        // Update the idea
        $idea->update($validated);

        // If the idea was in needs_revision status, change it back to draft
        if ($idea->status === 'needs_revision') {
            $idea->update(['status' => 'draft']);
            $idea->logAction('status_changed', 'status', 'draft', 'Idea revised and ready for resubmission');
        } else {
            $idea->logAction('updated', null, null, 'Idea details updated');
        }

        return redirect()->route('team-leader.idea.show')
            ->with('success', 'Idea updated successfully.');
    }

    /**
     * Submit the idea for review.
     */
    public function submit(Request $request)
    {
        $team = auth()->user()->leadingTeam()->with('idea')->first();
        
        if (!$team || !$team->idea) {
            return redirect()->route('team-leader.dashboard')
                ->with('error', 'No idea found.');
        }

        $idea = $team->idea;

        if (!$idea->canBeSubmitted()) {
            return redirect()->route('team-leader.idea.show')
                ->with('error', 'This idea cannot be submitted. Please ensure all required fields are filled and submission period is open.');
        }

        $idea->submit();

        // Send notification email to team leader
        // TODO: Implement email notification

        return redirect()->route('team-leader.idea.show')
            ->with('success', 'Idea submitted successfully for review.');
    }

    /**
     * Withdraw the submitted idea.
     */
    public function withdraw(Request $request)
    {
        $team = auth()->user()->leadingTeam()->with('idea')->first();
        
        if (!$team || !$team->idea) {
            return redirect()->route('team-leader.dashboard')
                ->with('error', 'No idea found.');
        }

        $idea = $team->idea;

        if (!in_array($idea->status, ['submitted', 'under_review'])) {
            return redirect()->route('team-leader.idea.show')
                ->with('error', 'Only submitted ideas can be withdrawn.');
        }

        $idea->update([
            'status' => 'draft',
            'submitted_at' => null,
        ]);

        $idea->logAction('withdrawn', 'status', 'draft', 'Idea withdrawn from review');

        return redirect()->route('team-leader.idea.show')
            ->with('success', 'Idea withdrawn successfully. You can edit and resubmit it.');
    }

    /**
     * Upload a file for the idea.
     */
    public function uploadFile(Request $request)
    {
        $team = auth()->user()->leadingTeam()->with('idea')->first();
        
        if (!$team || !$team->idea) {
            return response()->json(['error' => 'No idea found.'], 404);
        }

        $idea = $team->idea;

        if (!$idea->canBeEdited()) {
            return response()->json(['error' => 'Files cannot be uploaded in current status.'], 403);
        }

        $request->validate([
            'file' => 'required|file|mimes:pdf,ppt,pptx,doc,docx,xls,xlsx|max:15360', // 15MB
        ]);

        // Check if maximum files reached
        if ($idea->files()->count() >= 8) {
            return response()->json(['error' => 'Maximum number of files (8) reached.'], 422);
        }

        $file = $request->file('file');
        $path = $file->store('ideas/' . $idea->id, 'public');
        
        $ideaFile = $idea->files()->create([
            'filename' => $file->getClientOriginalName(),
            'path' => $path,
            'mime_type' => $file->getMimeType(),
            'size' => $file->getSize(),
            'uploaded_by' => auth()->id(),
        ]);

        $idea->logAction('file_uploaded', null, $file->getClientOriginalName(), 'File uploaded');

        return response()->json([
            'success' => true,
            'file' => $ideaFile,
        ]);
    }

    /**
     * Delete a file from the idea.
     */
    public function deleteFile(IdeaFile $file)
    {
        $team = auth()->user()->leadingTeam()->with('idea')->first();
        
        if (!$team || !$team->idea) {
            return response()->json(['error' => 'No idea found.'], 404);
        }

        $idea = $team->idea;

        // Check if the file belongs to this idea
        if ($file->idea_id !== $idea->id) {
            return response()->json(['error' => 'File not found.'], 404);
        }

        if (!$idea->canBeEdited()) {
            return response()->json(['error' => 'Files cannot be deleted in current status.'], 403);
        }

        // Delete the file from storage
        Storage::disk('public')->delete($file->path);
        
        // Delete the database record
        $filename = $file->filename;
        $file->delete();

        $idea->logAction('file_deleted', null, $filename, 'File deleted');

        return response()->json([
            'success' => true,
            'message' => 'File deleted successfully.',
        ]);
    }
}
