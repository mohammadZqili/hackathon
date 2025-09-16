<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Edition;
use Illuminate\Http\Request;

class TestAccountsController extends Controller
{
    /**
     * Get test accounts for development environment
     * Only accessible in local/development environment
     */
    public function index(Request $request)
    {
        // Only allow in development environment
        if (!app()->environment('local', 'development')) {
            abort(404);
        }

        // Get current edition
        $currentEdition = Edition::where('is_current', true)->first();

        $accounts = [
            'system_admin' => [],
            'hackathon_admin' => [],
            'track_supervisor' => [],
            'workshop_supervisor' => [],
            'team_leader' => [],
            'team_member' => [],
            'visitor' => [],
        ];

        // Get System Admin
        $systemAdmins = User::role('system_admin')
            ->select('id', 'name', 'email')
            ->limit(1)
            ->get();

        $accounts['system_admin'] = $systemAdmins->map(function ($user) {
            return [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'password' => 'password', // Default password for all test accounts
                'extra' => 'Full System Access',
            ];
        });

        // Get Hackathon Admins - just get any hackathon admins
        $hackathonAdmins = User::role('hackathon_admin')
            ->select('id', 'name', 'email')
            ->limit(2)
            ->get();

        $accounts['hackathon_admin'] = $hackathonAdmins->map(function ($user) use ($currentEdition) {
            $editionText = $currentEdition ? "Edition: {$currentEdition->edition_name}" : "Hackathon Admin";
            return [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'password' => 'password',
                'extra' => $editionText,
            ];
        });

        // Get Track Supervisors - simplified query
        $trackSupervisors = User::role('track_supervisor')
            ->select('id', 'name', 'email')
            ->limit(2)
            ->get();

        $accounts['track_supervisor'] = $trackSupervisors->map(function ($user) {
            // Try to get track info if available
            $track = \App\Models\Track::whereHas('supervisors', function($q) use ($user) {
                $q->where('users.id', $user->id);
            })->first();

            $trackName = $track ? $track->name : 'Track Supervisor';
            return [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'password' => 'password',
                'extra' => "Track: {$trackName}",
            ];
        });

        // Get Workshop Supervisor (if none exist, leave empty)
        $workshopSupervisors = User::role('workshop_supervisor')
            ->select('id', 'name', 'email')
            ->limit(1)
            ->get();

        $accounts['workshop_supervisor'] = $workshopSupervisors->map(function ($user) {
            return [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'password' => 'password',
                'extra' => 'Workshop Management',
            ];
        });

        // Get Team Leaders - simplified
        $teamLeaders = User::role('team_leader')
            ->with('team')
            ->select('id', 'name', 'email', 'team_id')
            ->limit(2)
            ->get();

        $accounts['team_leader'] = $teamLeaders->map(function ($user) {
            $teamName = $user->team?->name ?? 'Team Leader';
            return [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'password' => 'password',
                'extra' => "Team: {$teamName}",
                'team_id' => $user->team_id,
            ];
        });

        // Get Team Members - simplified to just get team members with their teams
        $teamMembers = User::role('team_member')
            ->with('team')
            ->select('id', 'name', 'email', 'team_id')
            ->limit(6)
            ->get();

        $memberArray = [];
        foreach ($teamMembers as $member) {
            $teamName = $member->team?->name ?? 'Team Member';
            $memberArray[] = [
                'id' => $member->id,
                'name' => $member->name,
                'email' => $member->email,
                'password' => 'password',
                'extra' => "Team: {$teamName}",
                'team_name' => $teamName,
            ];
        }

        $accounts['team_member'] = collect($memberArray);

        // Get Visitor
        $visitors = User::role('visitor')
            ->select('id', 'name', 'email')
            ->limit(1)
            ->get();

        $accounts['visitor'] = $visitors->map(function ($user) {
            return [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'password' => 'password',
                'extra' => 'Workshop Access Only',
            ];
        });

        return response()->json([
            'accounts' => $accounts,
            'current_edition' => $currentEdition ? [
                'id' => $currentEdition->id,
                'name' => $currentEdition->edition_name,
                'year' => $currentEdition->edition_year,
            ] : null,
        ]);
    }
}