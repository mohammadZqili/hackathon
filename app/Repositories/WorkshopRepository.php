<?php

namespace App\Repositories;

use App\Models\Workshop;
use App\Models\WorkshopRegistration;
use App\Repositories\Contracts\WorkshopRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Str;
use Carbon\Carbon;

class WorkshopRepository extends BaseRepository implements WorkshopRepositoryInterface
{
    public function __construct(Workshop $model)
    {
        parent::__construct($model);
    }

    /**
     * Get workshops by hackathon ID
     */
    public function getByHackathon(int $hackathonId, array $filters = [])
    {
        $query = $this->model->where('hackathon_id', $hackathonId)
            ->with(['speakers', 'organizations', 'registrations']);

        // Apply filters
        if (!empty($filters['search'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('title', 'like', "%{$filters['search']}%")
                  ->orWhere('description', 'like', "%{$filters['search']}%")
                  ->orWhere('location', 'like', "%{$filters['search']}%");
            });
        }

        if (!empty($filters['speaker_id'])) {
            $query->whereHas('speakers', function ($q) use ($filters) {
                $q->where('speakers.id', $filters['speaker_id']);
            });
        }

        if (!empty($filters['organization_id'])) {
            $query->whereHas('organizations', function ($q) use ($filters) {
                $q->where('organizations.id', $filters['organization_id']);
            });
        }

        if (!empty($filters['date_from'])) {
            $query->where('date', '>=', $filters['date_from']);
        }

        if (!empty($filters['date_to'])) {
            $query->where('date', '<=', $filters['date_to']);
        }

        if (!empty($filters['type'])) {
            $query->where('type', $filters['type']);
        }

        if (!empty($filters['available_only'])) {
            $query->whereRaw('capacity > (SELECT COUNT(*) FROM workshop_registrations WHERE workshop_id = workshops.id)');
        }

        // Apply sorting
        $sortBy = $filters['sort_by'] ?? 'date';
        $sortDirection = $filters['sort_direction'] ?? 'asc';
        $query->orderBy($sortBy, $sortDirection);

        return isset($filters['paginate']) && $filters['paginate']
            ? $query->paginate($filters['per_page'] ?? 15)
            : $query->get();
    }

    /**
     * Get workshop with registrations
     */
    public function getWithRegistrations(int $workshopId)
    {
        return $this->model->with(['speakers', 'organizations', 'registrations.user'])
            ->findOrFail($workshopId);
    }

    /**
     * Get workshop statistics
     */
    public function getStatistics(int $workshopId)
    {
        $workshop = $this->model->with(['registrations'])->findOrFail($workshopId);

        $registrations = $workshop->registrations;
        $attendedCount = $registrations->where('attended', true)->count();
        $noShowCount = $registrations->where('attended', false)->count();

        return [
            'capacity' => $workshop->capacity,
            'total_registrations' => $registrations->count(),
            'available_spots' => $workshop->capacity - $registrations->count(),
            'attended_count' => $attendedCount,
            'no_show_count' => $noShowCount,
            'attendance_rate' => $registrations->count() > 0 ? ($attendedCount / $registrations->count()) * 100 : 0,
            'is_full' => $registrations->count() >= $workshop->capacity,
        ];
    }

    /**
     * Register user for workshop
     */
    public function registerUser(int $workshopId, int $userId, array $data = [])
    {
        $workshop = $this->findOrFail($workshopId);

        // Check if workshop is full
        $currentRegistrations = $workshop->registrations()->count();
        if ($currentRegistrations >= $workshop->capacity) {
            throw new \Exception('Workshop is full');
        }

        // Check if user is already registered
        if ($this->isUserRegistered($workshopId, $userId)) {
            throw new \Exception('User is already registered for this workshop');
        }

        // Generate unique barcode
        $barcode = $this->generateUniqueBarcode();

        return WorkshopRegistration::create([
            'workshop_id' => $workshopId,
            'user_id' => $userId,
            'barcode' => $barcode,
            'registered_at' => now(),
            'attended' => false,
            ...$data
        ]);
    }

    /**
     * Unregister user from workshop
     */
    public function unregisterUser(int $workshopId, int $userId)
    {
        return WorkshopRegistration::where('workshop_id', $workshopId)
            ->where('user_id', $userId)
            ->delete();
    }

    /**
     * Check user registration status
     */
    public function isUserRegistered(int $workshopId, int $userId): bool
    {
        return WorkshopRegistration::where('workshop_id', $workshopId)
            ->where('user_id', $userId)
            ->exists();
    }

    /**
     * Mark user as attended
     */
    public function markAttendance(int $workshopId, int $userId, string $barcode = null)
    {
        $query = WorkshopRegistration::where('workshop_id', $workshopId)
            ->where('user_id', $userId);

        if ($barcode) {
            $query->where('barcode', $barcode);
        }

        $registration = $query->first();
        
        if (!$registration) {
            throw new \Exception('Registration not found');
        }

        if ($registration->attended) {
            throw new \Exception('User is already marked as attended');
        }

        $registration->update([
            'attended' => true,
            'attended_at' => now()
        ]);

        return $registration;
    }

    /**
     * Get user registrations
     */
    public function getUserRegistrations(int $userId, array $filters = [])
    {
        $query = WorkshopRegistration::with(['workshop.speakers', 'workshop.organizations'])
            ->where('user_id', $userId);

        // Apply filters
        if (!empty($filters['attended'])) {
            $query->where('attended', $filters['attended'] === 'yes');
        }

        if (!empty($filters['upcoming_only'])) {
            $query->whereHas('workshop', function ($q) {
                $q->where('date', '>', now());
            });
        }

        if (!empty($filters['past_only'])) {
            $query->whereHas('workshop', function ($q) {
                $q->where('date', '<', now());
            });
        }

        // Apply sorting
        $query->orderBy('registered_at', 'desc');

        return isset($filters['paginate']) && $filters['paginate']
            ? $query->paginate($filters['per_page'] ?? 15)
            : $query->get();
    }

    /**
     * Get workshops by speaker
     */
    public function getBySpeaker(int $speakerId)
    {
        return $this->model->whereHas('speakers', function ($query) use ($speakerId) {
            $query->where('speakers.id', $speakerId);
        })->with(['organizations', 'registrations'])->get();
    }

    /**
     * Get workshops by organization
     */
    public function getByOrganization(int $organizationId)
    {
        return $this->model->whereHas('organizations', function ($query) use ($organizationId) {
            $query->where('organizations.id', $organizationId);
        })->with(['speakers', 'registrations'])->get();
    }

    /**
     * Get available workshops (not full)
     */
    public function getAvailableWorkshops(array $filters = [])
    {
        $query = $this->model->whereRaw('capacity > (SELECT COUNT(*) FROM workshop_registrations WHERE workshop_id = workshops.id)')
            ->with(['speakers', 'organizations']);

        // Apply additional filters
        if (!empty($filters['upcoming_only'])) {
            $query->where('date', '>', now());
        }

        $query->orderBy('date', 'asc');

        return isset($filters['paginate']) && $filters['paginate']
            ? $query->paginate($filters['per_page'] ?? 15)
            : $query->get();
    }

    /**
     * Get workshop attendance report
     */
    public function getAttendanceReport(int $workshopId)
    {
        $workshop = $this->getWithRegistrations($workshopId);
        $registrations = $workshop->registrations;

        return [
            'workshop' => $workshop,
            'total_registered' => $registrations->count(),
            'attended' => $registrations->where('attended', true)->count(),
            'no_show' => $registrations->where('attended', false)->count(),
            'attendance_list' => $registrations->map(function ($registration) {
                return [
                    'user_name' => $registration->user->name,
                    'user_email' => $registration->user->email,
                    'registered_at' => $registration->registered_at,
                    'attended' => $registration->attended,
                    'attended_at' => $registration->attended_at,
                    'barcode' => $registration->barcode,
                ];
            }),
        ];
    }

    /**
     * Bulk register users for workshop
     */
    public function bulkRegisterUsers(int $workshopId, array $userIds)
    {
        $workshop = $this->findOrFail($workshopId);
        $currentRegistrations = $workshop->registrations()->count();

        if (($currentRegistrations + count($userIds)) > $workshop->capacity) {
            throw new \Exception('Not enough available spots for all users');
        }

        $registrations = [];
        foreach ($userIds as $userId) {
            if (!$this->isUserRegistered($workshopId, $userId)) {
                $registrations[] = [
                    'workshop_id' => $workshopId,
                    'user_id' => $userId,
                    'barcode' => $this->generateUniqueBarcode(),
                    'registered_at' => now(),
                    'attended' => false,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }

        if (!empty($registrations)) {
            WorkshopRegistration::insert($registrations);
        }

        return count($registrations);
    }

    /**
     * Generate unique barcode
     */
    private function generateUniqueBarcode(): string
    {
        do {
            $barcode = 'WS-' . strtoupper(Str::random(10));
        } while (WorkshopRegistration::where('barcode', $barcode)->exists());

        return $barcode;
    }
}
