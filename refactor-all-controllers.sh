#!/bin/bash

echo "Starting complete SystemAdmin controller refactoring..."

# Create all missing repositories
cat > app/Repositories/UserRepository.php << 'EOF'
<?php

namespace App\Repositories;

use App\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class UserRepository extends BaseRepository
{
    public function __construct()
    {
        parent::__construct(new User());
    }

    public function getPaginatedWithFilters(array $filters, int $perPage = 15): LengthAwarePaginator
    {
        $query = $this->query()
            ->with(['roles', 'teams', 'ledTeams'])
            ->withCount(['teams', 'ideas']);

        if (!empty($filters['role'])) {
            $query->whereHas('roles', function ($q) use ($filters) {
                $q->where('name', $filters['role']);
            });
        }

        if (!empty($filters['user_type'])) {
            $query->where('user_type', $filters['user_type']);
        }

        if (!empty($filters['edition_id'])) {
            $query->where('edition_id', $filters['edition_id']);
        }

        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        return $query->latest()->paginate($perPage)->withQueryString();
    }

    public function findWithFullDetails(int $id): ?User
    {
        return $this->query()
            ->with(['roles', 'teams', 'ledTeams', 'ideas'])
            ->find($id);
    }

    public function getStatistics(array $filters = []): array
    {
        $query = $this->query();

        if (!empty($filters['edition_id'])) {
            $query->where('edition_id', $filters['edition_id']);
        }

        return [
            'total' => $query->count(),
            'admins' => (clone $query)->where('user_type', 'system_admin')->count(),
            'team_leaders' => (clone $query)->where('user_type', 'team_leader')->count(),
            'participants' => (clone $query)->where('user_type', 'team_member')->count(),
            'active' => (clone $query)->where('is_active', true)->count(),
        ];
    }

    public function getAvailableForTeam(int $editionId): Collection
    {
        return $this->query()
            ->whereDoesntHave('teams')
            ->whereDoesntHave('ledTeams')
            ->where('edition_id', $editionId)
            ->get();
    }

    public function searchUsers(string $search, array $filters = []): Collection
    {
        $query = $this->query()
            ->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });

        if (!empty($filters['edition_id'])) {
            $query->where('edition_id', $filters['edition_id']);
        }

        return $query->limit(10)->get();
    }
}
EOF

cat > app/Repositories/OrganizationRepository.php << 'EOF'
<?php

namespace App\Repositories;

use App\Models\Organization;
use Illuminate\Pagination\LengthAwarePaginator;

class OrganizationRepository extends BaseRepository
{
    public function __construct()
    {
        parent::__construct(new Organization());
    }

    public function getPaginatedWithFilters(array $filters, int $perPage = 15): LengthAwarePaginator
    {
        $query = $this->query()
            ->withCount(['workshops', 'speakers']);

        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if (!empty($filters['type'])) {
            $query->where('type', $filters['type']);
        }

        return $query->latest()->paginate($perPage)->withQueryString();
    }

    public function findWithFullDetails(int $id): ?Organization
    {
        return $this->query()
            ->with(['workshops', 'speakers'])
            ->find($id);
    }

    public function getStatistics(array $filters = []): array
    {
        $query = $this->query();

        return [
            'total' => $query->count(),
            'sponsors' => (clone $query)->where('type', 'sponsor')->count(),
            'partners' => (clone $query)->where('type', 'partner')->count(),
            'active' => (clone $query)->where('is_active', true)->count(),
        ];
    }
}
EOF

cat > app/Repositories/SpeakerRepository.php << 'EOF'
<?php

namespace App\Repositories;

use App\Models\Speaker;
use Illuminate\Pagination\LengthAwarePaginator;

class SpeakerRepository extends BaseRepository
{
    public function __construct()
    {
        parent::__construct(new Speaker());
    }

    public function getPaginatedWithFilters(array $filters, int $perPage = 15): LengthAwarePaginator
    {
        $query = $this->query()
            ->with(['workshops', 'organization'])
            ->withCount(['workshops']);

        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('bio', 'like', "%{$search}%")
                  ->orWhere('expertise', 'like', "%{$search}%");
            });
        }

        if (!empty($filters['organization_id'])) {
            $query->where('organization_id', $filters['organization_id']);
        }

        return $query->latest()->paginate($perPage)->withQueryString();
    }

    public function findWithFullDetails(int $id): ?Speaker
    {
        return $this->query()
            ->with(['workshops', 'organization'])
            ->find($id);
    }

    public function getStatistics(array $filters = []): array
    {
        $query = $this->query();

        return [
            'total' => $query->count(),
            'active' => (clone $query)->where('is_active', true)->count(),
            'with_workshops' => (clone $query)->has('workshops')->count(),
        ];
    }
}
EOF

cat > app/Repositories/SettingsRepository.php << 'EOF'
<?php

namespace App\Repositories;

use App\Models\SystemSetting;
use Illuminate\Support\Collection;

class SettingsRepository
{
    public function getAll(): Collection
    {
        return SystemSetting::all()->groupBy('group');
    }

    public function getByGroup(string $group): Collection
    {
        return SystemSetting::where('group', $group)->get();
    }

    public function get(string $key, $default = null)
    {
        $setting = SystemSetting::where('key', $key)->first();
        return $setting ? $setting->value : $default;
    }

    public function set(string $key, $value, string $group = 'general'): bool
    {
        return SystemSetting::updateOrCreate(
            ['key' => $key],
            ['value' => $value, 'group' => $group]
        )->exists();
    }

    public function updateBatch(array $settings): bool
    {
        foreach ($settings as $key => $value) {
            SystemSetting::where('key', $key)->update(['value' => $value]);
        }
        return true;
    }
}
EOF

echo "Repositories created."

# Create all services
cat > app/Services/IdeaService.php << 'EOF'
<?php

namespace App\Services;

use App\Models\User;
use App\Repositories\IdeaRepository;
use App\Repositories\HackathonEditionRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class IdeaService extends BaseService
{
    protected IdeaRepository $ideaRepository;
    protected HackathonEditionRepository $editionRepository;

    public function __construct(
        IdeaRepository $ideaRepository,
        HackathonEditionRepository $editionRepository
    ) {
        $this->ideaRepository = $ideaRepository;
        $this->editionRepository = $editionRepository;
    }

    public function getPaginatedIdeas(User $user, array $filters = [], int $perPage = 15): array
    {
        $roleFilters = $this->buildRoleFilters($user, $filters);
        $ideas = $this->ideaRepository->getPaginatedWithFilters($roleFilters, $perPage);
        $statistics = $this->ideaRepository->getStatistics($roleFilters);
        $editions = $this->getEditionsForUser($user);

        return [
            'ideas' => $ideas,
            'statistics' => $statistics,
            'editions' => $editions,
            'filters' => $filters
        ];
    }

    public function getIdeaDetails(int $ideaId, User $user): ?array
    {
        $idea = $this->ideaRepository->findWithFullDetails($ideaId);
        
        if (!$idea || !$this->userCanAccessIdea($user, $idea)) {
            return null;
        }
        
        return [
            'idea' => $idea,
            'permissions' => $this->getIdeaPermissions($user, $idea)
        ];
    }

    public function reviewIdea(int $ideaId, array $reviewData, User $user): array
    {
        $idea = $this->ideaRepository->find($ideaId);
        
        if (!$idea) {
            throw new \Exception('Idea not found.');
        }
        
        if (!$this->userCanReviewIdea($user, $idea)) {
            throw new \Exception('You do not have permission to review this idea.');
        }
        
        DB::beginTransaction();
        try {
            $reviewData['reviewer_id'] = $user->id;
            $review = $this->ideaRepository->addReview($ideaId, $reviewData);
            
            if ($reviewData['status'] === 'approved' || $reviewData['status'] === 'rejected') {
                $this->ideaRepository->updateStatus($ideaId, $reviewData['status']);
            }
            
            Log::info('Idea reviewed', [
                'idea_id' => $ideaId,
                'reviewer_id' => $user->id,
                'status' => $reviewData['status']
            ]);
            
            DB::commit();
            
            return [
                'success' => true,
                'review' => $review,
                'message' => 'Idea reviewed successfully.'
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function deleteIdea(int $ideaId, User $user): array
    {
        $idea = $this->ideaRepository->find($ideaId);
        
        if (!$idea) {
            throw new \Exception('Idea not found.');
        }
        
        if (!$this->userCanDeleteIdea($user, $idea)) {
            throw new \Exception('You do not have permission to delete this idea.');
        }
        
        DB::beginTransaction();
        try {
            $this->ideaRepository->delete($ideaId);
            
            Log::info('Idea deleted', [
                'idea_id' => $ideaId,
                'user_id' => $user->id
            ]);
            
            DB::commit();
            
            return [
                'success' => true,
                'message' => 'Idea deleted successfully.'
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function exportIdeas(User $user, array $filters = []): array
    {
        $roleFilters = $this->buildRoleFilters($user, $filters);
        $ideas = $this->ideaRepository->getForExport($roleFilters);
        
        $csvData = [];
        $csvData[] = ['ID', 'Title', 'Team', 'Track', 'Status', 'Score', 'Submitted At'];
        
        foreach ($ideas as $idea) {
            $csvData[] = [
                $idea->id,
                $idea->title,
                $idea->team->name ?? 'N/A',
                $idea->track->name ?? 'N/A',
                $idea->status,
                $idea->reviews_avg_score ?? 'N/A',
                $idea->submitted_at ? $idea->submitted_at->format('Y-m-d H:i') : 'N/A',
            ];
        }
        
        return [
            'data' => $csvData,
            'filename' => 'ideas-export-' . date('Y-m-d') . '.csv'
        ];
    }

    protected function buildRoleFilters(User $user, array $filters): array
    {
        $roleFilters = $filters;
        
        switch ($user->user_type) {
            case 'hackathon_admin':
                if ($user->edition_id) {
                    $roleFilters['edition_id'] = $user->edition_id;
                }
                break;
                
            case 'track_supervisor':
                $trackIds = DB::table('track_supervisors')
                    ->where('user_id', $user->id)
                    ->pluck('track_id')
                    ->toArray();
                    
                if (!empty($trackIds)) {
                    $roleFilters['track_id'] = $trackIds;
                }
                break;
                
            case 'team_leader':
            case 'team_member':
                $team = $user->team ?? $user->ledTeam;
                if ($team) {
                    $roleFilters['team_id'] = $team->id;
                }
                break;
        }
        
        return $roleFilters;
    }

    protected function getEditionsForUser(User $user): \Illuminate\Support\Collection
    {
        switch ($user->user_type) {
            case 'system_admin':
                return $this->editionRepository->all();
            case 'hackathon_admin':
                if ($user->edition_id) {
                    return collect([$this->editionRepository->find($user->edition_id)]);
                }
                return collect();
            default:
                return collect();
        }
    }

    protected function userCanAccessIdea(User $user, $idea): bool
    {
        switch ($user->user_type) {
            case 'system_admin':
                return true;
            case 'hackathon_admin':
                return $idea->team && $user->edition_id == $idea->team->edition_id;
            case 'track_supervisor':
                $trackIds = DB::table('track_supervisors')
                    ->where('user_id', $user->id)
                    ->pluck('track_id')
                    ->toArray();
                return in_array($idea->track_id, $trackIds);
            case 'team_leader':
            case 'team_member':
                return $idea->team_id == ($user->team_id ?? $user->ledTeam?->id);
            default:
                return false;
        }
    }

    protected function userCanReviewIdea(User $user, $idea): bool
    {
        if (!$this->userCanAccessIdea($user, $idea)) {
            return false;
        }
        
        return in_array($user->user_type, ['system_admin', 'hackathon_admin', 'track_supervisor']);
    }

    protected function userCanDeleteIdea(User $user, $idea): bool
    {
        return $user->user_type === 'system_admin';
    }

    protected function getIdeaPermissions(User $user, $idea): array
    {
        return [
            'canReview' => $this->userCanReviewIdea($user, $idea),
            'canDelete' => $this->userCanDeleteIdea($user, $idea),
            'canExport' => true,
        ];
    }
}
EOF

cat > app/Services/UserService.php << 'EOF'
<?php

namespace App\Services;

use App\Models\User;
use App\Repositories\UserRepository;
use App\Repositories\HackathonEditionRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class UserService extends BaseService
{
    protected UserRepository $userRepository;
    protected HackathonEditionRepository $editionRepository;

    public function __construct(
        UserRepository $userRepository,
        HackathonEditionRepository $editionRepository
    ) {
        $this->userRepository = $userRepository;
        $this->editionRepository = $editionRepository;
    }

    public function getPaginatedUsers(User $user, array $filters = [], int $perPage = 15): array
    {
        $roleFilters = $this->buildRoleFilters($user, $filters);
        $users = $this->userRepository->getPaginatedWithFilters($roleFilters, $perPage);
        $statistics = $this->userRepository->getStatistics($roleFilters);
        $editions = $this->getEditionsForUser($user);

        return [
            'users' => $users,
            'statistics' => $statistics,
            'editions' => $editions,
            'filters' => $filters
        ];
    }

    public function getUserDetails(int $userId, User $user): ?array
    {
        $targetUser = $this->userRepository->findWithFullDetails($userId);
        
        if (!$targetUser || !$this->userCanAccessUser($user, $targetUser)) {
            return null;
        }
        
        return [
            'user' => $targetUser,
            'permissions' => $this->getUserPermissions($user, $targetUser)
        ];
    }

    public function createUser(array $data, User $user): array
    {
        if (!$this->userCanCreateUser($user)) {
            throw new \Exception('You do not have permission to create users.');
        }
        
        DB::beginTransaction();
        try {
            if (isset($data['password'])) {
                $data['password'] = Hash::make($data['password']);
            }
            
            $newUser = $this->userRepository->create($data);
            
            Log::info('User created', [
                'user_id' => $newUser->id,
                'created_by' => $user->id
            ]);
            
            DB::commit();
            
            return [
                'success' => true,
                'user' => $newUser,
                'message' => 'User created successfully.'
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function updateUser(int $userId, array $data, User $user): array
    {
        $targetUser = $this->userRepository->find($userId);
        
        if (!$targetUser) {
            throw new \Exception('User not found.');
        }
        
        if (!$this->userCanEditUser($user, $targetUser)) {
            throw new \Exception('You do not have permission to edit this user.');
        }
        
        DB::beginTransaction();
        try {
            if (isset($data['password']) && !empty($data['password'])) {
                $data['password'] = Hash::make($data['password']);
            } else {
                unset($data['password']);
            }
            
            $this->userRepository->update($userId, $data);
            $targetUser = $this->userRepository->findWithFullDetails($userId);
            
            Log::info('User updated', [
                'user_id' => $userId,
                'updated_by' => $user->id
            ]);
            
            DB::commit();
            
            return [
                'success' => true,
                'user' => $targetUser,
                'message' => 'User updated successfully.'
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function deleteUser(int $userId, User $user): array
    {
        $targetUser = $this->userRepository->find($userId);
        
        if (!$targetUser) {
            throw new \Exception('User not found.');
        }
        
        if (!$this->userCanDeleteUser($user, $targetUser)) {
            throw new \Exception('You do not have permission to delete this user.');
        }
        
        if ($targetUser->id === $user->id) {
            throw new \Exception('You cannot delete your own account.');
        }
        
        DB::beginTransaction();
        try {
            $this->userRepository->delete($userId);
            
            Log::info('User deleted', [
                'user_id' => $userId,
                'deleted_by' => $user->id
            ]);
            
            DB::commit();
            
            return [
                'success' => true,
                'message' => 'User deleted successfully.'
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function searchUsers(string $search, User $user): array
    {
        $filters = [];
        
        if ($user->user_type === 'hackathon_admin' && $user->edition_id) {
            $filters['edition_id'] = $user->edition_id;
        }
        
        $users = $this->userRepository->searchUsers($search, $filters);
        
        return [
            'users' => $users
        ];
    }

    protected function buildRoleFilters(User $user, array $filters): array
    {
        $roleFilters = $filters;
        
        switch ($user->user_type) {
            case 'hackathon_admin':
                if ($user->edition_id) {
                    $roleFilters['edition_id'] = $user->edition_id;
                }
                break;
        }
        
        return $roleFilters;
    }

    protected function getEditionsForUser(User $user): \Illuminate\Support\Collection
    {
        switch ($user->user_type) {
            case 'system_admin':
                return $this->editionRepository->all();
            case 'hackathon_admin':
                if ($user->edition_id) {
                    return collect([$this->editionRepository->find($user->edition_id)]);
                }
                return collect();
            default:
                return collect();
        }
    }

    protected function userCanAccessUser(User $user, User $targetUser): bool
    {
        switch ($user->user_type) {
            case 'system_admin':
                return true;
            case 'hackathon_admin':
                return $user->edition_id == $targetUser->edition_id;
            default:
                return $user->id === $targetUser->id;
        }
    }

    protected function userCanCreateUser(User $user): bool
    {
        return in_array($user->user_type, ['system_admin', 'hackathon_admin']);
    }

    protected function userCanEditUser(User $user, User $targetUser): bool
    {
        if (!$this->userCanAccessUser($user, $targetUser)) {
            return false;
        }
        
        return in_array($user->user_type, ['system_admin', 'hackathon_admin']) || 
               $user->id === $targetUser->id;
    }

    protected function userCanDeleteUser(User $user, User $targetUser): bool
    {
        return $user->user_type === 'system_admin' && $user->id !== $targetUser->id;
    }

    protected function getUserPermissions(User $user, User $targetUser): array
    {
        return [
            'canEdit' => $this->userCanEditUser($user, $targetUser),
            'canDelete' => $this->userCanDeleteUser($user, $targetUser),
            'canChangeRole' => $user->user_type === 'system_admin',
        ];
    }
}
EOF

cat > app/Services/OrganizationService.php << 'EOF'
<?php

namespace App\Services;

use App\Models\User;
use App\Repositories\OrganizationRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class OrganizationService extends BaseService
{
    protected OrganizationRepository $organizationRepository;

    public function __construct(OrganizationRepository $organizationRepository)
    {
        $this->organizationRepository = $organizationRepository;
    }

    public function getPaginatedOrganizations(User $user, array $filters = [], int $perPage = 15): array
    {
        if (!$this->userCanAccessOrganizations($user)) {
            throw new \Exception('You do not have permission to view organizations.');
        }

        $organizations = $this->organizationRepository->getPaginatedWithFilters($filters, $perPage);
        $statistics = $this->organizationRepository->getStatistics($filters);

        return [
            'organizations' => $organizations,
            'statistics' => $statistics,
            'filters' => $filters
        ];
    }

    public function getOrganizationDetails(int $organizationId, User $user): ?array
    {
        if (!$this->userCanAccessOrganizations($user)) {
            return null;
        }

        $organization = $this->organizationRepository->findWithFullDetails($organizationId);
        
        if (!$organization) {
            return null;
        }
        
        return [
            'organization' => $organization,
            'permissions' => $this->getOrganizationPermissions($user)
        ];
    }

    public function createOrganization(array $data, User $user): array
    {
        if (!$this->userCanManageOrganizations($user)) {
            throw new \Exception('You do not have permission to create organizations.');
        }
        
        DB::beginTransaction();
        try {
            $organization = $this->organizationRepository->create($data);
            
            Log::info('Organization created', [
                'organization_id' => $organization->id,
                'user_id' => $user->id
            ]);
            
            DB::commit();
            
            return [
                'success' => true,
                'organization' => $organization,
                'message' => 'Organization created successfully.'
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function updateOrganization(int $organizationId, array $data, User $user): array
    {
        if (!$this->userCanManageOrganizations($user)) {
            throw new \Exception('You do not have permission to update organizations.');
        }

        $organization = $this->organizationRepository->find($organizationId);
        
        if (!$organization) {
            throw new \Exception('Organization not found.');
        }
        
        DB::beginTransaction();
        try {
            $this->organizationRepository->update($organizationId, $data);
            $organization = $this->organizationRepository->findWithFullDetails($organizationId);
            
            Log::info('Organization updated', [
                'organization_id' => $organizationId,
                'user_id' => $user->id
            ]);
            
            DB::commit();
            
            return [
                'success' => true,
                'organization' => $organization,
                'message' => 'Organization updated successfully.'
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function deleteOrganization(int $organizationId, User $user): array
    {
        if (!$this->userCanManageOrganizations($user)) {
            throw new \Exception('You do not have permission to delete organizations.');
        }

        $organization = $this->organizationRepository->find($organizationId);
        
        if (!$organization) {
            throw new \Exception('Organization not found.');
        }
        
        DB::beginTransaction();
        try {
            $this->organizationRepository->delete($organizationId);
            
            Log::info('Organization deleted', [
                'organization_id' => $organizationId,
                'user_id' => $user->id
            ]);
            
            DB::commit();
            
            return [
                'success' => true,
                'message' => 'Organization deleted successfully.'
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    protected function userCanAccessOrganizations(User $user): bool
    {
        return in_array($user->user_type, ['system_admin', 'hackathon_admin']);
    }

    protected function userCanManageOrganizations(User $user): bool
    {
        return $user->user_type === 'system_admin';
    }

    protected function getOrganizationPermissions(User $user): array
    {
        return [
            'canCreate' => $this->userCanManageOrganizations($user),
            'canEdit' => $this->userCanManageOrganizations($user),
            'canDelete' => $this->userCanManageOrganizations($user),
        ];
    }
}
EOF

cat > app/Services/SpeakerService.php << 'EOF'
<?php

namespace App\Services;

use App\Models\User;
use App\Repositories\SpeakerRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SpeakerService extends BaseService
{
    protected SpeakerRepository $speakerRepository;

    public function __construct(SpeakerRepository $speakerRepository)
    {
        $this->speakerRepository = $speakerRepository;
    }

    public function getPaginatedSpeakers(User $user, array $filters = [], int $perPage = 15): array
    {
        if (!$this->userCanAccessSpeakers($user)) {
            throw new \Exception('You do not have permission to view speakers.');
        }

        $speakers = $this->speakerRepository->getPaginatedWithFilters($filters, $perPage);
        $statistics = $this->speakerRepository->getStatistics($filters);

        return [
            'speakers' => $speakers,
            'statistics' => $statistics,
            'filters' => $filters
        ];
    }

    public function getSpeakerDetails(int $speakerId, User $user): ?array
    {
        if (!$this->userCanAccessSpeakers($user)) {
            return null;
        }

        $speaker = $this->speakerRepository->findWithFullDetails($speakerId);
        
        if (!$speaker) {
            return null;
        }
        
        return [
            'speaker' => $speaker,
            'permissions' => $this->getSpeakerPermissions($user)
        ];
    }

    public function createSpeaker(array $data, User $user): array
    {
        if (!$this->userCanManageSpeakers($user)) {
            throw new \Exception('You do not have permission to create speakers.');
        }
        
        DB::beginTransaction();
        try {
            $speaker = $this->speakerRepository->create($data);
            
            Log::info('Speaker created', [
                'speaker_id' => $speaker->id,
                'user_id' => $user->id
            ]);
            
            DB::commit();
            
            return [
                'success' => true,
                'speaker' => $speaker,
                'message' => 'Speaker created successfully.'
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function updateSpeaker(int $speakerId, array $data, User $user): array
    {
        if (!$this->userCanManageSpeakers($user)) {
            throw new \Exception('You do not have permission to update speakers.');
        }

        $speaker = $this->speakerRepository->find($speakerId);
        
        if (!$speaker) {
            throw new \Exception('Speaker not found.');
        }
        
        DB::beginTransaction();
        try {
            $this->speakerRepository->update($speakerId, $data);
            $speaker = $this->speakerRepository->findWithFullDetails($speakerId);
            
            Log::info('Speaker updated', [
                'speaker_id' => $speakerId,
                'user_id' => $user->id
            ]);
            
            DB::commit();
            
            return [
                'success' => true,
                'speaker' => $speaker,
                'message' => 'Speaker updated successfully.'
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function deleteSpeaker(int $speakerId, User $user): array
    {
        if (!$this->userCanManageSpeakers($user)) {
            throw new \Exception('You do not have permission to delete speakers.');
        }

        $speaker = $this->speakerRepository->find($speakerId);
        
        if (!$speaker) {
            throw new \Exception('Speaker not found.');
        }
        
        DB::beginTransaction();
        try {
            $this->speakerRepository->delete($speakerId);
            
            Log::info('Speaker deleted', [
                'speaker_id' => $speakerId,
                'user_id' => $user->id
            ]);
            
            DB::commit();
            
            return [
                'success' => true,
                'message' => 'Speaker deleted successfully.'
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    protected function userCanAccessSpeakers(User $user): bool
    {
        return in_array($user->user_type, ['system_admin', 'hackathon_admin']);
    }

    protected function userCanManageSpeakers(User $user): bool
    {
        return $user->user_type === 'system_admin';
    }

    protected function getSpeakerPermissions(User $user): array
    {
        return [
            'canCreate' => $this->userCanManageSpeakers($user),
            'canEdit' => $this->userCanManageSpeakers($user),
            'canDelete' => $this->userCanManageSpeakers($user),
        ];
    }
}
EOF

cat > app/Services/SettingsService.php << 'EOF'
<?php

namespace App\Services;

use App\Models\User;
use App\Repositories\SettingsRepository;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SettingsService
{
    protected SettingsRepository $settingsRepository;

    public function __construct(SettingsRepository $settingsRepository)
    {
        $this->settingsRepository = $settingsRepository;
    }

    public function getAllSettings(User $user): array
    {
        if (!$this->userCanManageSettings($user)) {
            throw new \Exception('You do not have permission to view settings.');
        }

        return [
            'settings' => $this->settingsRepository->getAll(),
            'groups' => ['general', 'smtp', 'branding', 'notifications', 'sms', 'twitter']
        ];
    }

    public function getSettingsByGroup(string $group, User $user): array
    {
        if (!$this->userCanManageSettings($user)) {
            throw new \Exception('You do not have permission to view settings.');
        }

        return [
            'settings' => $this->settingsRepository->getByGroup($group)
        ];
    }

    public function updateSettings(array $settings, User $user): array
    {
        if (!$this->userCanManageSettings($user)) {
            throw new \Exception('You do not have permission to update settings.');
        }
        
        DB::beginTransaction();
        try {
            $this->settingsRepository->updateBatch($settings);
            
            // Clear cache
            Cache::flush();
            
            Log::info('Settings updated', [
                'user_id' => $user->id,
                'settings' => array_keys($settings)
            ]);
            
            DB::commit();
            
            return [
                'success' => true,
                'message' => 'Settings updated successfully.'
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function updateSettingGroup(string $group, array $settings, User $user): array
    {
        if (!$this->userCanManageSettings($user)) {
            throw new \Exception('You do not have permission to update settings.');
        }
        
        DB::beginTransaction();
        try {
            foreach ($settings as $key => $value) {
                $this->settingsRepository->set($key, $value, $group);
            }
            
            // Clear cache
            Cache::flush();
            
            Log::info('Settings group updated', [
                'user_id' => $user->id,
                'group' => $group
            ]);
            
            DB::commit();
            
            return [
                'success' => true,
                'message' => ucfirst($group) . ' settings updated successfully.'
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    protected function userCanManageSettings(User $user): bool
    {
        return $user->user_type === 'system_admin';
    }
}
EOF

echo "Services created. Now refactoring controllers..."

chmod +x refactor-all-controllers.sh