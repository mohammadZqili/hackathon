<?php

namespace App\Repositories;

use App\Models\Workshop;
use App\Models\WorkshopRegistration;
use Illuminate\Support\Facades\DB;

class WorkshopRepository
{
    public function getAll()
    {
        return Workshop::with(['supervisor', 'registrations'])->get();
    }

    public function getUpcoming()
    {
        return Workshop::where('date', '>=', now())
            ->orderBy('date', 'asc')
            ->with('supervisor')
            ->get();
    }

    public function find($id)
    {
        return Workshop::findOrFail($id);
    }

    public function getUserWorkshops($userId)
    {
        return Workshop::whereHas('registrations', function ($query) use ($userId) {
            $query->where('user_id', $userId);
        })->with('supervisor')->get();
    }

    public function isUserRegistered($userId, $workshopId)
    {
        return WorkshopRegistration::where('user_id', $userId)
            ->where('workshop_id', $workshopId)
            ->exists();
    }

    public function registerUser($userId, $workshopId)
    {
        DB::transaction(function () use ($userId, $workshopId) {
            WorkshopRegistration::create([
                'user_id' => $userId,
                'workshop_id' => $workshopId,
                'registered_at' => now()
            ]);

            Workshop::where('id', $workshopId)
                ->increment('current_participants');
        });

        return true;
    }

    public function unregisterUser($userId, $workshopId)
    {
        DB::transaction(function () use ($userId, $workshopId) {
            WorkshopRegistration::where('user_id', $userId)
                ->where('workshop_id', $workshopId)
                ->delete();

            Workshop::where('id', $workshopId)
                ->decrement('current_participants');
        });

        return true;
    }

    public function countUserWorkshops($userId)
    {
        return WorkshopRegistration::where('user_id', $userId)->count();
    }
}
