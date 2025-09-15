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
     * Get all speakers
     */
    public function getAllSpeakers()
    {
        return $this->speakerRepository->getAllOrderedByName();
    }

    /**
     * Get paginated speakers (simple version for backward compatibility)
     */
    public function getPaginatedSpeakers($userOrPerPage = 15, array $filters = [], int $perPage = 15)
    {
        // If first parameter is a User object, use the full method
        if ($userOrPerPage instanceof User) {
            return $this->getPaginatedSpeakersWithUser($userOrPerPage, $filters, $perPage);
        }

        // Otherwise, treat it as perPage parameter for simple pagination
        return $this->speakerRepository->paginate($userOrPerPage);
    }






    /**
     * Get speaker with organization
     */
    public function getSpeakerWithOrganization(int $id)
    {
        return $this->speakerRepository->findWithOrganization($id);
    }

    /**
     * Activate speaker
     */
    public function activateSpeaker(int $id): bool
    {
        return $this->speakerRepository->update($id, ['is_active' => true]);
    }

    /**
     * Deactivate speaker
     */
    public function deactivateSpeaker(int $id): bool
    {
        return $this->speakerRepository->update($id, ['is_active' => false]);
    }

    /**
     * Get paginated speakers based on user role and filters
     */
    public function getPaginatedSpeakersWithUser(User $user, array $filters = [], int $perPage = 15): array
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
     * Create speaker (backward compatibility wrapper)
     */
    public function createSpeaker($dataOrUser, $userOrNull = null)
    {
        // If second parameter is provided, use new signature
        if ($userOrNull instanceof User) {
            return $this->createSpeakerWithUser($dataOrUser, $userOrNull);
        }

        // Otherwise, simple create without user context
        return $this->speakerRepository->create($dataOrUser);
    }

    /**
     * Create a new speaker with user permissions
     */
    public function createSpeakerWithUser(array $data, User $user): array
    {
        // Check permissions
        if (!$this->userCanCreateSpeaker($user)) {
            throw new \Exception('You do not have permission to create speaker_PLURAL.');
        }

        // Validate edition access for non-system admin
        if (!$user->hasRole('system_admin') && !empty($data['edition_id'])) {
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
     * Update speaker (backward compatibility wrapper)
     */
    public function updateSpeaker($speakerIdOrId, $dataOrUser = null, $userOrNull = null)
    {
        // If third parameter is provided, use new signature
        if ($userOrNull instanceof User) {
            return $this->updateSpeakerWithUser($speakerIdOrId, $dataOrUser, $userOrNull);
        }

        // Otherwise, simple update without user context
        return $this->speakerRepository->update($speakerIdOrId, $dataOrUser ?? []);
    }

    /**
     * Update a speaker with user permissions
     */
    public function updateSpeakerWithUser(int $speakerId, array $data, User $user): array
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
     * Delete speaker (backward compatibility wrapper)
     */
    public function deleteSpeaker($speakerIdOrId, $userOrNull = null)
    {
        // If second parameter is provided, use new signature
        if ($userOrNull instanceof User) {
            return $this->deleteSpeakerWithUser($speakerIdOrId, $userOrNull);
        }

        // Otherwise, simple delete without user context
        return $this->speakerRepository->delete($speakerIdOrId);
    }

    /**
     * Delete a speaker with user permissions
     */
    public function deleteSpeakerWithUser(int $speakerId, User $user): array
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
        return $user->hasRole('system_admin');
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
