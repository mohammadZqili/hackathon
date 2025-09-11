<?php

namespace App\Services;

use App\Repositories\TeamRepository;
use App\Repositories\IdeaRepository;
use App\Repositories\UserRepository;
use App\Models\Team;
use App\Models\User;
use App\Models\Idea;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class TeamService
{
    protected TeamRepository $teamRepository;
    protected IdeaRepository $ideaRepository;
    protected UserRepository $userRepository;

    public function __construct(
        TeamRepository $teamRepository,
        IdeaRepository $ideaRepository,
        UserRepository $userRepository
    ) {
        $this->teamRepository = $teamRepository;
        $this->ideaRepository = $ideaRepository;
        $this->userRepository = $userRepository;
    }

    /**
     * Get the team led by the user
     */
    public function getMyTeam(User $user): ?Team
    {
        return $this->teamRepository->findByLeaderId($user->id);
    }

    /**
     * Get dashboard statistics for team leader
     */
    public function getDashboardStats(User $user): array
    {
        $team = $this->getMyTeam($user);
        
        if (!$team) {
            return [
                'has_team' => false,
                'team_name' => null,
                'members_count' => 0,
                'idea_status' => null,
                'pending_members' => 0,
                'track_name' => null,
                'edition_name' => null,
            ];
        }

        return [
            'has_team' => true,
            'team_name' => $team->name,
            'members_count' => $team->acceptedMembers()->count() + 1, // +1 for leader
            'idea_status' => $team->idea?->status ?? 'not_submitted',
            'pending_members' => $team->pendingMembers()->count(),
            'track_name' => $team->track?->name,
            'edition_name' => $team->hackathon?->name,
            'team_status' => $team->status,
            'created_at' => $team->created_at,
        ];
    }

    /**
     * Get team members with their details
     */
    public function getTeamMembers(Team $team): Collection
    {
        return $team->members()->with('user')->get()->map(function ($member) {
            return [
                'id' => $member->user->id,
                'name' => $member->user->name,
                'email' => $member->user->email,
                'status' => $member->pivot->status,
                'role' => $member->pivot->role,
                'joined_at' => $member->pivot->joined_at,
            ];
        });
    }

    /**
     * Add a new team member
     */
    public function addTeamMember(Team $team, string $email, string $role = 'member'): array
    {
        DB::beginTransaction();
        try {
            $user = $this->userRepository->findByEmail($email);
            
            if (!$user) {
                DB::rollBack();
                return ['success' => false, 'message' => 'User not found with this email'];
            }

            // Check if user is already in another team for this hackathon
            $existingTeam = $this->teamRepository->getTeamsForUser($user->id)
                ->where('hackathon_id', $team->hackathon_id)
                ->first();
                
            if ($existingTeam) {
                DB::rollBack();
                return ['success' => false, 'message' => 'User is already in another team for this hackathon'];
            }

            // Check if team is full
            if ($team->isFull()) {
                DB::rollBack();
                return ['success' => false, 'message' => 'Team is already full'];
            }

            $memberData = [
                'user_id' => $user->id,
                'role' => $role,
                'status' => 'pending',
                'joined_at' => now()
            ];

            $member = $this->teamRepository->addMember($team->id, $memberData);
            
            if ($member) {
                DB::commit();
                // TODO: Send invitation email to user
                return ['success' => true, 'message' => 'Team member added successfully'];
            }

            DB::rollBack();
            return ['success' => false, 'message' => 'Failed to add member'];
            
        } catch (\Exception $e) {
            DB::rollBack();
            return ['success' => false, 'message' => 'An error occurred: ' . $e->getMessage()];
        }
    }

    /**
     * Remove a team member
     */
    public function removeTeamMember(Team $team, int $memberId): bool
    {
        // Cannot remove the leader
        if ($team->leader_id === $memberId) {
            return false;
        }

        return $this->teamRepository->removeMember($team->id, $memberId);
    }

    /**
     * Accept a pending member
     */
    public function acceptMember(Team $team, int $memberId): bool
    {
        return $this->teamRepository->acceptMember($team->id, $memberId);
    }

    /**
     * Reject a pending member
     */
    public function rejectMember(Team $team, int $memberId): bool
    {
        return $this->teamRepository->rejectMember($team->id, $memberId);
    }

    /**
     * Submit team idea
     */
    public function submitIdea(Team $team, array $data): array
    {
        DB::beginTransaction();
        try {
            // Check if team already has an idea
            if ($team->idea) {
                // Update existing idea
                $idea = $this->ideaRepository->update($team->idea->id, $data);
            } else {
                // Create new idea
                $data['team_id'] = $team->id;
                $data['track_id'] = $team->track_id;
                $data['edition_id'] = $team->hackathon_id;
                $data['status'] = 'draft';
                $data['submitted_at'] = now();
                
                // Map fields to match database columns
                if (isset($data['solution'])) {
                    $data['solution_approach'] = $data['solution'];
                    unset($data['solution']);
                }
                if (isset($data['target_audience']) || isset($data['unique_value'])) {
                    $data['expected_impact'] = ($data['target_audience'] ?? '') . ' ' . ($data['unique_value'] ?? '');
                    unset($data['target_audience']);
                    unset($data['unique_value']);
                }
                if (isset($data['technologies']) && is_array($data['technologies'])) {
                    $data['technologies'] = json_encode($data['technologies']);
                }
                
                // Remove fields that don't exist in the database
                unset($data['technical_feasibility']);
                unset($data['business_model']);
                
                $idea = $this->ideaRepository->create($data);
            }

            // Update team status
            $this->teamRepository->update($team->id, ['status' => 'submitted']);

            DB::commit();
            return ['success' => true, 'idea' => $idea];
            
        } catch (\Exception $e) {
            DB::rollBack();
            return ['success' => false, 'message' => 'Failed to submit idea: ' . $e->getMessage()];
        }
    }

    /**
     * Update team idea
     */
    public function updateIdea(Idea $idea, array $data): Idea
    {
        // Map fields to match database columns
        if (isset($data['solution'])) {
            $data['solution_approach'] = $data['solution'];
            unset($data['solution']);
        }
        if (isset($data['target_audience']) || isset($data['unique_value'])) {
            $data['expected_impact'] = ($data['target_audience'] ?? '') . ' ' . ($data['unique_value'] ?? '');
            unset($data['target_audience']);
            unset($data['unique_value']);
        }
        if (isset($data['technologies']) && is_array($data['technologies'])) {
            $data['technologies'] = json_encode($data['technologies']);
        }
        
        // Remove fields that don't exist in the database
        unset($data['technical_feasibility']);
        unset($data['business_model']);
        
        return $this->ideaRepository->update($idea->id, $data);
    }

    /**
     * Get team's idea
     */
    public function getTeamIdea(Team $team): ?Idea
    {
        return $team->idea()->with(['files', 'auditLogs'])->first();
    }

    /**
     * Check if user can manage team
     */
    public function canManageTeam(User $user, Team $team): bool
    {
        return $team->leader_id === $user->id;
    }

    /**
     * Get available tracks for team
     */
    public function getAvailableTracks(int $hackathonId): Collection
    {
        return DB::table('tracks')
            ->where('hackathon_edition_id', $hackathonId)
            ->where('status', 'active')
            ->get();
    }

    /**
     * Update team details
     */
    public function updateTeamDetails(Team $team, array $data): Team
    {
        return $this->teamRepository->update($team->id, $data);
    }
}