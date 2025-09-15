<?php

namespace App\Policies;

use App\Models\Team;
use App\Models\User;
use App\Services\EditionContext;
use Illuminate\Auth\Access\HandlesAuthorization;

class TeamPolicy
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
    public function view(User $user, Team $team): bool
    {
        // Admins can view all teams
        if ($user->hasRole(['system_admin', 'hackathon_admin'])) {
            return true;
        }

        // Track supervisors can only view teams in their assigned tracks
        if ($user->hasRole('track_supervisor')) {
            $edition = $this->editionContext->current();
            if (!$edition) {
                return false;
            }

            // Check if team belongs to current edition
            if ($team->edition_id !== $edition->id) {
                return false;
            }

            // Check if team's track is assigned to this supervisor
            $trackIds = $user->tracksInEdition($edition->id)->pluck('tracks.id')->toArray();
            return in_array($team->track_id, $trackIds);
        }

        // Team leaders and members can view their own team
        if ($user->hasRole(['team_leader', 'team_member'])) {
            return $team->leader_id === $user->id ||
                   $team->members()->where('user_id', $user->id)->exists();
        }

        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        // Admins and track supervisors can create teams
        return $user->hasAnyRole(['system_admin', 'hackathon_admin', 'track_supervisor']);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Team $team): bool
    {
        // Only admins can update teams
        // Track supervisors cannot update teams
        if ($user->hasAnyRole(['system_admin', 'hackathon_admin'])) {
            return true;
        }

        // Team leader can update their own team
        if ($user->hasRole('team_leader') && $team->leader_id === $user->id) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Team $team): bool
    {
        // Only admins can delete teams
        return $user->hasAnyRole(['system_admin', 'hackathon_admin']);
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Team $team): bool
    {
        return $user->hasAnyRole(['system_admin', 'hackathon_admin']);
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Team $team): bool
    {
        return $user->hasRole('system_admin');
    }

    /**
     * Determine whether the user can add members to the team.
     */
    public function addMember(User $user, Team $team): bool
    {
        // Admins can add members to any team
        if ($user->hasAnyRole(['system_admin', 'hackathon_admin'])) {
            return true;
        }

        // Team leader can add members to their own team
        if ($user->hasRole('team_leader') && $team->leader_id === $user->id) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can remove members from the team.
     */
    public function removeMember(User $user, Team $team): bool
    {
        // Admins can remove members from any team
        if ($user->hasAnyRole(['system_admin', 'hackathon_admin'])) {
            return true;
        }

        // Team leader can remove members from their own team
        if ($user->hasRole('team_leader') && $team->leader_id === $user->id) {
            return true;
        }

        return false;
    }
}