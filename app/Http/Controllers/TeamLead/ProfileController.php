<?php

namespace App\Http\Controllers\TeamLead;

use App\Http\Controllers\Controller;
use App\Services\ProfileService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ProfileController extends Controller
{
    protected $profileService;

    public function __construct(ProfileService $profileService)
    {
        $this->profileService = $profileService;
    }

    public function index()
    {
        $profile = $this->profileService->getUserProfile(auth()->id());
        
        return Inertia::render('Shared/Profile/Index', [
            'profile' => $profile,
            'role' => 'team-lead'
        ]);
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'bio' => 'nullable|string|max:500',
            'skills' => 'nullable|array',
            'avatar' => 'nullable|image|max:2048'
        ]);

        $profile = $this->profileService->updateProfile(auth()->id(), $validated);

        return back()->with('success', 'Profile updated successfully');
    }
}
