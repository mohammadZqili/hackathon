<?php

namespace App\Services;

use App\Repositories\IdeaRepository;
use App\Repositories\TeamRepository;
use Illuminate\Support\Facades\DB;

class IdeaService extends BaseService
{
    protected $ideaRepo;
    protected $teamRepo;

    public function __construct(IdeaRepository $ideaRepo, TeamRepository $teamRepo)
    {
        $this->ideaRepo = $ideaRepo;
        $this->teamRepo = $teamRepo;
    }

    public function getTeamIdea($userId)
    {
        $team = $this->teamRepo->findByLeaderId($userId);
        if (!$team) {
            $member = $this->teamRepo->findMemberTeam($userId);
            $team = $member ? $member->team : null;
        }
        
        return $team ? $this->ideaRepo->findByTeamId($team->id) : null;
    }

    public function submitIdea($userId, array $data)
    {
        return DB::transaction(function () use ($userId, $data) {
            $team = $this->teamRepo->findByLeaderId($userId);
            if (!$team) {
                throw new \Exception('Only team leaders can submit ideas');
            }

            $data['team_id'] = $team->id;
            $data['submitted_by'] = $userId;
            $data['status'] = 'submitted';
            
            return $this->ideaRepo->create($data);
        });
    }

    public function updateIdea($ideaId, $userId, array $data)
    {
        return DB::transaction(function () use ($ideaId, $userId, $data) {
            $idea = $this->ideaRepo->find($ideaId);
            $team = $this->teamRepo->findByLeaderId($userId);
            
            if (!$team || $idea->team_id !== $team->id) {
                throw new \Exception('Unauthorized to update this idea');
            }

            return $this->ideaRepo->update($ideaId, $data);
        });
    }

    public function addComment($ideaId, $userId, $comment)
    {
        $idea = $this->ideaRepo->find($ideaId);
        
        // Check if user is part of the team
        $team = $this->teamRepo->findByLeaderId($userId);
        if (!$team) {
            $member = $this->teamRepo->findMemberTeam($userId);
            $team = $member ? $member->team : null;
        }
        
        if (!$team || $idea->team_id !== $team->id) {
            throw new \Exception('Unauthorized to comment on this idea');
        }

        return $this->ideaRepo->addComment($ideaId, [
            'user_id' => $userId,
            'comment' => $comment,
            'created_at' => now()
        ]);
    }
}
