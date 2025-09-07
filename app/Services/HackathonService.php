<?php

namespace App\Services;

use App\Models\Hackathon;
use App\Repositories\HackathonRepository;
use App\Repositories\TeamRepository;
use App\Repositories\IdeaRepository;
use App\Repositories\WorkshopRepository;
use App\Services\Contracts\HackathonServiceInterface;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class HackathonService implements HackathonServiceInterface
{
    public function __construct(
        private HackathonRepository $hackathonRepo,
        private TeamRepository $teamRepo,
        private IdeaRepository $ideaRepo,
        private WorkshopRepository $workshopRepo
    ) {}

    /**
     * Get the current active hackathon.
     */
    public function getCurrentHackathon(): ?Hackathon
    {
        return Cache::remember('current_hackathon', 3600, function () {
            return $this->hackathonRepo->findBy('is_current', true);
        });
    }

    /**
     * Create a new hackathon.
     */
    public function createHackathon(array $data): Hackathon
    {
        return DB::transaction(function () use ($data) {
            // If marking as current, deactivate others
            if ($data['is_current'] ?? false) {
                Hackathon::where('is_current', true)->update(['is_current' => false]);
                Cache::forget('current_hackathon');
            }
            
            // Add creator
            $data['created_by'] = auth()->id();
            
            // Create the hackathon
            $hackathon = $this->hackathonRepo->create($data);
            
            // Log the creation
            Log::info('Hackathon created', [
                'hackathon_id' => $hackathon->id,
                'name' => $hackathon->name,
                'created_by' => auth()->id()
            ]);
            
            return $hackathon;
        });
    }

    /**
     * Update hackathon details.
     */
    public function updateHackathon(int $id, array $data): bool
    {
        Cache::forget('current_hackathon');
        Cache::forget("hackathon_stats_{$id}");
        
        return $this->hackathonRepo->update($id, $data);
    }

    /**
     * Activate a hackathon and deactivate others.
     */
    public function activateHackathon(int $id): bool
    {
        return DB::transaction(function () use ($id) {
            // Deactivate all others
            Hackathon::where('is_current', true)->update(['is_current' => false]);
            
            // Activate this one
            $result = $this->hackathonRepo->update($id, [
                'is_current' => true,
                'is_active' => true
            ]);
            
            // Clear cache
            Cache::forget('current_hackathon');
            
            Log::info('Hackathon activated', ['hackathon_id' => $id]);
            
            return $result;
        });
    }

    /**
     * Get comprehensive statistics for a hackathon.
     */
    public function getHackathonStatistics(int $id): array
    {
        return Cache::remember("hackathon_stats_{$id}", 600, function () use ($id) {
            $hackathon = $this->hackathonRepo->findOrFail($id);
            
            // Get team statistics
            $teams = $hackathon->teams()->withCount('acceptedMembers')->get();
            $totalParticipants = $teams->sum('accepted_members_count');
            
            // Get idea statistics
            $ideas = $hackathon->teams()->with('idea')->get()->pluck('idea')->filter();
            $ideaStats = [
                'total' => $ideas->count(),
                'draft' => $ideas->where('status', 'draft')->count(),
                'submitted' => $ideas->where('status', 'submitted')->count(),
                'under_review' => $ideas->where('status', 'under_review')->count(),
                'needs_revision' => $ideas->where('status', 'needs_revision')->count(),
                'accepted' => $ideas->where('status', 'accepted')->count(),
                'rejected' => $ideas->where('status', 'rejected')->count(),
            ];
            
            // Get workshop statistics
            $workshops = $hackathon->workshops;
            $workshopStats = [
                'total' => $workshops->count(),
                'upcoming' => $workshops->where('start_time', '>', now())->count(),
                'completed' => $workshops->where('end_time', '<', now())->count(),
                'total_registrations' => $workshops->sum('current_attendees'),
            ];
            
            // Calculate timeline progress
            $registrationProgress = $this->calculateRegistrationProgress($hackathon);
            $ideaSubmissionProgress = $this->calculateIdeaSubmissionProgress($hackathon);
            
            return [
                'teams' => [
                    'total' => $teams->count(),
                    'active' => $teams->where('status', 'active')->count(),
                    'submitted' => $teams->where('status', 'submitted')->count(),
                ],
                'participants' => [
                    'total' => $totalParticipants,
                    'average_per_team' => $teams->count() > 0 ? round($totalParticipants / $teams->count(), 1) : 0,
                ],
                'ideas' => $ideaStats,
                'workshops' => $workshopStats,
                'tracks' => [
                    'total' => $hackathon->tracks()->count(),
                    'with_ideas' => $hackathon->tracks()->has('ideas')->count(),
                ],
                'timeline' => [
                    'registration' => $registrationProgress,
                    'idea_submission' => $ideaSubmissionProgress,
                ],
            ];
        });
    }

    /**
     * Check if registration is currently open.
     */
    public function isRegistrationOpen(int $id): bool
    {
        $hackathon = $this->hackathonRepo->find($id);
        return $hackathon ? $hackathon->isRegistrationOpen() : false;
    }

    /**
     * Check if idea submission is currently open.
     */
    public function isIdeaSubmissionOpen(int $id): bool
    {
        $hackathon = $this->hackathonRepo->find($id);
        return $hackathon ? $hackathon->isIdeaSubmissionOpen() : false;
    }

    /**
     * Get all hackathons with pagination.
     */
    public function getAllHackathons(int $perPage = 15): mixed
    {
        return $this->hackathonRepo->paginate($perPage);
    }

    /**
     * Archive a completed hackathon.
     */
    public function archiveHackathon(int $id): bool
    {
        return DB::transaction(function () use ($id) {
            $result = $this->hackathonRepo->update($id, [
                'is_active' => false,
                'is_current' => false,
            ]);
            
            Cache::forget('current_hackathon');
            Cache::forget("hackathon_stats_{$id}");
            
            Log::info('Hackathon archived', ['hackathon_id' => $id]);
            
            return $result;
        });
    }

    /**
     * Get registration timeline data.
     */
    public function getRegistrationTimeline(int $id): array
    {
        $hackathon = $this->hackathonRepo->findOrFail($id);
        
        return [
            'registration' => [
                'start' => $hackathon->registration_start_date,
                'end' => $hackathon->registration_end_date,
                'is_open' => $hackathon->isRegistrationOpen(),
                'days_remaining' => $this->calculateDaysRemaining($hackathon->registration_end_date),
            ],
            'idea_submission' => [
                'start' => $hackathon->idea_submission_start_date,
                'end' => $hackathon->idea_submission_end_date,
                'is_open' => $hackathon->isIdeaSubmissionOpen(),
                'days_remaining' => $this->calculateDaysRemaining($hackathon->idea_submission_end_date),
            ],
            'event' => [
                'start' => $hackathon->event_start_date,
                'end' => $hackathon->event_end_date,
                'is_running' => $hackathon->isEventRunning(),
                'days_until_start' => $this->calculateDaysUntil($hackathon->event_start_date),
            ],
        ];
    }

    /**
     * Calculate registration progress percentage.
     */
    private function calculateRegistrationProgress(Hackathon $hackathon): array
    {
        $total = $hackathon->registration_end_date->diffInDays($hackathon->registration_start_date);
        $elapsed = now()->diffInDays($hackathon->registration_start_date);
        
        if ($elapsed < 0) {
            // Registration hasn't started
            return [
                'percentage' => 0,
                'status' => 'not_started',
                'days_until_start' => abs($elapsed),
            ];
        }
        
        if (now()->isAfter($hackathon->registration_end_date)) {
            // Registration ended
            return [
                'percentage' => 100,
                'status' => 'ended',
                'days_since_end' => now()->diffInDays($hackathon->registration_end_date),
            ];
        }
        
        // Registration is open
        $percentage = min(100, ($elapsed / $total) * 100);
        
        return [
            'percentage' => round($percentage, 2),
            'status' => 'open',
            'days_remaining' => $hackathon->registration_end_date->diffInDays(now()),
        ];
    }

    /**
     * Calculate idea submission progress percentage.
     */
    private function calculateIdeaSubmissionProgress(Hackathon $hackathon): array
    {
        $total = $hackathon->idea_submission_end_date->diffInDays($hackathon->idea_submission_start_date);
        $elapsed = now()->diffInDays($hackathon->idea_submission_start_date);
        
        if ($elapsed < 0) {
            // Submission hasn't started
            return [
                'percentage' => 0,
                'status' => 'not_started',
                'days_until_start' => abs($elapsed),
            ];
        }
        
        if (now()->isAfter($hackathon->idea_submission_end_date)) {
            // Submission ended
            return [
                'percentage' => 100,
                'status' => 'ended',
                'days_since_end' => now()->diffInDays($hackathon->idea_submission_end_date),
            ];
        }
        
        // Submission is open
        $percentage = min(100, ($elapsed / $total) * 100);
        
        return [
            'percentage' => round($percentage, 2),
            'status' => 'open',
            'days_remaining' => $hackathon->idea_submission_end_date->diffInDays(now()),
        ];
    }

    /**
     * Calculate days remaining until a date.
     */
    private function calculateDaysRemaining($date): int
    {
        return max(0, now()->diffInDays($date, false));
    }

    /**
     * Calculate days until a date.
     */
    private function calculateDaysUntil($date): int
    {
        $diff = now()->diffInDays($date, false);
        return $diff < 0 ? 0 : $diff;
    }
}
