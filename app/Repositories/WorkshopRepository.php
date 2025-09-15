<?php

namespace App\Repositories;

use App\Models\Workshop;
use App\Models\WorkshopRegistration;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class WorkshopRepository extends BaseRepository
{
    public function __construct()
    {
        parent::__construct(new Workshop());
    }
    public function getAllOrderedByTitle()
    {
        return $this->model->orderBy('title')->get();
    }

    public function getPaginatedWithRelations(int $perPage = 15)
    {
        return $this->model->with(['speakers', 'organizations'])
            ->latest()
            ->paginate($perPage);
    }

    public function findWithRelations(int $id)
    {
        return $this->model->with(['hackathon', 'speakers', 'organizations', 'registrations'])
            ->find($id);
    }

    public function findWithSpeakersAndOrganizations(int $id)
    {
        return $this->model->with(['speakers', 'organizations'])
            ->find($id);
    }

    public function findWithAttendance(int $id)
    {
        return $this->model->with('attendances.user')
            ->find($id);
    }

    public function updateWithRelations(int $id, array $data)
    {
        $workshop = $this->model->find($id);

        // Separate relation data from workshop data
        $workshopData = collect($data)->except(['speaker_ids', 'organization_ids'])->toArray();
        $workshop->update($workshopData);

        // Sync speakers if provided
        if (isset($data['speaker_ids'])) {
            $speakerData = [];
            foreach ($data['speaker_ids'] as $index => $speakerId) {
                $speakerData[$speakerId] = ['role' => 'main_speaker', 'order' => $index + 1];
            }
            $workshop->speakers()->sync($speakerData);
        }

        // Sync organizations if provided
        if (isset($data['organization_ids'])) {
            $orgData = [];
            foreach ($data['organization_ids'] as $orgId) {
                $orgData[$orgId] = ['role' => 'organizer'];
            }
            $workshop->organizations()->sync($orgData);
        }

        return $workshop;
    }

    public function getAll()
    {
        return $this->model->with(['supervisors', 'registrations'])->get();
    }

    public function getUpcoming()
    {
        return $this->model->where('start_time', '>=', now())
            ->orderBy('start_time', 'asc')
            ->with('supervisors')
            ->get();
    }

    public function findOrFail(string $id, array $columns = ['*']): Model
    {
        return $this->model->select($columns)->findOrFail($id);
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
