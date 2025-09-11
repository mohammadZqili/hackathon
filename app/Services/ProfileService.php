<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Storage;

class ProfileService extends BaseService
{
    public function getUserProfile($userId)
    {
        return User::with(['roles', 'teams'])->findOrFail($userId);
    }

    public function updateProfile($userId, array $data)
    {
        $user = User::findOrFail($userId);

        if (isset($data['avatar'])) {
            // Delete old avatar if exists
            if ($user->avatar) {
                Storage::delete($user->avatar);
            }
            
            // Store new avatar
            $data['avatar'] = $data['avatar']->store('avatars', 'public');
        }

        $user->update($data);

        return $user;
    }
}
