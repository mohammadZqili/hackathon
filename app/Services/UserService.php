<?php

namespace App\Services;

use App\Models\User;
use App\Repositories\UserRepository;
use App\Repositories\HackathonEditionRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Collection;

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

    /**
     * Get paginated users based on user role and filters
     */
    public function getPaginatedUsers(User $user, array $filters = [], int $perPage = 15): array
    {
        // Build filters based on user role
        $roleFilters = $this->buildRoleFilters($user, $filters);
        
        // Get paginated users
        $users = $this->userRepository->getPaginatedWithFilters($roleFilters, $perPage);
        
        // Get statistics
        $statistics = $this->userRepository->getStatistics($roleFilters);
        
        // Get editions for filter dropdown
        $editions = $this->getEditionsForUser($user);
        
        return [
            'users' => $users,
            'statistics' => $statistics,
            'editions' => $editions,
            'filters' => $filters
        ];
    }

    /**
     * Get user details
     */
    public function getUserDetails(string $userId, User $currentUser): ?array
    {
        $user = $this->userRepository->findWithFullDetails($userId);
        
        if (!$user) {
            return null;
        }
        
        // Check if user has access to this user
        if (!$this->userCanAccessUser($currentUser, $user)) {
            return null;
        }
        
        return [
            'user' => $user,
            'permissions' => $this->getUserPermissions($currentUser, $user)
        ];
    }

    /**
     * Create a new user
     */
    public function createUser(array $data, User $currentUser): array
    {
        // Check permissions
        if (!$this->userCanCreateUser($currentUser)) {
            throw new \Exception('You do not have permission to create users.');
        }
        
        // Validate edition access for non-system admin
        if ($currentUser->user_type !== 'system_admin' && !empty($data['edition_id'])) {
            if (!$this->userCanAccessEdition($currentUser, $data['edition_id'])) {
                throw new \Exception('You do not have access to this edition.');
            }
        }
        
        DB::beginTransaction();
        try {
            // Hash password if provided
            if (!empty($data['password'])) {
                $data['password'] = Hash::make($data['password']);
            }
            
            // Set edition_id based on role
            if ($currentUser->user_type === 'hackathon_admin' && empty($data['edition_id'])) {
                $data['edition_id'] = $currentUser->edition_id;
            }
            
            // Create user
            $user = $this->userRepository->create($data);
            
            // Log activity
            Log::info('User created', [
                'user_id' => $user->id,
                'created_by' => $currentUser->id,
                'data' => array_diff_key($data, ['password' => ''])
            ]);
            
            DB::commit();
            
            return [
                'success' => true,
                'user' => $user,
                'message' => 'User created successfully.'
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to create user', [
                'created_by' => $currentUser->id,
                'error' => $e->getMessage(),
                'data' => array_diff_key($data, ['password' => ''])
            ]);
            throw $e;
        }
    }

    /**
     * Update a user
     */
    public function updateUser(string $userId, array $data, User $currentUser): array
    {
        $user = $this->userRepository->find($userId);
        
        if (!$user) {
            throw new \Exception('User not found.');
        }
        
        // Check permissions
        if (!$this->userCanEditUser($currentUser, $user)) {
            throw new \Exception('You do not have permission to edit this user.');
        }
        
        DB::beginTransaction();
        try {
            // Hash password if provided
            if (!empty($data['password'])) {
                $data['password'] = Hash::make($data['password']);
            } else {
                unset($data['password']);
            }
            
            // Prevent changing user_type unless system admin
            if ($currentUser->user_type !== 'system_admin' && isset($data['user_type'])) {
                unset($data['user_type']);
            }
            
            // Update user
            $this->userRepository->update($userId, $data);
            
            // Refresh user data
            $user = $this->userRepository->findWithFullDetails($userId);
            
            // Log activity
            Log::info('User updated', [
                'user_id' => $userId,
                'updated_by' => $currentUser->id,
                'data' => array_diff_key($data, ['password' => ''])
            ]);
            
            DB::commit();
            
            return [
                'success' => true,
                'user' => $user,
                'message' => 'User updated successfully.'
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to update user', [
                'user_id' => $userId,
                'updated_by' => $currentUser->id,
                'error' => $e->getMessage(),
                'data' => array_diff_key($data, ['password' => ''])
            ]);
            throw $e;
        }
    }

    /**
     * Delete a user
     */
    public function deleteUser(string $userId, User $currentUser): array
    {
        $user = $this->userRepository->find($userId);
        
        if (!$user) {
            throw new \Exception('User not found.');
        }
        
        // Check permissions
        if (!$this->userCanDeleteUser($currentUser, $user)) {
            throw new \Exception('You do not have permission to delete this user.');
        }
        
        // Check dependencies
        if ($this->userRepository->hasDependencies($userId)) {
            throw new \Exception('Cannot delete user with dependencies.');
        }
        
        DB::beginTransaction();
        try {
            // Delete user
            $this->userRepository->delete($userId);
            
            // Log activity
            Log::info('User deleted', [
                'user_id' => $userId,
                'deleted_by' => $currentUser->id
            ]);
            
            DB::commit();
            
            return [
                'success' => true,
                'message' => 'User deleted successfully.'
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to delete user', [
                'user_id' => $userId,
                'deleted_by' => $currentUser->id,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Search users for autocomplete
     */
    public function searchUsers(User $currentUser, string $query, int $limit = 10): Collection
    {
        if (strlen($query) < 2) {
            return collect([]);
        }
        
        // Build filters based on user role
        $filters = $this->buildRoleFilters($currentUser, ['search' => $query]);
        
        return $this->userRepository->searchUsersWithFilters($filters, $limit);
    }

    /**
     * Export users to CSV
     */
    public function exportUsers(User $currentUser, array $filters = []): array
    {
        // Build filters based on user role
        $roleFilters = $this->buildRoleFilters($currentUser, $filters);
        
        // Get users for export
        $users = $this->userRepository->getForExport($roleFilters);
        
        // Build CSV data
        $csvData = $this->buildExportData($users);
        
        return [
            'data' => $csvData,
            'filename' => 'users-export-' . date('Y-m-d') . '.csv'
        ];
    }

    /**
     * Build filters based on user role
     */
    protected function buildRoleFilters(User $user, array $filters): array
    {
        $roleFilters = $filters;
        
        switch ($user->user_type) {
            case 'hackathon_admin':
                // Limit to user's edition
                if ($user->edition_id) {
                    $roleFilters['edition_id'] = $user->edition_id;
                }
                // Can't manage system admins
                $roleFilters['exclude_user_types'] = ['system_admin'];
                break;
                
            case 'track_supervisor':
                // Can only see users in their tracks
                $trackIds = DB::table('track_supervisors')
                    ->where('user_id', $user->id)
                    ->pluck('track_id')
                    ->toArray();
                    
                if (!empty($trackIds)) {
                    // Get team members in their tracks
                    $teamIds = DB::table('teams')
                        ->whereIn('track_id', $trackIds)
                        ->pluck('id')
                        ->toArray();
                    
                    $roleFilters['team_ids'] = $teamIds;
                }
                // Can only see team members and leaders
                $roleFilters['user_types'] = ['team_leader', 'team_member'];
                break;
                
            case 'system_admin':
                // No additional filters - can see everything
                break;
                
            default:
                // Other roles - force empty result
                $roleFilters['force_empty'] = true;
                break;
        }
        
        return $roleFilters;
    }

    /**
     * Get editions available to user
     */
    protected function getEditionsForUser(User $user): Collection
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

    /**
     * Check if user can access a specific user
     */
    protected function userCanAccessUser(User $currentUser, User $targetUser): bool
    {
        switch ($currentUser->user_type) {
            case 'system_admin':
                return true;
                
            case 'hackathon_admin':
                // Can't access system admins
                if ($targetUser->user_type === 'system_admin') {
                    return false;
                }
                // Can access users in their edition
                return $currentUser->edition_id == $targetUser->edition_id;
                
            case 'track_supervisor':
                // Can only access team members in their tracks
                if (!in_array($targetUser->user_type, ['team_leader', 'team_member'])) {
                    return false;
                }
                
                // Check if user is in a team in their tracks
                $trackIds = DB::table('track_supervisors')
                    ->where('user_id', $currentUser->id)
                    ->pluck('track_id');
                    
                $userTeams = DB::table('team_members')
                    ->where('user_id', $targetUser->id)
                    ->pluck('team_id');
                    
                return DB::table('teams')
                    ->whereIn('id', $userTeams)
                    ->whereIn('track_id', $trackIds)
                    ->exists();
                
            default:
                return false;
        }
    }

    /**
     * Check if user can access a specific edition
     */
    protected function userCanAccessEdition(User $user, string $editionId): bool
    {
        switch ($user->user_type) {
            case 'system_admin':
                return true;
                
            case 'hackathon_admin':
                return $user->edition_id == $editionId;
                
            default:
                return false;
        }
    }

    /**
     * Check if user can create users
     */
    protected function userCanCreateUser(User $user): bool
    {
        return in_array($user->user_type, ['system_admin', 'hackathon_admin']);
    }

    /**
     * Check if user can edit a user
     */
    protected function userCanEditUser(User $currentUser, User $targetUser): bool
    {
        if (!$this->userCanAccessUser($currentUser, $targetUser)) {
            return false;
        }
        
        // Hackathon admin can't edit system admins
        if ($currentUser->user_type === 'hackathon_admin' && $targetUser->user_type === 'system_admin') {
            return false;
        }
        
        return in_array($currentUser->user_type, ['system_admin', 'hackathon_admin']);
    }

    /**
     * Check if user can delete a user
     */
    protected function userCanDeleteUser(User $currentUser, User $targetUser): bool
    {
        if (!$this->userCanAccessUser($currentUser, $targetUser)) {
            return false;
        }
        
        // Only system admin can delete users
        return $currentUser->user_type === 'system_admin';
    }

    /**
     * Get permissions for a user
     */
    protected function getUserPermissions(User $currentUser, User $targetUser): array
    {
        return [
            'canEdit' => $this->userCanEditUser($currentUser, $targetUser),
            'canDelete' => $this->userCanDeleteUser($currentUser, $targetUser),
            'canExport' => true,
        ];
    }

    /**
     * Build export data
     */
    protected function buildExportData(Collection $users): array
    {
        $csvData = [];
        $csvData[] = ['ID', 'Name', 'Email', 'Type', 'Edition', 'Team', 'Status', 'Created At'];
        
        foreach ($users as $user) {
            $csvData[] = [
                $user->id,
                $user->name,
                $user->email,
                $user->user_type,
                $user->edition?->name ?? 'N/A',
                $user->teams?->first()?->name ?? 'N/A',
                ($user->last_login_at && $user->last_login_at > now()->subDays(30)) ? 'Active' : 'Inactive',
                $user->created_at->format('Y-m-d H:i'),
            ];
        }
        
        return $csvData;
    }
}