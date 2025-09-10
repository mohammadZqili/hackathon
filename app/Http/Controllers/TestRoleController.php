<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TestRoleController extends Controller
{
    public function checkRole(Request $request)
    {
        $user = auth()->user();
        
        if (!$user) {
            return response()->json([
                'error' => 'Not authenticated'
            ], 401);
        }
        
        return response()->json([
            'user' => [
                'id' => $user->id,
                'email' => $user->email,
                'name' => $user->name,
            ],
            'roles' => $user->getRoleNames()->toArray(),
            'permissions' => $user->getAllPermissions()->pluck('name')->toArray(),
            'checks' => [
                'has_hackathon-admin' => $user->hasRole('hackathon-admin'),
                'has_hackathon_admin' => $user->hasRole('hackathon_admin'),
                'has_system-admin' => $user->hasRole('system-admin'),
                'has_system_admin' => $user->hasRole('system_admin'),
            ],
            'edition_id' => $user->edition_id ?? null,
        ]);
    }
}