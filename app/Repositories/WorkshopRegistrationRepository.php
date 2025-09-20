<?php

namespace App\Repositories;

use App\Models\User;
use App\Models\WorkshopRegistration;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class WorkshopRegistrationRepository extends BaseRepository
{
    public function __construct()
    {
        parent::__construct(new WorkshopRegistration());
    }

    /**
     * Find registration by workshop and user
     */
    public function findByWorkshopAndUser(int $workshopId, int $userId): ?WorkshopRegistration
    {
        return $this->query()
            ->where('workshop_id', $workshopId)
            ->where('user_id', $userId)
            ->first();
    }

    /**
     * Find registration by workshop and barcode
     */
    public function findByWorkshopAndBarcode(int $workshopId, string $barcode): ?WorkshopRegistration
    {
        return $this->query()
            ->where('workshop_id', $workshopId)
            ->where('barcode', $barcode)
            ->first();
    }

    /**
     * Find registration by legacy QR data
     */
    public function findByLegacyData(int $workshopId, string $barcode, ?int $registrationId = null): ?WorkshopRegistration
    {
        $query = $this->query()
            ->where('workshop_id', $workshopId)
            ->where('barcode', $barcode);

        if ($registrationId) {
            $query->orWhere(function($q) use ($workshopId, $registrationId) {
                $q->where('workshop_id', $workshopId)
                  ->where('id', $registrationId);
            });
        }

        return $query->first();
    }

    /**
     * Get workshop statistics
     */
    public function getWorkshopStats(int $workshopId): array
    {
        $query = $this->query()->where('workshop_id', $workshopId);

        $registered = $query->count();
        $attendees = (clone $query)->whereNotNull('attended_at')->count();
        $walkIns = (clone $query)->whereNull('user_id')->whereNotNull('attended_at')->count();

        return [
            'registered' => $registered,
            'attendees' => $attendees,
            'unregistered' => $walkIns,
            'attendance_rate' => $registered > 0 ? round(($attendees / $registered) * 100, 1) : 0,
        ];
    }

    /**
     * Get attendance data for export
     */
    public function getAttendanceForExport(?int $workshopId = null): Collection
    {
        $query = $this->query()
            ->with(['user', 'workshop'])
            ->whereNotNull('attended_at');

        if ($workshopId) {
            $query->where('workshop_id', $workshopId);
        }

        return $query->orderBy('attended_at', 'desc')->get();
    }

    /**
     * Search users for participant lookup
     */
    public function searchUsers(string $query): array
    {
        $results = User::where('name', 'like', "%{$query}%")
            ->orWhere('email', 'like', "%{$query}%")
            ->orWhere('national_id', 'like', "%{$query}%")
            ->with(['workshopRegistrations.workshop'])
            ->take(10)
            ->get()
            ->map(function ($user) {
                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'national_id' => $user->national_id,
                    'registrations' => $user->workshopRegistrations->map(function ($reg) {
                        return [
                            'workshop_id' => $reg->workshop_id,
                            'workshop_title' => $reg->workshop->title ?? 'N/A',
                            'barcode' => $reg->barcode,
                            'attended' => $reg->attended_at !== null,
                        ];
                    }),
                ];
            });

        return ['results' => $results];
    }

    /**
     * Get recent check-ins across all workshops
     */
    public function getRecentCheckIns(int $limit = 50): Collection
    {
        return $this->query()
            ->with(['user', 'workshop'])
            ->whereNotNull('attended_at')
            ->orderBy('attended_at', 'desc')
            ->take($limit)
            ->get();
    }

    /**
     * Get workshop attendance details
     */
    public function getWorkshopAttendance(int $workshopId): Collection
    {
        return $this->query()
            ->with('user')
            ->where('workshop_id', $workshopId)
            ->whereNotNull('attended_at')
            ->orderBy('attended_at', 'desc')
            ->get();
    }

    /**
     * Get workshop registrations without attendance
     */
    public function getPendingRegistrations(int $workshopId): Collection
    {
        return $this->query()
            ->with('user')
            ->where('workshop_id', $workshopId)
            ->whereNull('attended_at')
            ->get();
    }

    /**
     * Add relationships to query
     */
    protected function addRelationships($query): void
    {
        $query->with(['user', 'workshop']);
    }

    /**
     * Apply search filters
     */
    protected function applySearchFilters($query, string $search): void
    {
        $query->whereHas('user', function($q) use ($search) {
            $q->where('name', 'like', "%{$search}%")
              ->orWhere('email', 'like', "%{$search}%");
        })
        ->orWhereHas('workshop', function($q) use ($search) {
            $q->where('title', 'like', "%{$search}%");
        })
        ->orWhere('barcode', 'like', "%{$search}%");
    }

    /**
     * Check if entity has dependencies
     */
    public function hasDependencies(int $id): bool
    {
        // Workshop registrations typically don't have dependencies that prevent deletion
        return false;
    }
}