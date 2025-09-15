<?php

namespace App\Policies;

use App\Models\Track;
use App\Models\User;
use App\Services\EditionContext;
use Illuminate\Auth\Access\HandlesAuthorization;

class TrackPolicy
{
    use HandlesAuthorization;

    protected EditionContext $editionContext;

    public function __construct()
    {
        $this->editionContext = app(EditionContext::class);
    }

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(['system_admin', 'hackathon_admin', 'track_supervisor']);
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Track $track): bool
    {
        // Admins can view all tracks
        if ($user->hasRole(['system_admin', 'hackathon_admin'])) {
            return true;
        }

        // Track supervisors can only view tracks they are assigned to
        if ($user->hasRole('track_supervisor')) {
            $edition = $this->editionContext->current();
            if (!$edition) {
                return false;
            }

            // Check if track belongs to current edition
            if ($track->edition_id !== $edition->id) {
                return false;
            }

            // Check if this track is assigned to this supervisor
            $trackIds = $user->tracksInEdition($edition->id)->pluck('tracks.id')->toArray();
            return in_array($track->id, $trackIds);
        }

        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        // Only admins can create tracks
        // Track supervisors cannot create tracks
        return $user->hasAnyRole(['system_admin', 'hackathon_admin']);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Track $track): bool
    {
        // Only admins can update tracks
        // Track supervisors cannot update tracks
        return $user->hasAnyRole(['system_admin', 'hackathon_admin']);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Track $track): bool
    {
        // Only system admin can delete tracks
        return $user->hasRole('system_admin');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Track $track): bool
    {
        return $user->hasRole('system_admin');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Track $track): bool
    {
        return $user->hasRole('system_admin');
    }

    /**
     * Determine whether the user can manage supervisors for this track.
     */
    public function manageSupervisors(User $user, Track $track): bool
    {
        // Only admins can manage track supervisors
        return $user->hasAnyRole(['system_admin', 'hackathon_admin']);
    }

    /**
     * Determine whether the user can view teams in this track.
     */
    public function viewTeams(User $user, Track $track): bool
    {
        // Use the same logic as view permission
        return $this->view($user, $track);
    }

    /**
     * Determine whether the user can view ideas in this track.
     */
    public function viewIdeas(User $user, Track $track): bool
    {
        // Use the same logic as view permission
        return $this->view($user, $track);
    }

    /**
     * Determine whether the user can export data from this track.
     */
    public function export(User $user, Track $track): bool
    {
        // Use the same logic as view permission
        return $this->view($user, $track);
    }
}