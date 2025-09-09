<?php

namespace App\Http\Controllers\SystemAdmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Idea;
use App\Services\IdeaService;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class IdeaController extends Controller
{
    public function __construct(
        private IdeaService $ideaService
    ) {}

    public function index(Request $request)
    {
        $filters = $request->only(['search', 'status', 'track', 'edition']);
        
        $ideas = $this->ideaService->getPaginatedIdeas($filters, 15);
        $filterOptions = $this->ideaService->getFilterOptions();
        $stats = $this->ideaService->getGeneralIdeaStatistics();

        return Inertia::render('SystemAdmin/Ideas/Index', [
            'ideas' => $ideas,
            'filters' => $filters,
            'tracks' => $filterOptions['tracks'],
            'editions' => $filterOptions['editions'],
            'stats' => $stats
        ]);
    }

    public function show(Idea $idea)
    {
        $ideaWithRelations = $this->ideaService->getIdeaWithRelations($idea->id);
        $availableSupervisors = $this->ideaService->getAvailableSupervisors($idea->track_id);
        $userPermissions = $this->ideaService->getUserPermissions();

        return Inertia::render('SystemAdmin/Ideas/Show', [
            'idea' => $ideaWithRelations,
            'availableSupervisors' => $availableSupervisors,
            'can' => $userPermissions
        ]);
    }

    /**
     * Show the review page for an idea
     */
    public function review(Idea $idea)
    {
        $ideaWithRelations = $this->ideaService->getIdeaWithRelations($idea->id);
        $availableSupervisors = $this->ideaService->getAvailableSupervisors($idea->track_id);
        $evaluationCriteria = $this->ideaService->getEvaluationCriteria($idea->track);

        return Inertia::render('SystemAdmin/Ideas/Review', [
            'idea' => $ideaWithRelations,
            'availableSupervisors' => $availableSupervisors,
            'evaluationCriteria' => $evaluationCriteria
        ]);
    }

    public function destroy(Idea $idea)
    {
        $this->ideaService->deleteIdea($idea);

        return redirect()->route('system-admin.ideas.index')
            ->with('success', 'Idea deleted successfully.');
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

        $this->ideaService->acceptIdea($idea, $request->only(['feedback', 'score']));

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

        $this->ideaService->rejectIdea($idea, $request->only(['feedback', 'score']));

        return redirect()->back()->with('success', 'Idea rejected.');
    }

    /**
     * Request edits for an idea
     */
    public function needEdit(Request $request, Idea $idea)
    {
        $request->validate([
            'feedback' => 'required|string|max:2000',
            'score' => 'nullable|numeric|min:0|max:100'
        ]);

        $this->ideaService->markForRevision($idea, $request->only(['feedback', 'score']));

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

        try {
            $this->ideaService->assignSupervisor($idea, $request->supervisor_id);
            
            return redirect()->back()->with('success', 'Supervisor assigned successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    /**
     * Update score for an idea
     */
    public function updateScore(Request $request, Idea $idea)
    {
        $request->validate([
            'score' => 'required|numeric|min:0|max:100'
        ]);

        $this->ideaService->updateScore($idea, $request->score);

        return redirect()->back()->with('success', 'Score updated successfully.');
    }

    /**
     * Download idea file
     */
    public function downloadFile(Idea $idea, $fileId)
    {
        $file = $idea->files()->findOrFail($fileId);
        
        $filePath = storage_path('app/' . $file->file_path);
        
        if (!file_exists($filePath)) {
            return redirect()->back()->with('error', 'File not found.');
        }

        return response()->download($filePath, $file->original_name ?? $file->file_name);
    }

    /**
     * Export ideas data
     */
    public function export(Request $request)
    {
        $filters = $request->only(['search', 'status', 'track', 'edition']);
        $exportData = $this->ideaService->getExportData($filters);

        return response()->json([
            'data' => $exportData,
            'total' => $exportData->count(),
            'exported_at' => now()->toISOString()
        ]);
    }

    /**
     * Get idea statistics
     */
    public function statistics()
    {
        $stats = $this->ideaService->getDetailedStatistics();
        return response()->json($stats);
    }
}
