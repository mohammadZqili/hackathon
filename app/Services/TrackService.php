<?php

namespace App\Services;

use App\Models\User;
use App\Repositories\TrackRepository;
use App\Repositories\HackathonEditionRepository;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TrackService extends BaseService
{
    protected TrackRepository $trackRepository;
    protected HackathonEditionRepository $editionRepository;

    public function __construct(
        TrackRepository $trackRepository,
        HackathonEditionRepository $editionRepository
    ) {
        $this->trackRepository = $trackRepository;
        $this->editionRepository = $editionRepository;
    }

    /**
     * Get track supervisors
     */
    public function getTrackSupervisors()
    {
        return $this->trackRepository->getTrackSupervisors();
    }

    /**
     * Get paginated tracks based on user role and filters
     */
    public function getPaginatedTracks(User $user, array $filters = [], int $perPage = 15): array
    {
        // Build filters based on user role
        $roleFilters = $this->buildRoleFilters($user, $filters);

        // Get paginated tracks
        $tracks = $this->trackRepository->getPaginatedWithFilters($roleFilters, $perPage);

        // Get statistics
        $statistics = $this->trackRepository->getStatistics($roleFilters);

        // Get editions for filter dropdown
        $editions = $this->getEditionsForUser($user);

        return [
            'tracks' => $tracks,
            'statistics' => $statistics,
            'editions' => $editions,
            'filters' => $filters
        ];
    }

    /**
     * Get track details
     */
    public function getTrackDetails(int $trackId, User $user): ?array
    {
        $track = $this->trackRepository->findWithFullDetails($trackId);

        if (!$track) {
            return null;
        }

        // Check if user has access to this track
        if (!$this->userCanAccessTrack($user, $track)) {
            return null;
        }

        return [
            'track' => $track,
            'permissions' => $this->getTrackPermissions($user, $track)
        ];
    }

    /**
     * Create a new track
     */
    public function createTrack(array $data, User $user): array
    {
        // Check permissions
        if (!$this->userCanCreateTrack($user)) {
            throw new \Exception('You do not have permission to create tracks.');
        }

        // Validate edition access for non-system admin
        if (!$user->hasRole('system_admin') && !empty($data['edition_id'])) {
            if (!$this->userCanAccessEdition($user, $data['edition_id'])) {
                throw new \Exception('You do not have access to this edition.');
            }
        }

        DB::beginTransaction();
        try {
            // Extract supervisor_id if present
            $supervisorId = $data['supervisor_id'] ?? null;
            $trackData = collect($data)->except(['supervisor_id'])->toArray();

            // Create track
            $track = $this->trackRepository->create($trackData);

            // Assign supervisor if provided
            if ($supervisorId) {
                $track->supervisors()->attach($supervisorId, [
                    'is_primary' => true,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }

            // Log activity
            Log::info('Track created', [
                'track_id' => $track->id,
                'user_id' => $user->id,
                'data' => $data
            ]);

            DB::commit();

            return [
                'success' => true,
                'track' => $track,
                'message' => 'Track created successfully.'
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to create track', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
                'data' => $data
            ]);
            throw $e;
        }
    }

    /**
     * Update a track
     */
    public function updateTrack(int $trackId, array $data, User $user): array
    {
        $track = $this->trackRepository->find($trackId);

        if (!$track) {
            throw new \Exception('Track not found.');
        }

        // Check permissions
        if (!$this->userCanEditTrack($user, $track)) {
            throw new \Exception('You do not have permission to edit this track.');
        }

        // Validate edition access for non-system admin
        if (!$user->hasRole('system_admin') && !empty($data['edition_id'])) {
            if (!$this->userCanAccessEdition($user, $data['edition_id'])) {
                throw new \Exception('You do not have access to this edition.');
            }
        }

        DB::beginTransaction();
        try {
            // Extract supervisor_id if present
            $supervisorId = $data['supervisor_id'] ?? null;
            $trackData = collect($data)->except(['supervisor_id'])->toArray();

            // Update track
            $this->trackRepository->update($trackId, $trackData);

            // Update supervisor assignment
            if (isset($data['supervisor_id'])) {
                // Remove existing primary supervisor
                $track->supervisors()->wherePivot('is_primary', true)->detach();

                // Assign new supervisor if provided
                if ($supervisorId) {
                    $track->supervisors()->attach($supervisorId, [
                        'is_primary' => true,
                        'created_at' => now(),
                        'updated_at' => now()
                    ]);
                }
            }

            // Refresh track data
            $track = $this->trackRepository->findWithFullDetails($trackId);

            // Log activity
            Log::info('Track updated', [
                'track_id' => $trackId,
                'user_id' => $user->id,
                'data' => $data
            ]);

            DB::commit();

            return [
                'success' => true,
                'track' => $track,
                'message' => 'Track updated successfully.'
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to update track', [
                'track_id' => $trackId,
                'user_id' => $user->id,
                'error' => $e->getMessage(),
                'data' => $data
            ]);
            throw $e;
        }
    }

    /**
     * Delete a track
     */
    public function deleteTrack(int $trackId, User $user): array
    {
        $track = $this->trackRepository->find($trackId);

        if (!$track) {
            throw new \Exception('Track not found.');
        }

        // Check permissions
        if (!$this->userCanDeleteTrack($user, $track)) {
            throw new \Exception('You do not have permission to delete this track.');
        }

        // Check dependencies
        if ($this->trackRepository->hasDependencies($trackId)) {
            throw new \Exception('Cannot delete track with associated teams or ideas.');
        }

        DB::beginTransaction();
        try {
            // Delete track
            $this->trackRepository->delete($trackId);

            // Log activity
            Log::info('Track deleted', [
                'track_id' => $trackId,
                'user_id' => $user->id
            ]);

            DB::commit();

            return [
                'success' => true,
                'message' => 'Track deleted successfully.'
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to delete track', [
                'track_id' => $trackId,
                'user_id' => $user->id,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Export tracks to CSV
     */
    public function exportTracks(User $user, array $filters = []): array
    {
        // Build filters based on user role
        $roleFilters = $this->buildRoleFilters($user, $filters);

        // Get tracks for export
        $tracks = $this->trackRepository->getForExport($roleFilters);

        $csvData = [];
        $csvData[] = ['ID', 'Name', 'Edition', 'Description', 'Teams Count', 'Ideas Count', 'Status', 'Created At'];

        foreach ($tracks as $track) {
            $csvData[] = [
                $track->id,
                $track->name,
                $track->edition->name ?? 'N/A',
                $track->description,
                $track->teams_count,
                $track->ideas_count,
                $track->is_active ? 'Active' : 'Inactive',
                $track->created_at->format('Y-m-d H:i'),
            ];
        }

        return [
            'data' => $csvData,
            'filename' => 'tracks-export-' . date('Y-m-d') . '.csv'
        ];
    }

    /**
     * Get data for create/edit forms
     */
    public function getFormData(User $user, ?int $trackId = null): array
    {
        $data = [
            'editions' => $this->getEditionsForUser($user),
            'hackathons' => $this->getHackathonsForUser($user),
            'supervisors' => $this->getAvailableSupervisors($user)
        ];

        if ($trackId) {
            $track = $this->trackRepository->findWithFullDetails($trackId);
            if ($track && $this->userCanAccessTrack($user, $track)) {
                $data['track'] = $track;
            }
        }

        return $data;
    }

    /**
     * Get available supervisors for track assignment
     */
    protected function getAvailableSupervisors(User $user): Collection
    {
        // Get users with track_supervisor role or system_admin role
        return \App\Models\User::whereIn('user_type', ['system_admin', 'track_supervisor'])
            ->where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name', 'email', 'user_type']);
    }

    /**
     * Assign supervisor to track
     */
    public function assignSupervisor(int $trackId, int $userId, User $currentUser, bool $isPrimary = false): array
    {
        $track = $this->trackRepository->find($trackId);

        if (!$track) {
            throw new \Exception('Track not found.');
        }

        // Check permissions
        if (!$this->userCanManageSupervisors($currentUser, $track)) {
            throw new \Exception('You do not have permission to manage supervisors for this track.');
        }

        DB::beginTransaction();
        try {
            $result = $this->trackRepository->assignSupervisor($trackId, $userId, $isPrimary);

            if (!$result) {
                throw new \Exception('Failed to assign supervisor.');
            }

            // Log activity
            Log::info('Supervisor assigned to track', [
                'track_id' => $trackId,
                'supervisor_id' => $userId,
                'assigned_by' => $currentUser->id,
                'is_primary' => $isPrimary
            ]);

            DB::commit();

            return [
                'success' => true,
                'message' => 'Supervisor assigned successfully.'
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to assign supervisor', [
                'track_id' => $trackId,
                'supervisor_id' => $userId,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Build filters based on user role
     */
    protected function buildRoleFilters(User $user, array $filters): array
    {
        $roleFilters = $filters;

        switch ($user->user_type) {
            case 'hackathon_admin':
                // Limit to user's edition
                if ($user->edition_id) {
                    $roleFilters['edition_id'] = $user->edition_id;
                }
                break;

            case 'track_supervisor':
                // Get supervised tracks
                $trackIds = DB::table('track_supervisors')
                    ->where('user_id', $user->id)
                    ->pluck('track_id')
                    ->toArray();

                if (!empty($trackIds)) {
                    $roleFilters['track_ids'] = $trackIds;
                } else {
                    // No tracks to show
                    $roleFilters['track_ids'] = [0]; // Force empty result
                }
                break;

            case 'system_admin':
                // No additional filters - can see everything
                break;

            default:
                // Other roles shouldn't access tracks
                $roleFilters['track_ids'] = [0]; // Force empty result
                break;
        }

        return $roleFilters;
    }

    /**
     * Get hackathons available to user
     */
    protected function getHackathonsForUser(User $user): Collection
    {
        switch ($user->user_type) {
            case 'system_admin':
                return \App\Models\Hackathon::all(['id', 'name']);
            case 'hackathon_admin':
            case 'track_supervisor':
                // For now, return all hackathons - can be filtered later based on requirements
                return \App\Models\Hackathon::all(['id', 'name']);
            default:
                return collect();
        }
    }

    /**
     * Get editions available to user
     */
    protected function getEditionsForUser(User $user): Collection
    {
        switch ($user->user_type) {
            case 'system_admin':
                return $this->editionRepository->all();

            case 'hackathon_admin':
                if ($user->edition_id) {
                    return collect([$this->editionRepository->find($user->edition_id)]);
                }
                return collect();

            case 'track_supervisor':
                // Get editions from supervised tracks
                $tracks = $this->trackRepository->getTracksBySupervisor((int) $user->id);
                $editionIds = $tracks->pluck('edition_id')->unique()->filter();

                if ($editionIds->isEmpty()) {
                    return collect();
                }

                return $this->editionRepository->findManyBy('id', $editionIds->toArray());

            default:
                return collect();
        }
    }

    /**
     * Check if user can access a specific track
     */
    protected function userCanAccessTrack(User $user, $track): bool
    {
        switch ($user->user_type) {
            case 'system_admin':
                return true;

            case 'hackathon_admin':
                return $user->edition_id == $track->edition_id;

            case 'track_supervisor':
                $trackIds = DB::table('track_supervisors')
                    ->where('user_id', $user->id)
                    ->pluck('track_id')
                    ->toArray();
                return in_array($track->id, $trackIds);

            default:
                return false;
        }
    }

    /**
     * Check if user can access a specific edition
     */
    protected function userCanAccessEdition(User $user, int $editionId): bool
    {
        switch ($user->user_type) {
            case 'system_admin':
                return true;

            case 'hackathon_admin':
                return $user->edition_id == $editionId;

            default:
                // Check if user has track_supervisor role
                if ($user->hasRole('track_supervisor')) {
                    // Track supervisors can access the current edition they're working with
                    $currentEdition = $this->editionRepository->getCurrentEdition();
                    if ($currentEdition && $currentEdition->id == $editionId) {
                        return true;
                    }

                    // Also check if they have tracks assigned in this edition
                    $trackCount = $user->tracksInEdition($editionId)->count();
                    return $trackCount > 0;
                }

                return false;
        }
    }

    /**
     * Check if user can create tracks
     */
    protected function userCanCreateTrack(User $user): bool
    {
        return in_array($user->user_type, ['system_admin', 'hackathon_admin']);
    }

    /**
     * Check if user can edit a track
     */
    protected function userCanEditTrack(User $user, $track): bool
    {
        if (!$this->userCanAccessTrack($user, $track)) {
            return false;
        }

        // Admins can edit all tracks
        if (in_array($user->user_type, ['system_admin', 'hackathon_admin'])) {
            return true;
        }

        // Track supervisors can edit tracks they are assigned to
        if ($user->hasRole('track_supervisor')) {
            $currentEdition = $this->editionRepository->getCurrentEdition();
            if (!$currentEdition) {
                return false;
            }

            // Check if track belongs to current edition
            if ($track->edition_id !== $currentEdition->id) {
                return false;
            }

            // Check if this track is assigned to this supervisor
            $trackIds = $user->tracksInEdition($currentEdition->id)->pluck('tracks.id')->toArray();
            return in_array($track->id, $trackIds);
        }

        return false;
    }

    /**
     * Check if user can delete a track
     */
    protected function userCanDeleteTrack(User $user, $track): bool
    {
        if (!$this->userCanAccessTrack($user, $track)) {
            return false;
        }

        // Only system admin can delete
        return $user->hasRole('system_admin');
    }

    /**
     * Check if user can manage supervisors
     */
    protected function userCanManageSupervisors(User $user, $track): bool
    {
        if (!$this->userCanAccessTrack($user, $track)) {
            return false;
        }

        return in_array($user->user_type, ['system_admin', 'hackathon_admin']);
    }

    /**
     * Get permissions for a track
     */
    protected function getTrackPermissions(User $user, $track): array
    {
        return [
            'canEdit' => $this->userCanEditTrack($user, $track),
            'canDelete' => $this->userCanDeleteTrack($user, $track),
            'canManageSupervisors' => $this->userCanManageSupervisors($user, $track),
            'canExport' => true, // All users with access can export
        ];
    }
}
