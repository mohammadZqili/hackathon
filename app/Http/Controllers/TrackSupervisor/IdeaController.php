<?php

namespace App\Http\Controllers\TrackSupervisor;

use App\Http\Controllers\Controller;
use App\Services\IdeaService;
use App\Services\TrackService;
use App\Services\EditionContext;
use App\Notifications\IdeaReviewedNotification;
use App\Notifications\IdeaStatusChangedNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;
use Inertia\Inertia;
use App\Models\Idea;
use App\Models\User;

class IdeaController extends Controller
{
    protected IdeaService $ideaService;
    protected TrackService $trackService;
    protected EditionContext $editionContext;

    public function __construct(
        IdeaService $ideaService,
        TrackService $trackService,
        EditionContext $editionContext
    ) {
        $this->ideaService = $ideaService;
        $this->trackService = $trackService;
        $this->editionContext = $editionContext;
    }
    /**
     * Display a listing of ideas in supervisor's tracks and current edition.
     */
    public function index(Request $request)
    {
        $edition = $this->editionContext->current();
        $user = auth()->user();

        // Get track IDs assigned to this supervisor in current edition
        $trackIds = $user->tracksInEdition($edition->id)->pluck('tracks.id')->toArray();

        // Add filters for edition and tracks
        $filters = array_merge(
            $request->only(['search', 'status', 'review_status']),
            [
                'edition_id' => $edition->id,
                'track_id' => $trackIds  // Will filter to only these track IDs
            ]
        );

        $data = $this->ideaService->getPaginatedIdeas(
            $user,
            $filters,
            $request->get('per_page', 15)
        );

        // Get only assigned tracks for filter dropdown
        $trackData = $this->trackService->getPaginatedTracks(
            $user,
            ['edition_id' => $edition->id],
            1000
        );

        // Get track supervisors through service
        $supervisors = $this->trackService->getTrackSupervisors();

        return Inertia::render('TrackSupervisor/Ideas/Index', array_merge($data, [
            'tracks' => $trackData['tracks']->items(),
            'supervisors' => $supervisors,
            'current_edition' => $edition,
            'assigned_tracks' => $trackIds
        ]));
    }

    /**
     * Show the form for creating a new idea.
     */
    public function create()
    {
        $edition = $this->editionContext->current();
        $user = auth()->user();

        // Get all tracks assigned to this supervisor
        $assignedTracks = $user->tracksInEdition($edition->id)->get();

        if ($assignedTracks->isEmpty()) {
            abort(403, 'You are not assigned to any track in the current edition.');
        }

        // Get teams in assigned tracks
        $teams = \App\Models\Team::whereIn('track_id', $assignedTracks->pluck('id'))
            ->where('edition_id', $edition->id)
            ->get();

        return Inertia::render('TrackSupervisor/Ideas/Create', [
            'edition' => $edition,
            'tracks' => $assignedTracks,
            'teams' => $teams
        ]);
    }

    /**
     * Store a newly created idea in storage.
     */
    public function store(Request $request)
    {
        $edition = $this->editionContext->current();
        $user = auth()->user();

        // Get track IDs assigned to this supervisor
        $assignedTrackIds = $user->tracksInEdition($edition->id)->pluck('tracks.id')->toArray();

        if (empty($assignedTrackIds)) {
            abort(403, 'You are not assigned to any track in the current edition.');
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'problem_statement' => 'nullable|string',
            'proposed_solution' => 'nullable|string',
            'target_audience' => 'nullable|string',
            'expected_impact' => 'nullable|string',
            'resources_needed' => 'nullable|string',
            'track_id' => ['required', 'exists:tracks,id', function($attribute, $value, $fail) use ($assignedTrackIds) {
                if (!in_array($value, $assignedTrackIds)) {
                    $fail('You can only create ideas in your assigned tracks.');
                }
            }],
            'team_id' => 'required|exists:teams,id'
        ]);

        $result = $this->ideaService->createIdea($validated, $user);

        return redirect()->route('track-supervisor.ideas.index')
            ->with('success', 'Idea created successfully.');
    }

    /**
     * Display the specified idea.
     */
    public function show(Idea $idea)
    {
        // Check policy
        $this->authorize('view', $idea);

        $data = $this->ideaService->getIdeaDetails($idea->id, auth()->user());

        if (!$data) {
            abort(404, 'Idea not found or access denied.');
        }

        // Get review history if exists
        $reviewHistory = [];
        if (method_exists($idea, 'auditLogs')) {
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
        }

        // Get scoring breakdown if available
        $scoring = null;
        if ($data['idea']->evaluation_scores) {
            $scoring = $data['idea']->evaluation_scores;
            $scoring['total_score'] = $data['idea']->score;
        }

        return Inertia::render('TrackSupervisor/Ideas/Show', array_merge($data, [
            'reviewHistory' => $reviewHistory,
            'scoring' => $scoring
        ]));
    }

    /**
     * Show the form for editing the specified idea.
     */
    public function edit(Idea $idea)
    {
        // Check policy
        $this->authorize('update', $idea);

        $edition = $this->editionContext->current();
        $user = auth()->user();

        $data = $this->ideaService->getIdeaDetails($idea->id, $user);

        // Get all tracks assigned to this supervisor
        $assignedTracks = $user->tracksInEdition($edition->id)->get();

        // Get teams in assigned tracks
        $teams = \App\Models\Team::whereIn('track_id', $assignedTracks->pluck('id'))
            ->where('edition_id', $edition->id)
            ->get();

        return Inertia::render('TrackSupervisor/Ideas/Edit', array_merge($data, [
            'edition' => $edition,
            'tracks' => $assignedTracks,
            'teams' => $teams
        ]));
    }

    /**
     * Update the specified idea in storage.
     */
    public function update(Request $request, Idea $idea)
    {
        // Check policy
        $this->authorize('update', $idea);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'problem_statement' => 'nullable|string',
            'proposed_solution' => 'nullable|string',
            'target_audience' => 'nullable|string',
            'expected_impact' => 'nullable|string',
            'resources_needed' => 'nullable|string',
            'track_id' => 'required|exists:tracks,id',
            'team_id' => 'required|exists:teams,id'
        ]);

        $result = $this->ideaService->updateIdea($idea->id, $validated, auth()->user());

        return redirect()->route('track-supervisor.ideas.show', $idea)
            ->with('success', 'Idea updated successfully.');
    }

    /**
     * Show the review page for an idea
     */
    public function review(Idea $idea)
    {
        // Check policy - can this supervisor review this idea?
        $this->authorize('review', $idea);

        $idea->load(['team', 'track', 'files']);

        $supervisors = User::role('track_supervisor')->get();

        $scoringCriteria = [
            'innovation' => 'Innovation and Creativity (0-25)',
            'feasibility' => 'Technical Feasibility (0-25)',
            'impact' => 'Potential Impact (0-25)',
            'presentation' => 'Presentation Quality (0-25)',
        ];

        return Inertia::render('TrackSupervisor/Ideas/Review', [
            'idea' => $idea,
            'supervisors' => $supervisors,
            'scoringCriteria' => $scoringCriteria,
        ]);
    }

    /**
     * Process idea review (Track supervisors can review ideas in their tracks)
     */
    public function processReview(Request $request, Idea $idea)
    {
        // Check policy
        $this->authorize('review', $idea);

        $validated = $request->validate([
            'status' => 'required|in:draft,submitted,under_review,needs_revision,accepted,rejected,pending_review,in_progress,completed',
            'reviewed_by' => 'nullable|exists:users,id',
            'feedback' => 'nullable|string',
            'scores' => 'nullable|array',
            'scores.innovation' => 'nullable|numeric|min:0|max:25',
            'scores.feasibility' => 'nullable|numeric|min:0|max:25',
            'scores.impact' => 'nullable|numeric|min:0|max:25',
            'scores.presentation' => 'nullable|numeric|min:0|max:25',
            'notify_team' => 'boolean',
        ]);

        try {
            // Store old status for notification
            $oldStatus = $idea->status;

            $result = $this->ideaService->processReview($idea->id, $validated, auth()->user());

            // Send notifications to team members
            if ($idea->team) {
                $teamMembers = $idea->team->members()->get();
                $feedback = $validated['feedback'] ?? null;
                $newStatus = $validated['status'];

                foreach ($teamMembers as $member) {
                    // Send review notification
                    $member->notify(new IdeaReviewedNotification($idea, auth()->user(), $newStatus, $feedback));

                    // Send status change notification if status changed
                    if ($oldStatus !== $newStatus) {
                        $member->notify(new IdeaStatusChangedNotification($idea, $oldStatus, $newStatus, auth()->user()));
                    }
                }

                // Also notify the team leader specifically if not already included
                if ($idea->team->leader && !$teamMembers->contains('id', $idea->team->leader->id)) {
                    $idea->team->leader->notify(new IdeaReviewedNotification($idea, auth()->user(), $newStatus, $feedback));
                    if ($oldStatus !== $newStatus) {
                        $idea->team->leader->notify(new IdeaStatusChangedNotification($idea, $oldStatus, $newStatus, auth()->user()));
                    }
                }
            }

            return redirect()->route('track-supervisor.ideas.show', $idea->id)
                ->with('success', $result['message'] . ' Team has been notified.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    /**
     * Accept an idea
     */
    public function accept(Request $request, Idea $idea)
    {
        $request->validate([
            'feedback' => 'nullable|string|max:2000',
            'score' => 'nullable|numeric|min:0|max:100'
        ]);

        // Store old status
        $oldStatus = $idea->status;

        $idea->accept(
            auth()->user(),
            $request->feedback,
            $request->score
        );

        // Notify team members about acceptance
        if ($idea->team) {
            $teamMembers = $idea->team->members()->get();
            foreach ($teamMembers as $member) {
                $member->notify(new IdeaReviewedNotification($idea, auth()->user(), 'accepted', $request->feedback));
                if ($oldStatus !== 'accepted') {
                    $member->notify(new IdeaStatusChangedNotification($idea, $oldStatus, 'accepted', auth()->user()));
                }
            }
        }

        return redirect()->back()->with('success', 'Idea accepted successfully. Team notified.');
    }

    /**
     * Reject an idea
     */
    public function reject(Request $request, Idea $idea)
    {
        $request->validate([
            'feedback' => 'required|string|max:2000',
            'score' => 'nullable|numeric|min:0|max:100'
        ]);

        // Store old status
        $oldStatus = $idea->status;

        $idea->reject(
            auth()->user(),
            $request->feedback,
            $request->score
        );

        // Notify team members about rejection
        if ($idea->team) {
            $teamMembers = $idea->team->members()->get();
            foreach ($teamMembers as $member) {
                $member->notify(new IdeaReviewedNotification($idea, auth()->user(), 'rejected', $request->feedback));
                if ($oldStatus !== 'rejected') {
                    $member->notify(new IdeaStatusChangedNotification($idea, $oldStatus, 'rejected', auth()->user()));
                }
            }
        }

        return redirect()->back()->with('success', 'Idea rejected. Team notified.');
    }

    /**
     * Request edits for an idea
     */
    public function needEdit(Request $request, Idea $idea)
    {
        $request->validate([
            'feedback' => 'required|string|max:2000'
        ]);

        // Store old status
        $oldStatus = $idea->status;

        $idea->requestRevision(
            auth()->user(),
            $request->feedback
        );

        // Notify team members about revision request
        if ($idea->team) {
            $teamMembers = $idea->team->members()->get();
            foreach ($teamMembers as $member) {
                $member->notify(new IdeaReviewedNotification($idea, auth()->user(), 'needs_revision', $request->feedback));
                if ($oldStatus !== 'needs_revision') {
                    $member->notify(new IdeaStatusChangedNotification($idea, $oldStatus, 'needs_revision', auth()->user()));
                }
            }
        }

        return redirect()->back()->with('success', 'Idea marked as needing revisions. Team notified.');
    }

    /**
     * Add comment to an idea
     */
    public function addComment(Request $request, Idea $idea)
    {
        $request->validate([
            'comment' => 'required|string|max:2000',
            'parent_id' => 'nullable|exists:idea_comments,id'
        ]);

        try {
            $comment = $this->ideaService->addComment(
                $idea->id,
                auth()->user()->id,
                $request->comment,
                $request->parent_id
            );

            return back()->with('success', 'Comment added successfully.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    /**
     * Assign supervisor to an idea
     */
    public function assignSupervisor(Request $request, Idea $idea)
    {
        $request->validate([
            'supervisor_id' => 'required|exists:users,id'
        ]);

        $idea->reviewed_by = $request->supervisor_id;
        $idea->save();

        $idea->logAction(
            'supervisor_assigned',
            'reviewed_by',
            $request->supervisor_id,
            'Supervisor assigned by System Admin',
            auth()->user()
        );

        return redirect()->back()->with('success', 'Supervisor assigned successfully.');
    }

    /**
     * Update score for an idea
     */
    public function updateScore(Request $request, Idea $idea)
    {
        $request->validate([
            'score' => 'required|numeric|min:0|max:100'
        ]);

        $idea->score = $request->score;
        $idea->save();

        $idea->logAction(
            'score_updated',
            'score',
            $request->score,
            'Score updated by System Admin',
            auth()->user()
        );

        return redirect()->back()->with('success', 'Score updated successfully.');
    }

    /**
     * Download idea file
     */
    public function downloadFile(Idea $idea, $fileId)
    {
        $file = $idea->files()->findOrFail($fileId);

        $filePath = storage_path('app/public/' . $file->path);

        if (!file_exists($filePath)) {
            return redirect()->back()->with('error', 'File not found.');
        }

        return response()->download($filePath, $file->filename);
    }

    /**
     * Delete an idea (Track supervisors cannot delete)
     */
    public function destroy(Idea $idea)
    {
        // Track supervisors cannot delete ideas
        abort(403, 'Track supervisors are not authorized to delete ideas.');
    }

    /**
     * Export ideas data
     */
    public function export(Request $request)
    {
        try {
            $result = $this->ideaService->exportIdeas(
                auth()->user(),
                $request->only(['search', 'status', 'track_id', 'edition_id'])
            );

            $headers = [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => 'attachment; filename="' . $result['filename'] . '"',
            ];

            $callback = function() use ($result) {
                $file = fopen('php://output', 'w');
                foreach ($result['data'] as $row) {
                    fputcsv($file, $row);
                }
                fclose($file);
            };

            return response()->stream($callback, 200, $headers);
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    /**
     * Get idea statistics
     */
    public function statistics()
    {
        $stats = $this->ideaService->getStatistics();

        return response()->json($stats);
    }
}
