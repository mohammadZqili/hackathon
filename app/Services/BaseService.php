<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;

class BaseService
{
    /**
     * Apply role-based filtering to any query
     * Note: Using user_type field, not role
     */
    protected function scopeByRole(Builder $query, User $user, string $model = ''): Builder
    {
        return match($user->user_type) {  // Changed from $user->role
            'system_admin' => $query,
            'hackathon_admin' => $this->scopeForHackathonAdmin($query, $user, $model),
            'track_supervisor' => $this->scopeForTrackSupervisor($query, $user, $model),
            'workshop_supervisor' => $this->scopeForWorkshopSupervisor($query, $user, $model),
            'team_leader', 'team_member' => $this->scopeForTeamMember($query, $user, $model),
            'visitor' => $query->whereRaw('1 = 0'),
            default => $query->whereRaw('1 = 0')
        };
    }
    
    private function scopeForHackathonAdmin($query, $user, $model)
    {
        $editionId = $user->edition_id ?? $user->hackathon_edition_id;
        
        return match($model) {
            'Team' => $query->where('edition_id', $editionId),
            'Idea' => $query->whereHas('team', fn($q) => $q->where('edition_id', $editionId)),
            'Workshop' => $query->where('edition_id', $editionId),
            'Track' => $query->where('edition_id', $editionId),
            'User' => $query->whereHas('teams', fn($q) => $q->where('edition_id', $editionId)),
            'News' => $query->where('edition_id', $editionId),
            default => $query
        };
    }
    
    private function scopeForTrackSupervisor($query, $user, $model)
    {
        // Get supervised track IDs from relation
        $trackIds = \DB::table('track_supervisors')
            ->where('user_id', $user->id)
            ->pluck('track_id')
            ->toArray();
        
        if (empty($trackIds)) {
            return $query->whereRaw('1 = 0');
        }
        
        return match($model) {
            'Team' => $query->whereIn('track_id', $trackIds),
            'Idea' => $query->whereIn('track_id', $trackIds),
            'Track' => $query->whereIn('id', $trackIds),
            default => $query->whereRaw('1 = 0')
        };
    }
    
    private function scopeForWorkshopSupervisor($query, $user, $model)
    {
        // Get supervised workshop IDs from relation
        $workshopIds = \DB::table('workshop_supervisors')
            ->where('user_id', $user->id)
            ->pluck('workshop_id')
            ->toArray();
        
        if (empty($workshopIds)) {
            return $query->whereRaw('1 = 0');
        }
        
        return match($model) {
            'Workshop' => $query->whereIn('id', $workshopIds),
            'WorkshopRegistration' => $query->whereIn('workshop_id', $workshopIds),
            'WorkshopAttendance' => $query->whereIn('workshop_id', $workshopIds),
            default => $query->whereRaw('1 = 0')
        };
    }
    
    private function scopeForTeamMember($query, $user, $model)
    {
        $teamId = $user->team_id;
        
        if (!$teamId) {
            return $query->whereRaw('1 = 0');
        }
        
        return match($model) {
            'Team' => $query->where('id', $teamId),
            'Idea' => $query->where('team_id', $teamId),
            'TeamMember' => $query->where('team_id', $teamId),
            default => $query->whereRaw('1 = 0')
        };
    }
    
    protected function getBasePermissions(User $user): array
    {
        return match($user->user_type) {  // Changed from $user->role
            'system_admin' => [
                'canCreate' => true,
                'canEdit' => true,
                'canDelete' => true,
                'canExport' => true,
                'canViewAll' => true,
                'canManageUsers' => true,
                'canManageSettings' => true
            ],
            'hackathon_admin' => [
                'canCreate' => true,
                'canEdit' => true,
                'canDelete' => false,
                'canExport' => true,
                'canViewAll' => true,
                'canManageUsers' => false,
                'canManageSettings' => false
            ],
            'track_supervisor' => [
                'canCreate' => false,
                'canEdit' => false,
                'canDelete' => false,
                'canExport' => true,
                'canViewAll' => true,
                'canReview' => true,
                'canScore' => true
            ],
            'workshop_supervisor' => [
                'canCreate' => false,
                'canEdit' => false,
                'canDelete' => false,
                'canExport' => true,
                'canCheckIn' => true,
                'canViewAttendance' => true
            ],
            'team_leader' => [
                'canCreate' => true,
                'canEdit' => true,
                'canDelete' => false,
                'canExport' => false,
                'canInviteMembers' => true,
                'canSubmitIdea' => true
            ],
            'team_member' => [
                'canCreate' => false,
                'canEdit' => false,
                'canDelete' => false,
                'canExport' => false,
                'canViewTeam' => true,
                'canLeaveTeam' => true
            ],
            'visitor' => [
                'canRegisterWorkshop' => true,
                'canViewPublic' => true
            ],
            default => []
        };
    }
}
