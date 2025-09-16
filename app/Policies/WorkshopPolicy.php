<?php

namespace App\Policies;

use App\Models\Workshop;
use App\Models\User;
use App\Services\EditionContext;
use Illuminate\Auth\Access\HandlesAuthorization;

class WorkshopPolicy
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
        return $user->hasAnyRole(['system_admin', 'hackathon_admin', 'track_supervisor', 'workshop_supervisor']);
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Workshop $workshop): bool
    {
        // Admins can view all workshops
        if ($user->hasRole(['system_admin', 'hackathon_admin'])) {
            return true;
        }

        // Track supervisors can view workshops in their edition
        if ($user->hasRole('track_supervisor')) {
            $edition = $this->editionContext->current();
            return $workshop->hackathon_edition_id === $edition->id;
        }

        // Workshop supervisors can view workshops they supervise
        if ($user->hasRole('workshop_supervisor')) {
            return $workshop->supervisor_id === $user->id;
        }

        // Regular users can view published workshops
        return $workshop->is_published;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        // Admins and track supervisors can create workshops
        return $user->hasAnyRole(['system_admin', 'hackathon_admin', 'track_supervisor']);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Workshop $workshop): bool
    {
        // Admins can update any workshop
        if ($user->hasAnyRole(['system_admin', 'hackathon_admin'])) {
            return true;
        }

        // Track supervisors can update workshops in their edition
        if ($user->hasRole('track_supervisor')) {
            $edition = $this->editionContext->current();
            return $workshop->hackathon_edition_id === $edition->id;
        }

        // Workshop supervisors can update workshops they supervise
        if ($user->hasRole('workshop_supervisor')) {
            return $workshop->supervisor_id === $user->id;
        }

        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Workshop $workshop): bool
    {
        // Admins can delete any workshop
        if ($user->hasAnyRole(['system_admin', 'hackathon_admin'])) {
            return true;
        }

        // Track supervisors can delete workshops in their edition
        if ($user->hasRole('track_supervisor')) {
            $edition = $this->editionContext->current();
            return $workshop->hackathon_edition_id === $edition->id;
        }

        return false;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Workshop $workshop): bool
    {
        return $user->hasAnyRole(['system_admin', 'hackathon_admin']);
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Workshop $workshop): bool
    {
        return $user->hasRole('system_admin');
    }

    /**
     * Determine whether the user can manage attendees.
     */
    public function manageAttendees(User $user, Workshop $workshop): bool
    {
        // Admins can manage attendees for any workshop
        if ($user->hasAnyRole(['system_admin', 'hackathon_admin'])) {
            return true;
        }

        // Track supervisors can manage attendees for workshops in their edition
        if ($user->hasRole('track_supervisor')) {
            $edition = $this->editionContext->current();
            return $workshop->hackathon_edition_id === $edition->id;
        }

        // Workshop supervisors can manage attendees for workshops they supervise
        if ($user->hasRole('workshop_supervisor')) {
            return $workshop->supervisor_id === $user->id;
        }

        return false;
    }
}