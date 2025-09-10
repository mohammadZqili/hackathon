<?php

namespace App\Services;

use App\Models\Team;
use App\Models\User;
use App\Repositories\TeamRepository;
use App\Repositories\HackathonRepository;
use App\Services\Contracts\TeamServiceInterface;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class TeamService implements TeamServiceInterface
{
    public function __construct(
        private TeamRepository $teamRepo,
        private HackathonRepository $hackathonRepo
    ) {}

    /**
     * Create a new team with leader (for team leaders).
     */
    public function createTeamWithLeader(array $data, User $leader): Team
    {
        return DB::transaction(function () use ($data, $leader) {
            // Validate hackathon is open for registration
            $hackathon = $this->hackathonRepo->find($data['hackathon_id']);
            
            if (!$hackathon || !$hackathon->isRegistrationOpen()) {
                throw new \Exception('التسجيل مغلق حالياً');
            }

            // Check if user already leads a team in this hackathon
            $existingTeam = $this->teamRepo->findByQuery([
                'leader_id' => $leader->id,
                'hackathon_id' => $hackathon->id
            ]);
            
            if ($existingTeam) {
                throw new \Exception('لديك فريق بالفعل في هذا الهاكاثون');
            }

            // Create team
            $data['leader_id'] = $leader->id;
            $data['invite_code'] = Str::upper(Str::random(8));
            $data['status'] = 'active';
            $data['max_members'] = $data['max_members'] ?? 5;
            
            $team = $this->teamRepo->create($data);
            
            // Add leader as first member with accepted status
            $team->members()->create([
                'user_id' => $leader->id,
                'status' => 'accepted',
                'role' => 'leader',
                'joined_at' => now(),
            ]);
            
            // Update user type if needed
            if ($leader->user_type === 'visitor') {
                $leader->update(['user_type' => 'team_leader']);
            }
            
            Log::info('Team created', [
                'team_id' => $team->id,
                'leader_id' => $leader->id,
                'hackathon_id' => $hackathon->id,
            ]);
            
            return $team;
        });
    }

    /**
     * Join team using invite code.
     */
    public function joinTeamByCode(string $code, User $user): array
    {
        $team = $this->teamRepo->findByQuery(['invite_code' => $code]);
        
        if (!$team) {
            return ['success' => false, 'message' => 'كود الدعوة غير صحيح'];
        }

        // Check if user can join the team
        $canJoin = $this->canUserJoinTeam($team, $user);
        if (!$canJoin['can_join']) {
            return ['success' => false, 'message' => $canJoin['reason']];
        }

        // Check if already requested to join
        $existingMember = $team->members()->where('user_id', $user->id)->first();
        if ($existingMember) {
            if ($existingMember->status === 'pending') {
                return ['success' => false, 'message' => 'طلبك قيد المراجعة'];
            } elseif ($existingMember->status === 'accepted') {
                return ['success' => false, 'message' => 'أنت عضو في هذا الفريق بالفعل'];
            }
        }

        // Add as pending member
        $team->members()->create([
            'user_id' => $user->id,
            'status' => 'pending',
            'role' => 'member',
            'joined_at' => null,
        ]);
        
        Log::info('Team join request', [
            'team_id' => $team->id,
            'user_id' => $user->id,
        ]);
        
        return ['success' => true, 'message' => 'تم إرسال طلب الانضمام', 'team' => $team];
    }

    /**
     * Accept a member to the team.
     */
    public function acceptMember(int $teamId, int $userId, User $leader): bool
    {
        $team = $this->teamRepo->find($teamId);
        
        if (!$team || $team->leader_id !== $leader->id) {
            throw new \Exception('غير مصرح لك بهذا الإجراء');
        }

        // Check team capacity
        $acceptedCount = $team->members()->where('status', 'accepted')->count();
        if ($acceptedCount >= $team->max_members) {
            throw new \Exception('الفريق ممتلئ');
        }

        $member = $team->members()->where('user_id', $userId)->first();
        if (!$member || $member->status !== 'pending') {
            throw new \Exception('طلب الانضمام غير موجود');
        }

        $member->update([
            'status' => 'accepted',
            'joined_at' => now(),
        ]);
        
        // Update user type if needed
        $user = User::find($userId);
        if ($user && $user->user_type === 'visitor') {
            $user->update(['user_type' => 'team_member']);
        }
        
        Log::info('Team member accepted', [
            'team_id' => $teamId,
            'user_id' => $userId,
            'leader_id' => $leader->id,
        ]);
        
        return true;
    }

    /**
     * Reject a member from joining the team.
     */
    public function rejectMember(int $teamId, int $userId, User $leader): bool
    {
        $team = $this->teamRepo->find($teamId);
        
        if (!$team || $team->leader_id !== $leader->id) {
            throw new \Exception('غير مصرح لك بهذا الإجراء');
        }

        $member = $team->members()->where('user_id', $userId)->first();
        if (!$member || $member->status !== 'pending') {
            throw new \Exception('طلب الانضمام غير موجود');
        }

        $member->delete();
        
        Log::info('Team member rejected', [
            'team_id' => $teamId,
            'user_id' => $userId,
            'leader_id' => $leader->id,
        ]);
        
        return true;
    }

    /**
     * Remove a member from the team.
     */
    public function removeFromTeam(int $teamId, int $userId, User $leader): bool
    {
        $team = $this->teamRepo->find($teamId);
        
        if (!$team || $team->leader_id !== $leader->id) {
            throw new \Exception('غير مصرح لك بهذا الإجراء');
        }

        // Cannot remove the leader
        if ($userId === $leader->id) {
            throw new \Exception('لا يمكن إزالة قائد الفريق');
        }

        $member = $team->members()->where('user_id', $userId)->first();
        if (!$member) {
            throw new \Exception('العضو غير موجود في الفريق');
        }

        $member->delete();
        
        Log::info('Team member removed', [
            'team_id' => $teamId,
            'user_id' => $userId,
            'leader_id' => $leader->id,
        ]);
        
        return true;
    }

    /**
     * Get all teams that user is part of.
     */
    public function getUserTeams(User $user): Collection
    {
        return $this->teamRepo->getTeamsForUser($user->id);
    }

    /**
     * Check if user can create a team.
     */
    public function canUserCreateTeam(User $user): bool
    {
        // Check if current hackathon is open
        $currentHackathon = $this->hackathonRepo->findByQuery(['is_current' => true]);
        
        if (!$currentHackathon || !$currentHackathon->isRegistrationOpen()) {
            return false;
        }

        // Check if user already has a team in current hackathon
        $existingTeam = $this->teamRepo->findByQuery([
            'leader_id' => $user->id,
            'hackathon_id' => $currentHackathon->id
        ]);
        
        return !$existingTeam;
    }

    /**
     * Regenerate team invite code.
     */
    public function regenerateInviteCode(int $teamId, User $leader): string
    {
        $team = $this->teamRepo->find($teamId);
        
        if (!$team || $team->leader_id !== $leader->id) {
            throw new \Exception('غير مصرح لك بهذا الإجراء');
        }

        $newCode = Str::upper(Str::random(8));
        $this->teamRepo->update($teamId, ['invite_code' => $newCode]);
        
        Log::info('Team invite code regenerated', [
            'team_id' => $teamId,
            'leader_id' => $leader->id,
        ]);
        
        return $newCode;
    }

    /**
     * Get team statistics.
     */
    public function getTeamStatistics(int $teamId): array
    {
        $team = $this->teamRepo->findOrFail($teamId);
        
        $members = $team->members;
        $idea = $team->idea;
        
        return [
            'member_count' => [
                'total' => $members->count(),
                'accepted' => $members->where('status', 'accepted')->count(),
                'pending' => $members->where('status', 'pending')->count(),
            ],
            'idea_status' => $idea ? $idea->status : 'not_started',
            'capacity' => [
                'max' => $team->max_members,
                'available' => $team->max_members - $members->where('status', 'accepted')->count(),
            ],
            'timeline' => [
                'created_at' => $team->created_at,
                'can_submit_idea' => $this->canTeamSubmitIdea($team),
            ],
        ];
    }

    /**
     * Check if user can join a specific team.
     */
    private function canUserJoinTeam(Team $team, User $user): array
    {
        // Check if hackathon registration is open
        if (!$team->hackathon->isRegistrationOpen()) {
            return ['can_join' => false, 'reason' => 'التسجيل مغلق حالياً'];
        }

        // Check if user already in a team for this hackathon
        $existingMembership = $this->teamRepo->getUserTeamInHackathon($user->id, $team->hackathon_id);
        if ($existingMembership) {
            return ['can_join' => false, 'reason' => 'أنت عضو في فريق آخر بالفعل'];
        }

        // Check team capacity
        $acceptedCount = $team->members()->where('status', 'accepted')->count();
        if ($acceptedCount >= $team->max_members) {
            return ['can_join' => false, 'reason' => 'الفريق ممتلئ'];
        }

        return ['can_join' => true, 'reason' => null];
    }

    /**
     * Check if team can submit idea.
     */
    private function canTeamSubmitIdea(Team $team): bool
    {
        return $team->hackathon->isIdeaSubmissionOpen() && 
               $team->status === 'active' &&
               $team->members()->where('status', 'accepted')->count() >= 2;
    }

    /**
     * Create a team (for admins).
     */
    public function createTeam(array $data): Team
    {
        return DB::transaction(function () use ($data) {
            // Generate invite code if not provided
            if (!isset($data['invite_code'])) {
                $data['invite_code'] = Str::upper(Str::random(8));
            }

            // Set default values
            $data['status'] = $data['status'] ?? 'pending';
            $data['max_members'] = $data['max_members'] ?? 5;
            
            $team = $this->teamRepo->create($data);
            
            // If leader is specified, add them as the first member
            if (isset($data['leader_id'])) {
                $team->members()->create([
                    'user_id' => $data['leader_id'],
                    'status' => 'accepted',
                    'role' => 'leader',
                    'joined_at' => now(),
                ]);
            }
            
            Log::info('Team created by admin', [
                'team_id' => $team->id,
                'edition_id' => $data['edition_id'] ?? null,
            ]);
            
            return $team;
        });
    }

    /**
     * Update a team.
     */
    public function updateTeam(int $teamId, array $data): Team
    {
        $team = $this->teamRepo->update($teamId, $data);
        
        Log::info('Team updated', [
            'team_id' => $teamId,
            'updated_data' => array_keys($data),
        ]);
        
        return $team;
    }

    /**
     * Approve a team.
     */
    public function approveTeam(int $teamId): Team
    {
        $team = $this->teamRepo->update($teamId, [
            'status' => 'approved',
            'approved_at' => now(),
        ]);
        
        Log::info('Team approved', ['team_id' => $teamId]);
        
        // TODO: Send notification to team leader
        
        return $team;
    }

    /**
     * Reject a team.
     */
    public function rejectTeam(int $teamId, string $reason): Team
    {
        $team = $this->teamRepo->update($teamId, [
            'status' => 'rejected',
            'rejection_reason' => $reason,
            'rejected_at' => now(),
        ]);
        
        Log::info('Team rejected', [
            'team_id' => $teamId,
            'reason' => $reason,
        ]);
        
        // TODO: Send notification to team leader with reason
        
        return $team;
    }

    /**
     * Delete a team.
     */
    public function deleteTeam(int $teamId): bool
    {
        DB::transaction(function () use ($teamId) {
            $team = $this->teamRepo->find($teamId);
            
            // Delete team members
            $team->members()->delete();
            
            // Delete team
            $this->teamRepo->delete($teamId);
            
            Log::info('Team deleted', ['team_id' => $teamId]);
        });
        
        return true;
    }

    /**
     * Get team statistics for an edition.
     */
    public function getTeamStatistics(int $editionId): array
    {
        $teams = Team::where('edition_id', $editionId)->get();
        
        return [
            'total' => $teams->count(),
            'pending' => $teams->where('status', 'pending')->count(),
            'approved' => $teams->where('status', 'approved')->count(),
            'rejected' => $teams->where('status', 'rejected')->count(),
            'with_ideas' => $teams->whereNotNull('idea_id')->count(),
            'average_members' => $teams->avg(function ($team) {
                return $team->members()->where('status', 'accepted')->count();
            }),
        ];
    }
}