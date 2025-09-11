<?php

namespace App\Http\Controllers\HackathonAdmin1;

use App\Models\Idea;
use App\Models\HackathonEdition;
use App\Models\Hackathon;
use App\Models\User;
use App\Models\Track;
use App\Http\Requests\HackathonAdmin\ReviewIdeaRequest;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class IdeaController extends BaseController
{
    /**
     * Display a listing of ideas for current hackathon edition.
     */
    public function index(Request $request): Response
    {
        if (!$this->currentEdition) {
            return Inertia::render('HackathonAdmin/NoEdition');
        }

        $query = Idea::where('edition_id', $this->currentEdition->id)
        ->with(['team', 'track', 'supervisor']);

        // Apply filters
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('track_id')) {
            $query->where('track_id', $request->track_id);
        }

        if ($request->filled('has_supervisor')) {
            if ($request->has_supervisor === 'yes') {
                $query->whereNotNull('reviewed_by');
            } else {
                $query->whereNull('reviewed_by');
            }
        }

        $ideas = $query->latest()->paginate(15)->withQueryString();

        // Get statistics
        $baseQuery = Idea::where('edition_id', $this->currentEdition->id);
        $statistics = [
            'total' => $baseQuery->count(),
            'draft' => (clone $baseQuery)->where('status', 'draft')->count(),
            'submitted' => (clone $baseQuery)->where('status', 'submitted')->count(),
            'under_review' => (clone $baseQuery)->where('status', 'under_review')->count(),
            'accepted' => (clone $baseQuery)->where('status', 'accepted')->count(),
            'rejected' => (clone $baseQuery)->where('status', 'rejected')->count(),
            'needs_revision' => (clone $baseQuery)->where('status', 'needs_revision')->count(),
        ];

        // Get tracks for current edition
        $tracks = Track::where('hackathon_edition_id', $this->currentEdition->id)->get();
        $supervisors = User::role('track_supervisor')->get();

        return Inertia::render('HackathonAdmin/Ideas/Index', [
            'ideas' => $ideas,
            'statistics' => $statistics,
            'tracks' => $tracks,
            'supervisors' => $supervisors,
            'filters' => $request->only(['search', 'status', 'track_id', 'has_supervisor']),
        ]);
    }

    /**
     * Display the specified idea.
     */
    public function show(Idea $idea): Response
    {
        // Ensure idea belongs to current edition
        if (!$this->checkEditionOwnership($idea)) {
            abort(403);
        }

        $idea->load(['team.members', 'track', 'supervisor', 'files']);

        // Get review history from audit logs
        $reviewHistory = $idea->auditLogs()
            ->where('action', 'status_changed')
            ->with('user')
            ->latest()
            ->get();

        // Get scoring breakdown if available
        $scoring = null;
        if ($idea->evaluation_scores) {
            $scoring = $idea->evaluation_scores;
        }

        return Inertia::render('HackathonAdmin/Ideas/Show', [
            'idea' => $idea,
            'reviewHistory' => $reviewHistory,
            'scoring' => $scoring,
        ]);
    }

    /**
     * Show the form for reviewing an idea.
     */
    public function review(Idea $idea): Response
    {
        if (!$this->checkEditionOwnership($idea)) {
            abort(403);
        }

        $idea->load(['team', 'track', 'files']);

        $supervisors = User::role('track_supervisor')
            ->whereHas('supervisedTracks', function($q) use ($idea) {
                $q->where('tracks.id', $idea->track_id);
            })
            ->get();

        $scoringCriteria = [
            'innovation' => 'Innovation and Creativity (0-25)',
            'feasibility' => 'Technical Feasibility (0-25)',
            'impact' => 'Potential Impact (0-25)',
            'presentation' => 'Presentation Quality (0-25)',
        ];

        return Inertia::render('HackathonAdmin/Ideas/Review', [
            'idea' => $idea,
            'supervisors' => $supervisors,
            'scoringCriteria' => $scoringCriteria,
        ]);
    }

    /**
     * Process idea review.
     */
    public function processReview(Request $request, Idea $idea)
    {
        if (!$this->checkEditionOwnership($idea)) {
            abort(403);
        }

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

        // Update idea fields
        $idea->status = $validated['status'];

        // Assign reviewer if provided
        if (isset($validated['reviewed_by']) && !empty($validated['reviewed_by'])) {
            $idea->reviewed_by = $validated['reviewed_by'];
        }

        // Store scoring data in evaluation_scores column
        if (isset($validated['scores'])) {
            $idea->evaluation_scores = $validated['scores'];
            // Calculate total score and store in score column
            $idea->score = array_sum($validated['scores']);
        }

        // Add feedback
        if (isset($validated['feedback'])) {
            $idea->feedback = $validated['feedback'];
        }

        $idea->reviewed_at = now();
        $idea->save();

        // Log the review action
        $idea->logAction(
            'status_changed',
            'status',
            $validated['status'],
            $validated['feedback'] ?? 'Review processed',
            auth()->user()
        );

        // Send notification to team if requested
        if ($request->boolean('notify_team')) {
            // TODO: Trigger notification service here
        }

        return redirect()->route('hackathon-admin.ideas.show', $idea->id)
            ->with('success', 'Idea review completed successfully.');
    }

    /**
     * Assign a supervisor to an idea.
     */
    public function assignSupervisor(Request $request, Idea $idea)
    {
        if (!$this->checkEditionOwnership($idea)) {
            abort(403);
        }

        $request->validate([
            'supervisor_id' => 'required|exists:users,id',
        ]);

        $idea->supervisor_id = $request->supervisor_id;
        $idea->assigned_at = now();
        $idea->save();

        return back()->with('success', 'Supervisor assigned successfully.');
    }

    /**
     * Update score for an idea.
     */
    public function updateScore(Request $request, Idea $idea)
    {
        if (!$this->checkEditionOwnership($idea)) {
            abort(403);
        }

        $request->validate([
            'score' => 'required|numeric|min:0|max:100'
        ]);

        $idea->score = $request->score;
        $idea->save();

        $idea->logAction(
            'score_updated',
            'score',
            $request->score,
            'Score updated',
            auth()->user()
        );

        return response()->json(['success' => true, 'message' => 'Score updated successfully.']);
    }

    /**
     * Export ideas to Excel.
     */
    public function export(Request $request)
    {
        if (!$this->currentEdition) {
            return back()->with('error', 'No current hackathon edition found.');
        }

        $ideas = Idea::where('edition_id', $this->currentEdition->id)
        ->with(['team', 'track', 'supervisor'])
        ->get();

        // Here you would implement Excel export logic
        // For now, returning a simple CSV
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="ideas-export-' . date('Y-m-d') . '.csv"',
        ];

        $callback = function() use ($ideas) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['ID', 'Title', 'Team', 'Track', 'Status', 'Score', 'Supervisor', 'Submitted At']);

            foreach ($ideas as $idea) {
                fputcsv($file, [
                    $idea->id,
                    $idea->title,
                    $idea->team->name,
                    $idea->track->name ?? 'N/A',
                    $idea->status,
                    $idea->total_score ?? 'N/A',
                    $idea->supervisor->name ?? 'Not Assigned',
                    $idea->created_at->format('Y-m-d H:i'),
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
