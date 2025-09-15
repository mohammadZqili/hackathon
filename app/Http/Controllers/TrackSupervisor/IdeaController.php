<?php

namespace App\Http\Controllers\TrackSupervisor;

use App\Http\Controllers\Controller;
use App\Services\IdeaService;
use App\Services\TrackService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Idea;
use App\Models\User;

class IdeaController extends Controller
{
    protected IdeaService $ideaService;
    protected TrackService $trackService;

    public function __construct(IdeaService $ideaService, TrackService $trackService)
    {
        $this->ideaService = $ideaService;
        $this->trackService = $trackService;
    }
    /**
     * Display a listing of all ideas across all editions.
     */
    public function index(Request $request)
    {
        $data = $this->ideaService->getPaginatedIdeas(
            auth()->user(),
            $request->only(['search', 'status', 'track_id', 'edition_id', 'review_status']),
            $request->get('per_page', 15)
        );

        // Get tracks for filter dropdown
        $trackData = $this->trackService->getPaginatedTracks(
            auth()->user(),
            [],
            1000 // Get all tracks
        );

        // Get track supervisors through service
        $supervisors = $this->trackService->getTrackSupervisors();

        return Inertia::render('TrackSupervisor/Ideas/Index', array_merge($data, [
            'tracks' => $trackData['tracks']->items(),
            'supervisors' => $supervisors
        ]));
    }

    /**
     * Show the form for creating a new idea.
     */
    public function create()
    {
        $trackData = $this->trackService->getPaginatedTracks(
            auth()->user(),
            [],
            1000 // Get all tracks
        );

        return Inertia::render('TrackSupervisor/Ideas/Create', [
            'tracks' => $trackData['tracks']->items()
        ]);
    }

    /**
     * Display the specified idea.
     */
    public function show(Idea $idea)
    {
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
        $idea->load(['team', 'track', 'edition', 'files']);

        // Get tracks for dropdown
        $trackData = $this->trackService->getPaginatedTracks(
            auth()->user(),
            [],
            1000 // Get all tracks
        );

        return Inertia::render('TrackSupervisor/Ideas/Edit', [
            'idea' => $idea,
            'tracks' => $trackData['tracks']->items(),
        ]);
    }

    /**
     * Update the specified idea in storage.
     */
    public function update(Request $request, Idea $idea)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'problem_statement' => 'required|string',
            'solution_approach' => 'required|string',
            'expected_impact' => 'nullable|string',
            'track_id' => 'required|exists:tracks,id',
            'status' => 'required|in:draft,submitted,under_review,needs_revision,accepted,rejected',
            'technologies' => 'nullable|array',
        ]);

        try {
            // Handle technologies field
            if (isset($validated['technologies']) && is_array($validated['technologies'])) {
                $validated['technologies'] = json_encode($validated['technologies']);
            }

            // Update the idea directly as System Admin
            $idea->update($validated);

            // Log the action
            if (method_exists($idea, 'logAction')) {
                $idea->logAction(
                    'idea_updated',
                    'idea',
                    $idea->id,
                    'Idea updated by System Admin',
                    auth()->user()
                );
            }

            return redirect()->route('system-admin.ideas.show', $idea->id)
                ->with('success', 'Idea updated successfully.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    /**
     * Show the review page for an idea
     */
    public function review(Idea $idea)
    {
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
     * Process idea review (System Admin can review any idea)
     */
    public function processReview(Request $request, Idea $idea)
    {
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
            $result = $this->ideaService->processReview($idea->id, $validated, auth()->user());

            // Send notification to team if requested
            if ($request->boolean('notify_team')) {
                // TODO: Trigger notification service here
            }

            return redirect()->route('system-admin.ideas.show', $idea->id)
                ->with('success', $result['message']);
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

        $idea->accept(
            auth()->user(),
            $request->feedback,
            $request->score
        );

        return redirect()->back()->with('success', 'Idea accepted successfully.');
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

        $idea->reject(
            auth()->user(),
            $request->feedback,
            $request->score
        );

        return redirect()->back()->with('success', 'Idea rejected.');
    }

    /**
     * Request edits for an idea
     */
    public function needEdit(Request $request, Idea $idea)
    {
        $request->validate([
            'feedback' => 'required|string|max:2000'
        ]);

        $idea->requestRevision(
            auth()->user(),
            $request->feedback
        );

        return redirect()->back()->with('success', 'Idea marked as needing revisions.');
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
     * Delete an idea (System Admin only)
     */
    public function destroy(Idea $idea)
    {
        try {
            $result = $this->ideaService->deleteIdea($idea->id, auth()->user());
            return redirect()->route('system-admin.ideas.index')
                ->with('success', $result['message']);
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
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
