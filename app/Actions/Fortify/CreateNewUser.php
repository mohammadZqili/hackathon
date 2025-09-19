<?php

namespace App\Actions\Fortify;

use App\Models\User;
use App\Models\TeamInvitation;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Laravel\Fortify\Contracts\CreatesNewUsers;

class CreateNewUser implements CreatesNewUsers
{
    use PasswordValidationRules;

    /**
     * Validate and create a newly registered user.
     *
     * @param  array<string, string>  $input
     */
    public function create(array $input): User
    {
        Validator::make($input, [
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique(User::class),
            ],
            'password' => $this->passwordRules(),
            'national_id' => ['required', 'string', 'max:20', Rule::unique(User::class)],
            'phone' => ['required', 'string', 'max:20'],
            'user_type' => ['required', 'string', 'in:team_leader,team_member,visitor'],
            'occupation' => ['required', 'string', 'in:student,employee'],
            'job_title' => ['nullable', 'string', 'max:255', 'required_if:occupation,employee'],
        ])->validate();

        $now = now();
        $userData = [
            'name' => $input['name'],
            'email' => $input['email'],
            'password' => Hash::make($input['password']),
            'national_id' => $input['national_id'],
            'phone' => $input['phone'],
            'user_type' => $input['user_type'],
            'occupation' => $input['occupation'],
            'is_active' => true,
            'password_changed_at' => $now,
            'password_expiry_at' => $now->addMonths(3),
        ];

        // Add job_title only if occupation is employee
        if ($input['occupation'] === 'employee' && !empty($input['job_title'])) {
            $userData['job_title'] = $input['job_title'];
        }

        $user = User::create($userData);

        // Assign role based on user_type
        if (class_exists('\Spatie\Permission\Models\Role')) {
            $roleMapping = [
                'team_leader' => 'team_leader',
                'team_member' => 'team_member',
                'visitor' => 'visitor'
            ];

            if (isset($roleMapping[$input['user_type']])) {
                try {
                    $role = \Spatie\Permission\Models\Role::firstOrCreate(
                        ['name' => $roleMapping[$input['user_type']]],
                        ['guard_name' => 'web']
                    );
                    $user->assignRole($role);
                } catch (\Exception $e) {
                    // Log error but don't fail registration
                    \Log::warning('Could not assign role during registration: ' . $e->getMessage());
                }
            }
        }

        // Check if there's a team invitation
        if (!empty($input['invitation_token'])) {
            $this->handleTeamInvitation($user, $input['invitation_token'], $input['email']);
        }

        session()->flash('success', 'Great! Your account has been created successfully.');

        return $user;
    }

    /**
     * Handle team invitation after user registration.
     *
     * @param  \App\Models\User  $user
     * @param  string  $token
     * @param  string  $email
     * @return void
     */
    protected function handleTeamInvitation(User $user, string $token, string $email)
    {
        // Find the invitation
        $invitation = TeamInvitation::where('token', $token)
            ->where('email', $email)
            ->where('status', 'pending')
            ->where('expires_at', '>', now())
            ->first();

        if ($invitation) {
            // Add user to the team
            $team = $invitation->team;
            if ($team) {
                try {
                    // Add user to team with the specified role and status
                    // Note: role should be 'member' not 'team_member' based on enum in database
                    $team->members()->attach($user->id, [
                        'role' => ($invitation->role === 'team_member' ? 'member' : $invitation->role) ?? 'member',
                        'status' => 'accepted',
                        'joined_at' => now(),
                        'invited_at' => $invitation->created_at,
                        'invited_by' => $team->leader_id
                    ]);

                    \Log::info('Team member attached successfully', [
                        'user_id' => $user->id,
                        'team_id' => $team->id,
                        'role' => ($invitation->role === 'team_member' ? 'member' : $invitation->role) ?? 'member'
                    ]);
                } catch (\Exception $e) {
                    \Log::error('Failed to attach team member', [
                        'error' => $e->getMessage(),
                        'user_id' => $user->id,
                        'team_id' => $team->id
                    ]);
                }

                // Update invitation status
                $invitation->update([
                    'status' => 'accepted',
                    'accepted_at' => now()
                ]);

                // Flash success message
                session()->flash('success',
                    session('success') . " You have been added to team '{$team->name}'!"
                );
            }
        }
    }
}
