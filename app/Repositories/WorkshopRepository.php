<?php

namespace App\Repositories;

use App\Models\Workshop;
use App\Models\WorkshopRegistration;
use Illuminate\Support\Facades\DB;

class WorkshopRepository
{
    public function getAll()
    {
        return Workshop::with(['supervisors', 'registrations'])->get();
    }

    public function getUpcoming()
    {
        return Workshop::where('start_time', '>=', now())
            ->orderBy('start_time', 'asc')
            ->with('supervisors')
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
        })->with('supervisors')->get();
    }

    public function isUserRegistered($userId, $workshopId)
    {
        return WorkshopRegistration::where('user_id', $userId)
            ->where('workshop_id', $workshopId)
            ->exists();
    }

    public function registerUser($userId, $workshopId)
    {
        return DB::transaction(function () use ($userId, $workshopId) {
            $workshop = Workshop::findOrFail($workshopId);
            $user = \App\Models\User::findOrFail($userId);
            
            // Use the workshop's registerUser method which handles barcode generation
            $registration = $workshop->registerUser($user);
            
            if (!$registration) {
                throw new \Exception('Registration failed');
            }
            
            return $registration;
        });
    }

    public function unregisterUser($userId, $workshopId)
    {
        DB::transaction(function () use ($userId, $workshopId) {
            WorkshopRegistration::where('user_id', $userId)
                ->where('workshop_id', $workshopId)
                ->delete();

            Workshop::where('id', $workshopId)
                ->decrement('current_attendees');
        });

        return true;
    }

    public function countUserWorkshops($userId)
    {
        return WorkshopRegistration::where('user_id', $userId)->count();
    }

    /**
     * Count workshops with filters
     */
    public function count(array $filters = []): int
    {
        $query = $this->query();

        if (!empty($filters['edition_id'])) {
            $query->where('hackathon_edition_id', $filters['edition_id']);
        }

        if (!empty($filters['status'])) {
            if ($filters['status'] === 'scheduled') {
                $query->where('is_active', true)->where('start_time', '>', now());
            } elseif ($filters['status'] === 'completed') {
                $query->where('end_time', '<', now());
            }
        }

        if (!empty($filters['date_after'])) {
            $query->where('start_time', '>', $filters['date_after']);
        }

        if (!empty($filters['force_empty'])) {
            $query->whereRaw('1 = 0');
        }

        return $query->count();
    }
}
