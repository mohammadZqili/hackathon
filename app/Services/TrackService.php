<?php

namespace App\Services;

use App\Models\Track;
use App\Models\User;
use App\Repositories\TrackRepository;
use App\Services\Contracts\TrackServiceInterface;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TrackService implements TrackServiceInterface
{
    public function __construct(
        private TrackRepository $trackRepo
    ) {}

    /**
     * Get tracks for a hackathon with statistics.
     */
    public function getHackathonTracks(int $hackathonId): Collection
    {
        return Cache::remember("hackathon_{$hackathonId}_tracks", 300, function () use ($hackathonId) {
            $tracks = $this->trackRepo->getByHackathon($hackathonId);

            return $tracks->map(function ($track) {
                $statistics = $this->getTrackStatistics($track->id);

                return [
                    'id' => $track->id,
                    'name' => $track->name,
                    'name_en' => $track->name_en,
                    'description' => $track->description,
                    'description_en' => $track->description_en,
                    'color' => $track->color,
                    'icon' => $track->icon,
                    'prize_description' => $track->prize_description,
                    'supervisor' => $track->supervisor ? [
                        'id' => $track->supervisor?->id,
                        'name' => $track->supervisor?->name,
                    ] : null,
                    'statistics' => $statistics,
                ];
            });
        });
    }

    /**
     * Get track details with comprehensive statistics.
     */
    public function getTrackDetails(int $trackId): array
    {
        $track = $this->trackRepo->getWithSupervisor($trackId);

        if (!$track) {
            throw new \Exception('المسار غير موجود');
        }

        // Get track statistics
        $statistics = $this->getTrackStatistics($trackId);

        // Get accepted ideas with teams
        $acceptedIdeas = $this->trackRepo->getIdeasByTrack($trackId, ['status' => 'accepted']);

        return [
            'track' => $track,
            'statistics' => $statistics,
            'accepted_ideas' => $acceptedIdeas,
            'hackathon' => $track->hackathon,
        ];
    }

    /**
     * Get track statistics.
     */
    public function getTrackStatistics(int $trackId): array
    {
        return Cache::remember("track_{$trackId}_statistics", 180, function () use ($trackId) {
            $statistics = $this->trackRepo->getTrackStatistics($trackId);

            return [
                'total_teams' => $statistics['teams_count'] ?? 0,
                'total_ideas' => $statistics['ideas_count'] ?? 0,
                'submitted_ideas' => $statistics['submitted_ideas_count'] ?? 0,
                'accepted_ideas' => $statistics['accepted_ideas_count'] ?? 0,
                'ideas_by_status' => [
                    'draft' => $statistics['draft_ideas_count'] ?? 0,
                    'submitted' => $statistics['submitted_status_count'] ?? 0,
                    'under_review' => $statistics['under_review_count'] ?? 0,
                    'needs_revision' => $statistics['needs_revision_count'] ?? 0,
                    'accepted' => $statistics['accepted_status_count'] ?? 0,
                    'rejected' => $statistics['rejected_count'] ?? 0,
                ],
                'average_score' => $statistics['average_score'] ?? 0,
            ];
        });
    }

    /**
     * Check if user can register for track.
     */
    public function canUserRegisterForTrack(Track $track, $user): bool
    {
        if (!$user) {
            return false;
        }

        // Check if hackathon registration is open
        if (!$track->hackathon->isRegistrationOpen()) {
            return false;
        }

        // Check if user already has a team in this hackathon
        $existingTeam = $user->ledTeams()
            ->where('hackathon_id', $track->hackathon_id)
            ->first();

        return !$existingTeam;
    }

    /**
     * Get track with ideas and teams.
     */
    public function getTrackWithRelations(int $trackId): Track
    {
        $track = $this->trackRepo->findOrFail($trackId);

        $track->load([
            'supervisor',
            'ideas' => function ($query) {
                $query->where('status', 'accepted')
                    ->with(['team' => function ($q) {
                        $q->with(['leader', 'acceptedMembers.user']);
                    }])
                    ->orderBy('score', 'desc');
            }
        ]);

        return $track;
    }

    /**
     * Create a new track.
     */
    public function createTrack(array $data): Track
    {
        return DB::transaction(function () use ($data) {
            $track = $this->trackRepo->create($data);
            
            Log::info('Track created', [
                'track_id' => $track->id,
                'edition_id' => $data['hackathon_edition_id'] ?? null,
            ]);
            
            return $track;
        });
    }

    /**
     * Update a track.
     */
    public function updateTrack(int $trackId, array $data): Track
    {
        $track = $this->trackRepo->update($trackId, $data);
        
        // Clear cache
        Cache::forget("track_{$trackId}_statistics");
        
        Log::info('Track updated', [
            'track_id' => $trackId,
            'updated_data' => array_keys($data),
        ]);
        
        return $track;
    }

    /**
     * Delete a track.
     */
    public function deleteTrack(int $trackId): bool
    {
        // Clear cache
        Cache::forget("track_{$trackId}_statistics");
        
        $result = $this->trackRepo->delete($trackId);
        
        Log::info('Track deleted', ['track_id' => $trackId]);
        
        return $result;
    }

    /**
     * Assign supervisor to track.
     */
    public function assignSupervisor(int $trackId, ?int $supervisorId): Track
    {
        $track = $this->trackRepo->update($trackId, [
            'supervisor_id' => $supervisorId
        ]);
        
        if ($supervisorId) {
            $supervisor = User::find($supervisorId);
            if ($supervisor && !$supervisor->hasRole('track_supervisor')) {
                $supervisor->assignRole('track_supervisor');
            }
            
            // Create track-supervisor relationship
            DB::table('track_supervisors')->updateOrInsert(
                ['track_id' => $trackId],
                [
                    'user_id' => $supervisorId,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }
        
        Log::info('Track supervisor assigned', [
            'track_id' => $trackId,
            'supervisor_id' => $supervisorId,
        ]);
        
        return $track;
    }
}
