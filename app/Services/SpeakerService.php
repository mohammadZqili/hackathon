<?php

namespace App\Services;

use App\Models\User;
use App\Repositories\SpeakerRepository;
use App\Repositories\HackathonEditionRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Collection;

class SpeakerService extends BaseService
{
    protected SpeakerRepository $speakerRepository;
    protected HackathonEditionRepository $editionRepository;

    public function __construct(
        SpeakerRepository $speakerRepository,
        HackathonEditionRepository $editionRepository
    ) {
        $this->speakerRepository = $speakerRepository;
        $this->editionRepository = $editionRepository;
    }

    /**
     * Get paginated speaker_PLURAL based on user role and filters
     */
    public function getPaginatedSpeakers(User $user, array $filters = [], int $perPage = 15): array
    {
        // Build filters based on user role
        $roleFilters = $this->buildRoleFilters($user, $filters);
        
        // Get paginated speaker_PLURAL
        $speaker_PLURAL = $this->speakerRepository->getPaginatedWithFilters($roleFilters, $perPage);
        
        // Get statistics
        $statistics = $this->speakerRepository->getStatistics($roleFilters);
        
        // Get editions for filter dropdown
        $editions = $this->getEditionsForUser($user);
        
        return [
            'speaker_PLURAL' => $speaker_PLURAL,
            'statistics' => $statistics,
            'editions' => $editions,
            'filters' => $filters
        ];
    }

    /**
     * Get speaker details
     */
    public function getSpeakerDetails(int $speakerId, User $user): ?array
    {
        $speaker = $this->speakerRepository->findWithFullDetails($speakerId);
        
        if (!$speaker) {
            return null;
        }
        
        // Check if user has access to this speaker
        if (!$this->userCanAccessSpeaker($user, $speaker)) {
            return null;
        }
        
        return [
            'speaker' => $speaker,
            'permissions' => $this->getSpeakerPermissions($user, $speaker)
        ];
    }

    /**
     * Create a new speaker
     */
    public function createSpeaker(array $data, User $user): array
    {
        // Check permissions
        if (!$this->userCanCreateSpeaker($user)) {
            throw new \Exception('You do not have permission to create speaker_PLURAL.');
        }
        
        // Validate edition access for non-system admin
        if ($user->user_type !== 'system_admin' && !empty($data['edition_id'])) {
            if (!$this->userCanAccessEdition($user, $data['edition_id'])) {
                throw new \Exception('You do not have access to this edition.');
            }
        }
        
        DB::beginTransaction();
        try {
            // Create speaker
            $speaker = $this->speakerRepository->create($data);
            
            // Log activity
            Log::info('Speaker created', [
                'speaker_id' => $speaker->id,
                'user_id' => $user->id,
                'data' => $data
            ]);
            
            DB::commit();
            
            return [
                'success' => true,
                'speaker' => $speaker,
                'message' => 'Speaker created successfully.'
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to create speaker', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
                'data' => $data
            ]);
            throw $e;
        }
    }

    /**
     * Update a speaker
     */
    public function updateSpeaker(int $speakerId, array $data, User $user): array
    {
        $speaker = $this->speakerRepository->find($speakerId);
        
        if (!$speaker) {
            throw new \Exception('Speaker not found.');
        }
        
        // Check permissions
        if (!$this->userCanEditSpeaker($user, $speaker)) {
            throw new \Exception('You do not have permission to edit this speaker.');
        }
        
        DB::beginTransaction();
        try {
            // Update speaker
            $this->speakerRepository->update($speakerId, $data);
            
            // Refresh speaker data
            $speaker = $this->speakerRepository->findWithFullDetails($speakerId);
            
            // Log activity
            Log::info('Speaker updated', [
                'speaker_id' => $speakerId,
                'user_id' => $user->id,
                'data' => $data
            ]);
            
            DB::commit();
            
            return [
                'success' => true,
                'speaker' => $speaker,
                'message' => 'Speaker updated successfully.'
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to update speaker', [
                'speaker_id' => $speakerId,
                'user_id' => $user->id,
                'error' => $e->getMessage(),
                'data' => $data
            ]);
            throw $e;
        }
    }

    /**
     * Delete a speaker
     */
    public function deleteSpeaker(int $speakerId, User $user): array
    {
        $speaker = $this->speakerRepository->find($speakerId);
        
        if (!$speaker) {
            throw new \Exception('Speaker not found.');
        }
        
        // Check permissions
        if (!$this->userCanDeleteSpeaker($user, $speaker)) {
            throw new \Exception('You do not have permission to delete this speaker.');
        }
        
        // Check dependencies
        if ($this->speakerRepository->hasDependencies($speakerId)) {
            throw new \Exception('Cannot delete speaker with dependencies.');
        }
        
        DB::beginTransaction();
        try {
            // Delete speaker
            $this->speakerRepository->delete($speakerId);
            
            // Log activity
            Log::info('Speaker deleted', [
                'speaker_id' => $speakerId,
                'user_id' => $user->id
            ]);
            
            DB::commit();
            
            return [
                'success' => true,
                'message' => 'Speaker deleted successfully.'
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to delete speaker', [
                'speaker_id' => $speakerId,
                'user_id' => $user->id,
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
                
            case 'system_admin':
                // No additional filters - can see everything
                break;
                
            default:
                // Other roles - force empty result
                $roleFilters['force_empty'] = true;
                break;
        }
        
        return $roleFilters;
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
                
            default:
                return collect();
        }
    }

    /**
     * Check if user can access a specific speaker
     */
    protected function userCanAccessSpeaker(User $user, $speaker): bool
    {
        switch ($user->user_type) {
            case 'system_admin':
                return true;
                
            case 'hackathon_admin':
                return !isset($speaker->edition_id) || $user->edition_id == $speaker->edition_id;
                
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
                return false;
        }
    }

    /**
     * Check if user can create speaker_PLURAL
     */
    protected function userCanCreateSpeaker(User $user): bool
    {
        return in_array($user->user_type, ['system_admin', 'hackathon_admin']);
    }

    /**
     * Check if user can edit a speaker
     */
    protected function userCanEditSpeaker(User $user, $speaker): bool
    {
        if (!$this->userCanAccessSpeaker($user, $speaker)) {
            return false;
        }
        
        return in_array($user->user_type, ['system_admin', 'hackathon_admin']);
    }

    /**
     * Check if user can delete a speaker
     */
    protected function userCanDeleteSpeaker(User $user, $speaker): bool
    {
        if (!$this->userCanAccessSpeaker($user, $speaker)) {
            return false;
        }
        
        // Only system admin can delete
        return $user->user_type === 'system_admin';
    }

    /**
     * Get permissions for a speaker
     */
    protected function getSpeakerPermissions(User $user, $speaker): array
    {
        return [
            'canEdit' => $this->userCanEditSpeaker($user, $speaker),
            'canDelete' => $this->userCanDeleteSpeaker($user, $speaker),
            'canExport' => true,
        ];
    }
}
