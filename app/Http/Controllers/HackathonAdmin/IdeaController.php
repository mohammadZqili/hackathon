<?php

namespace App\Http\Controllers\HackathonAdmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Idea;
use App\Models\User;
use App\Models\Track;
use App\Models\Hackathon;
use App\Models\HackathonEdition;

class IdeaController extends Controller
{
    /**
     * Display a listing of all ideas across all editions.
     */
    public function index(Request $request)
    {
        $query = Idea::with(['team', 'track', 'reviewer']);

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
        $statistics = [
            'total' => Idea::count(),
            'draft' => Idea::where('status', 'draft')->count(),
            'submitted' => Idea::where('status', 'submitted')->count(),
            'under_review' => Idea::where('status', 'under_review')->count(),
            'accepted' => Idea::where('status', 'accepted')->count(),
            'rejected' => Idea::where('status', 'rejected')->count(),
            'needs_revision' => Idea::where('status', 'needs_revision')->count(),
            'pending_review' => Idea::where('status', 'pending_review')->count(),
            'in_progress' => Idea::where('status', 'in_progress')->count(),
            'completed' => Idea::where('status', 'completed')->count(),
        ];

        $tracks = Track::all();
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
    public function show(Idea $idea)
    {
        $idea->load(['team.members', 'track', 'reviewer', 'files']);

        // Get review history from audit logs
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

        // Get scoring breakdown if available
        $scoring = null;
        if ($idea->evaluation_scores) {
            $scoring = $idea->evaluation_scores;
            $scoring['total_score'] = $idea->score;
        }

        return Inertia::render('HackathonAdmin/Ideas/Show', [
            'idea' => $idea,
            'reviewHistory' => $reviewHistory,
            'scoring' => $scoring,
        ]);
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

        return Inertia::render('HackathonAdmin/Ideas/Review', [
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
            $validated['feedback'] ?? 'Review processed by System Admin',
            auth()->user()
        );

        // Send notification to team if requested
        if ($request->boolean('notify_team')) {
            // TODO: Trigger notification service here
        }

        return redirect()->route('system-admin.ideas.show', $idea->id)
            ->with('success', 'Idea review completed successfully.');
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
        // Delete associated files from storage
        foreach ($idea->files as $file) {
            \Storage::disk('public')->delete($file->path);
        }

        // Delete the idea (cascade will handle related records)
        $idea->delete();

        return redirect()->route('system-admin.ideas.index')
            ->with('success', 'Idea deleted successfully.');
    }

    /**
     * Export ideas data
     */
    public function export(Request $request)
    {
        $query = Idea::with(['team', 'track', 'reviewer']);

        // Apply same filters as index
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

        $ideas = $query->get();

        // Generate CSV
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="ideas-export-' . date('Y-m-d') . '.csv"',
        ];

        $callback = function() use ($ideas) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['ID', 'Title', 'Team', 'Track', 'Status', 'Score', 'Reviewer', 'Submitted At', 'Reviewed At']);

            foreach ($ideas as $idea) {
                fputcsv($file, [
                    $idea->id,
                    $idea->title,
                    $idea->team?->name ?? 'N/A',
                    $idea->track?->name ?? 'N/A',
                    $idea->status,
                    $idea->score ?? 'N/A',
                    $idea->reviewer?->name ?? 'Not Assigned',
                    $idea->submitted_at?->format('Y-m-d H:i') ?? 'N/A',
                    $idea->reviewed_at?->format('Y-m-d H:i') ?? 'N/A',
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Get idea statistics
     */
    public function statistics()
    {
        $stats = [
            'by_status' => Idea::selectRaw('status, count(*) as count')
                ->groupBy('status')
                ->pluck('count', 'status'),
            'by_track' => Idea::selectRaw('track_id, count(*) as count')
                ->groupBy('track_id')
                ->with('track:id,name')
                ->get()
                ->mapWithKeys(function ($item) {
                    return [$item->track?->name ?? 'Unassigned' => $item->count];
                }),
            'average_score' => Idea::whereNotNull('score')->avg('score'),
            'total_reviewed' => Idea::whereNotNull('reviewed_at')->count(),
            'pending_review' => Idea::whereIn('status', ['submitted', 'under_review'])->count(),
        ];

        return response()->json($stats);
    }
}
