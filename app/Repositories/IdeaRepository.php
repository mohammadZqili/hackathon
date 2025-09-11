<?php

namespace App\Repositories;

use App\Models\Idea;
use App\Models\IdeaComment;

class IdeaRepository
{
    public function findByTeamId($teamId)
    {
        return Idea::where('team_id', $teamId)
            ->with(['comments.user', 'attachments'])
            ->first();
    }

    public function find($id)
    {
        return Idea::findOrFail($id);
    }

    public function create(array $data)
    {
        return Idea::create($data);
    }

    public function update($id, array $data)
    {
        $idea = Idea::findOrFail($id);
        $idea->update($data);
        return $idea;
    }

    public function addComment($ideaId, array $commentData)
    {
        return IdeaComment::create([
            'idea_id' => $ideaId,
            'user_id' => $commentData['user_id'],
            'comment' => $commentData['comment'],
            'created_at' => $commentData['created_at']
        ]);
    }

    public function getComments($ideaId)
    {
        return IdeaComment::where('idea_id', $ideaId)
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->get();
    }
}
