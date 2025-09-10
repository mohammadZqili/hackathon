<?php

namespace App\Http\Controllers\Shared;

use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

abstract class BaseController extends Controller
{
    /**
     * Apply role-based scope to query
     */
    protected function applyRoleScope(Builder $query, Request $request, string $model = null)
    {
        $user = $request->user();
        
        // System Admin - no restrictions
        if ($user->hasRole('system-admin')) {
            return $query;
        }
        
        // Hackathon Admin - restrict to their edition
        if ($user->hasRole('hackathon-admin')) {
            $editionId = $user->edition_id ?? session('current_edition_id');
            
            if ($model === 'Edition') {
                return $query->where('id', $editionId);
            }
            
            if (in_array($model, ['Team', 'Idea', 'Track', 'News', 'Workshop', 'Checkin'])) {
                return $query->where('edition_id', $editionId);
            }
            
            if ($model === 'User') {
                return $query->whereHas('teams', function($q) use ($editionId) {
                    $q->where('edition_id', $editionId);
                });
            }
        }
        
        // Track Supervisor - restrict to their track
        if ($user->hasRole('track-supervisor')) {
            $trackIds = $user->supervisedTracks->pluck('id')->toArray();
            $editionId = $user->supervisedTracks->first()->edition_id ?? null;
            
            if ($model === 'Track') {
                return $query->whereIn('id', $trackIds);
            }
            
            if ($model === 'Team') {
                return $query->whereIn('track_id', $trackIds);
            }
            
            if ($model === 'Idea') {
                return $query->whereHas('team', function($q) use ($trackIds) {
                    $q->whereIn('track_id', $trackIds);
                });
            }
            
            if ($model === 'User') {
                return $query->whereHas('teams', function($q) use ($trackIds) {
                    $q->whereIn('track_id', $trackIds);
                });
            }
            
            // For other models, restrict to edition
            if ($editionId) {
                return $query->where('edition_id', $editionId);
            }
        }
        
        // Workshop Supervisor - restrict to their workshops
        if ($user->hasRole('workshop-supervisor')) {
            $workshopIds = $user->supervisedWorkshops->pluck('id')->toArray();
            
            if ($model === 'Workshop') {
                return $query->whereIn('id', $workshopIds);
            }
            
            if ($model === 'Checkin') {
                return $query->whereIn('workshop_id', $workshopIds);
            }
        }
        
        return $query;
    }
    
    /**
     * Get the current edition for the user
     */
    protected function getCurrentEdition(Request $request)
    {
        $user = $request->user();
        
        if ($user->hasRole('system-admin')) {
            return session('current_edition_id') 
                ? \App\Models\Edition::find(session('current_edition_id'))
                : \App\Models\Edition::where('is_active', true)->first();
        }
        
        if ($user->hasRole('hackathon-admin')) {
            return \App\Models\Edition::find($user->edition_id);
        }
        
        if ($user->hasRole('track-supervisor')) {
            return $user->supervisedTracks->first()->edition ?? null;
        }
        
        return \App\Models\Edition::where('is_active', true)->first();
    }
    
    /**
     * Check if user can access resource
     */
    protected function canAccess($resource, Request $request): bool
    {
        $user = $request->user();
        
        if ($user->hasRole('system-admin')) {
            return true;
        }
        
        if ($user->hasRole('hackathon-admin')) {
            if (isset($resource->edition_id)) {
                return $resource->edition_id == $user->edition_id;
            }
        }
        
        if ($user->hasRole('track-supervisor')) {
            if (isset($resource->track_id)) {
                return $user->supervisedTracks->pluck('id')->contains($resource->track_id);
            }
            if (method_exists($resource, 'team') && $resource->team) {
                return $user->supervisedTracks->pluck('id')->contains($resource->team->track_id);
            }
        }
        
        return false;
    }
    
    /**
     * Get role-specific route prefix
     */
    protected function getRoutePrefix(Request $request): string
    {
        $user = $request->user();
        
        if ($user->hasRole('system-admin')) {
            return 'system-admin';
        }
        
        if ($user->hasRole('hackathon-admin')) {
            return 'hackathon-admin';
        }
        
        if ($user->hasRole('track-supervisor')) {
            return 'track-supervisor';
        }
        
        if ($user->hasRole('workshop-supervisor')) {
            return 'workshop-supervisor';
        }
        
        return 'visitor';
    }
}