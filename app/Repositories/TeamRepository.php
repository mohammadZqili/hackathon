<?php

namespace App\Repositories;

use App\Models\Team;
use App\Models\TeamMember;

class TeamRepository
{
    public function findByLeaderId($userId)
    {
        return Team::where('leader_id', $userId)->with(['members', 'track'])->first();
    }

    public function findMemberTeam($userId)
    {
        return TeamMember::where('user_id', $userId)->with('team.members')->first();
    }

    public function create(array $data)
    {
        return Team::create($data);
    }

    public function update($id, array $data)
    {
        $team = Team::findOrFail($id);
        $team->update($data);
        return $team;
    }

    public function addMember($teamId, array $memberData)
    {
        return TeamMember::create([
            'team_id' => $teamId,
            'user_id' => $memberData['user_id'],
            'role' => $memberData['role'] ?? 'member',
            'joined_at' => now()
        ]);
    }

    public function removeMember($teamId, $userId)
    {
        return TeamMember::where('team_id', $teamId)
            ->where('user_id', $userId)
            ->delete();
    }
}
