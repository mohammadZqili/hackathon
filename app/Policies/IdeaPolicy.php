<?php

namespace App\Policies;

use App\Models\Idea;
use App\Models\User;
use App\Services\EditionContext;
use Illuminate\Auth\Access\HandlesAuthorization;

class IdeaPolicy
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
        return $user->hasAnyRole(['system_admin', 'hackathon_admin', 'track_supervisor', 'team_leader', 'team_member']);
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Idea $idea): bool
    {
        // Admins can view all ideas
        if ($user->hasRole(['system_admin', 'hackathon_admin'])) {
            return true;
        }

        // Track supervisors can only view ideas in their assigned tracks
        if ($user->hasRole('track_supervisor')) {
            $edition = $this->editionContext->current();
            if (!$edition) {
                return false;
            }

            // Get idea's edition through its team
            $ideaEditionId = $idea->team ? $idea->team->edition_id : null;

            // Check if idea belongs to current edition
            if ($ideaEditionId !== $edition->id) {
                return false;
            }

            // Check if idea's track is assigned to this supervisor
            $trackIds = $user->tracksInEdition($edition->id)->pluck('tracks.id')->toArray();
            return in_array($idea->track_id, $trackIds);
        }

        // Team members can view their own team's ideas
        if ($user->hasRole(['team_leader', 'team_member'])) {
            $team = $idea->team;
            if ($team) {
                return $team->leader_id === $user->id ||
                       $team->members()->where('user_id', $user->id)->exists();
            }
        }

        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        // Only team leaders can create ideas for their team
        if ($user->hasRole('team_leader')) {
            return true;
        }

        // Admins and track supervisors can create ideas (for testing/management purposes)
        return $user->hasAnyRole(['system_admin', 'hackathon_admin', 'track_supervisor']);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Idea $idea): bool
    {
        // Admins can update any idea
        if ($user->hasAnyRole(['system_admin', 'hackathon_admin'])) {
            return true;
        }

        // Track supervisors can update ideas in their assigned tracks
        if ($user->hasRole('track_supervisor')) {
            $edition = $this->editionContext->current();
            if (!$edition) {
                return false;
            }

            // Get idea's edition through its team
            $ideaEditionId = $idea->team ? $idea->team->edition_id : null;

            // Check if idea belongs to current edition
            if ($ideaEditionId !== $edition->id) {
                return false;
            }

            // Check if idea's track is assigned to this supervisor
            $trackIds = $user->tracksInEdition($edition->id)->pluck('tracks.id')->toArray();
            return in_array($idea->track_id, $trackIds);
        }

        // Team leader can update their team's idea
        if ($user->hasRole('team_leader')) {
            $team = $idea->team;
            return $team && $team->leader_id === $user->id;
        }

        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Idea $idea): bool
    {
        // Admins can delete any idea
        if ($user->hasAnyRole(['system_admin', 'hackathon_admin'])) {
            return true;
        }

        // Track supervisors can delete ideas in their assigned tracks
        if ($user->hasRole('track_supervisor')) {
            $edition = $this->editionContext->current();
            if (!$edition) {
                return false;
            }

            // Get idea's edition through its team
            $ideaEditionId = $idea->team ? $idea->team->edition_id : null;

            // Check if idea belongs to current edition
            if ($ideaEditionId !== $edition->id) {
                return false;
            }

            // Check if idea's track is assigned to this supervisor
            $trackIds = $user->tracksInEdition($edition->id)->pluck('tracks.id')->toArray();
            return in_array($idea->track_id, $trackIds);
        }

        return false;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Idea $idea): bool
    {
        return $user->hasAnyRole(['system_admin', 'hackathon_admin']);
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Idea $idea): bool
    {
        return $user->hasRole('system_admin');
    }

    /**
     * Determine whether the user can review the idea.
     */
    public function review(User $user, Idea $idea): bool
    {
        // Admins can review any idea
        if ($user->hasAnyRole(['system_admin', 'hackathon_admin'])) {
            return true;
        }

        // Track supervisors can review ideas in their assigned tracks
        if ($user->hasRole('track_supervisor')) {
            $edition = $this->editionContext->current();
            if (!$edition) {
                return false;
            }

            // Check if idea's track is assigned to this supervisor
            $trackIds = $user->tracksInEdition($edition->id)->pluck('tracks.id')->toArray();
            return in_array($idea->track_id, $trackIds);
        }

        return false;
    }

    /**
     * Determine whether the user can accept the idea.
     */
    public function accept(User $user, Idea $idea): bool
    {
        return $this->review($user, $idea);
    }

    /**
     * Determine whether the user can reject the idea.
     */
    public function reject(User $user, Idea $idea): bool
    {
        return $this->review($user, $idea);
    }

    /**
     * Determine whether the user can request edits for the idea.
     */
    public function needEdit(User $user, Idea $idea): bool
    {
        return $this->review($user, $idea);
    }

    /**
     * Determine whether the user can assign a supervisor to the idea.
     */
    public function assignSupervisor(User $user, Idea $idea): bool
    {
        // Only admins can assign supervisors
        return $user->hasAnyRole(['system_admin', 'hackathon_admin']);
    }

    /**
     * Determine whether the user can update the score of the idea.
     */
    public function updateScore(User $user, Idea $idea): bool
    {
        return $this->review($user, $idea);
    }
}