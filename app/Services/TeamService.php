<?php

namespace App\Services;

use App\Repositories\TeamRepository;
use App\Repositories\IdeaRepository;
use App\Repositories\UserRepository;
use App\Models\Team;
use App\Models\User;
use App\Models\Idea;
use App\Mail\TeamInvitation;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

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
     * Get paginated teams
     */
    public function getPaginatedTeams($user, array $filters = [], int $perPage = 15)
    {
        return [
            'teams' => $this->teamRepository->getPaginatedWithFilters($filters, $perPage),
            'filters' => $filters
        ];
    }

    /**
     * Get team details
     */
    public function getTeamDetails(int $id, $user)
    {
        $team = $this->teamRepository->find($id);
        if ($team) {
            $team->load(['leader', 'members', 'idea', 'edition']);
        }
        return ['team' => $team];
    }

    /**
     * Create team
     */
    public function createTeam(array $data, $user)
    {
        DB::beginTransaction();
        try {
            $team = $this->teamRepository->create([
                'name' => $data['name'],
                'description' => $data['description'] ?? null,
                'edition_id' => $data['edition_id'],
                'leader_id' => $data['leader_id'] ?? null,
                'max_members' => $data['max_members'] ?? 5,
                'status' => 'active'
            ]);

            // Add leader as a member if specified
            if (!empty($data['leader_id'])) {
                $team->members()->attach($data['leader_id'], ['role' => 'leader']);
            }

            // Add other members
            if (!empty($data['member_ids'])) {
                foreach ($data['member_ids'] as $memberId) {
                    if ($memberId != $data['leader_id']) {
                        $team->members()->attach($memberId, ['role' => 'member']);
                    }
                }
            }

            DB::commit();
            return $team;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Update team
     */
    public function updateTeam(int $id, array $data, $user)
    {
        return $this->teamRepository->update($id, $data);
    }

    /**
     * Delete team
     */
    public function deleteTeam(int $id, $user)
    {
        return $this->teamRepository->delete($id);
    }

    /**
     * Get the team led by the user
     */
    public function getMyTeam(User $user): ?Team
    {
        return $this->teamRepository->findByLeaderId($user->id);
    }

    /**
     * Get the team that a user is a member of (for team members)
     */
    public function getMemberTeam($userId): ?Team
    {
        // First check if user is a team leader
        $team = $this->teamRepository->findByLeaderId($userId);
        if ($team) {
            return $team;
        }

        // Then check if user is a team member
        $user = User::find($userId);
        if ($user && $user->team_id) {
            return Team::find($user->team_id);
        }

        // Finally check team_members table
        $teamMember = DB::table('team_members')
            ->where('user_id', $userId)
            ->where('status', 'accepted')
            ->first();
            
        if ($teamMember) {
            return Team::find($teamMember->team_id);
        }

        return null;
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
        // Get team members from the members relationship
        $members = $team->members()->get()->map(function ($member) {
            return [
                'id' => $member->id,
                'name' => $member->name,
                'email' => $member->email,
                'status' => $member->pivot->status ?? 'accepted',
                'role' => $member->pivot->role ?? 'member',
                'joined_at' => $member->pivot->joined_at ?? $member->pivot->created_at,
            ];
        });

        // Include the team leader only if not already in members
        if ($team->leader) {
            $leaderInMembers = $members->firstWhere('id', $team->leader->id);
            if (!$leaderInMembers) {
                $leader = [
                    'id' => $team->leader->id,
                    'name' => $team->leader->name,
                    'email' => $team->leader->email,
                    'status' => 'accepted',
                    'role' => 'leader',
                    'joined_at' => $team->created_at,
                ];
                $members->prepend($leader);
            } else {
                // Update the role to leader if they're in the members list
                $members = $members->map(function ($member) use ($team) {
                    if ($member['id'] === $team->leader->id) {
                        $member['role'] = 'leader';
                    }
                    return $member;
                });
            }
        }

        return $members;
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
     * Add a new team member with invitation
     */
    public function addMemberWithInvitation(Team $team, array $data): array
    {
        DB::beginTransaction();
        try {
            // Check if user with this email already exists
            $user = $this->userRepository->findByEmail($data['email']);

            if (!$user) {
                // Create new user if they don't exist
                $userData = [
                    'name' => $data['name'],
                    'email' => $data['email'],
                    'phone' => $data['phone'] ?? null,
                    'password' => bcrypt(Str::random(12)), // Generate random password
                    'role' => 'team_member',
                    'email_verified_at' => null
                ];

                $user = $this->userRepository->create($userData);
            } else {
                // Update existing user's phone if provided
                if (!empty($data['phone'])) {
                    $this->userRepository->update($user->id, ['phone' => $data['phone']]);
                }
            }

            // Check if user is already in any team for this edition
            $existingTeam = DB::table('team_members')
                ->join('teams', 'teams.id', '=', 'team_members.team_id')
                ->where('team_members.user_id', $user->id)
                ->where('teams.edition_id', $team->edition_id)
                ->first();

            if ($existingTeam) {
                // Remove from previous team if different
                if ($existingTeam->team_id !== $team->id) {
                    DB::table('team_members')
                        ->where('team_id', $existingTeam->team_id)
                        ->where('user_id', $user->id)
                        ->delete();
                } else {
                    DB::rollBack();
                    return ['success' => false, 'message' => 'User is already a member of this team.'];
                }
            }

            // Check if team is full
            $currentMembers = DB::table('team_members')
                ->where('team_id', $team->id)
                ->count();

            if ($currentMembers >= $team->max_members) {
                DB::rollBack();
                return ['success' => false, 'message' => 'Team is already at maximum capacity.'];
            }

            // Add member to team
            $memberData = [
                'user_id' => $user->id,
                'role' => 'member',
                'status' => 'accepted',
                'joined_at' => now()
            ];

            $this->teamRepository->addMember($team->id, $memberData);

            // Send invitation email if requested
            if (!empty($data['send_invitation']) && $data['send_invitation']) {
                try {
                    Mail::to($user->email)->send(new TeamInvitation($user, $team));
                } catch (\Exception $e) {
                    Log::error('Failed to send team invitation email: ' . $e->getMessage());
                    // Don't fail the whole operation if email fails
                }
            }

            DB::commit();
            return [
                'success' => true,
                'message' => $data['send_invitation'] ? 'Member added and invitation sent successfully.' : 'Member added successfully.',
                'user' => $user
            ];

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error adding team member: ' . $e->getMessage());
            return ['success' => false, 'message' => 'An error occurred while adding the member.'];
        }
    }

    /**
     * Remove a team member
     */
    public function removeTeamMember(Team $team, string $memberId): bool
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
    public function acceptMember(Team $team, string $memberId): bool
    {
        return $this->teamRepository->acceptMember($team->id, $memberId);
    }

    /**
     * Reject a pending member
     */
    public function rejectMember(Team $team, string $memberId): bool
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