<?php

namespace App\Services;

use App\Models\Workshop;
use App\Models\Edition;
use App\Models\User;
use App\Models\Team;
use App\Models\Track;
use App\Models\WorkshopRegistration;
use App\Notifications\WorkshopCreatedNotification;
use App\Repositories\WorkshopRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;
use Carbon\Carbon;

class WorkshopService extends BaseService
{
    protected $workshopRepo;

    public function __construct(WorkshopRepository $workshopRepo)
    {
        $this->workshopRepo = $workshopRepo;
    }


    /**
     * Get paginated workshops
     */
    public function getPaginatedWorkshops(int $perPage = 15)
    {
        return $this->workshopRepo->getPaginatedWithRelations($perPage);
    }

    /**
     * Get workshop with relations
     */
    public function getWorkshopWithRelations(int $id)
    {
        return $this->workshopRepo->findWithSpeakersAndOrganizations($id);
    }

    /**
     * Get workshop with attendance
     */
    public function getWorkshopWithAttendance(int $id)
    {
        return $this->workshopRepo->findWithAttendance($id);
    }

    /**
     * Get workshop details with all relations for display
     */
    public function getWorkshopDetails(int $id)
    {
        $workshop = $this->workshopRepo->findWithSpeakersAndOrganizations($id);

        if (!$workshop) {
            return null;
        }

        // Load additional relations if needed
        $workshop->load(['attendees', 'speakers', 'organizations']);

        // Add computed attributes
        $workshop->attendees_count = $workshop->attendees()->count();
        $workshop->is_full = $workshop->isFull();
        $workshop->can_register = $workshop->canRegister();
        $workshop->available_seats = $workshop->max_attendees - $workshop->current_attendees;

        return $workshop;
    }

    /**
     * Validate workshop data with business rules
     * Following Meta/Google standards for comprehensive validation
     */
    public function validateWorkshopData(array $data): array
    {
        $errors = [];

        // Parse dates for validation
        $startTime = Carbon::parse($data['start_time']);
        $endTime = Carbon::parse($data['end_time']);
        $now = Carbon::now();

        // Business Rule 1: Check for conflicting workshops at the same location
        if (!empty($data['location']) && $data['format'] !== 'online') {
            $conflictingWorkshop = Workshop::where('location', $data['location'])
                ->where(function($query) use ($startTime, $endTime) {
                    $query->whereBetween('start_time', [$startTime, $endTime])
                          ->orWhereBetween('end_time', [$startTime, $endTime])
                          ->orWhere(function($q) use ($startTime, $endTime) {
                              $q->where('start_time', '<=', $startTime)
                                ->where('end_time', '>=', $endTime);
                          });
                })
                ->first();

            if ($conflictingWorkshop) {
                $errors['location'] = "This location is already booked for another workshop at this time.";
            }
        }

        // Business Rule 2: Validate workshop timing (business hours)
        $startHour = $startTime->hour;
        $endHour = $endTime->hour;

        if ($startHour < 7 || $startHour > 20) {
            $errors['start_time'] = "Workshops should start between 7:00 AM and 8:00 PM.";
        }

        if ($endHour > 22 || ($endHour == 22 && $endTime->minute > 0)) {
            $errors['end_time'] = "Workshops should end by 10:00 PM.";
        }

        // Business Rule 3: Check speaker availability
        if (!empty($data['speaker_ids'])) {
            foreach ($data['speaker_ids'] as $speakerId) {
                $speakerBusy = DB::table('workshop_speakers')
                    ->join('workshops', 'workshops.id', '=', 'workshop_speakers.workshop_id')
                    ->where('workshop_speakers.speaker_id', $speakerId)
                    ->where(function($query) use ($startTime, $endTime) {
                        $query->whereBetween('workshops.start_time', [$startTime, $endTime])
                              ->orWhereBetween('workshops.end_time', [$startTime, $endTime]);
                    })
                    ->exists();

                if ($speakerBusy) {
                    $errors['speaker_ids'] = "One or more selected speakers are not available at this time.";
                    break;
                }
            }
        }

        // Business Rule 4: Validate capacity for online workshops
        if ($data['format'] === 'online' && (!isset($data['max_attendees']) || $data['max_attendees'] > 500)) {
            $errors['max_attendees'] = "Online workshops are limited to 500 attendees.";
        }

        // Business Rule 5: Registration deadline validation
        if (!empty($data['registration_deadline'])) {
            $regDeadline = Carbon::parse($data['registration_deadline']);
            $daysBefore = $startTime->diffInDays($regDeadline);

            if ($daysBefore < 1) {
                $errors['registration_deadline'] = "Registration should close at least 1 day before the workshop.";
            }
        }

        return [
            'valid' => empty($errors),
            'errors' => $errors
        ];
    }

    /**
     * Create a workshop with all relationships
     * Following repository pattern as per architecture standards
     */
    public function createWorkshop(array $data, User $createdBy): Workshop
    {
        return DB::transaction(function () use ($data, $createdBy) {
            // Prepare workshop data
            $workshopData = collect($data)->except(['speaker_ids', 'organization_ids', 'remote_link'])->toArray();

            // Use hackathon_id (renamed from hackathon_edition_id for db compatibility)
            if (!empty($workshopData['hackathon_edition_id'])) {
                $workshopData['hackathon_id'] = $workshopData['hackathon_edition_id'];
                unset($workshopData['hackathon_edition_id']);
            } elseif (empty($workshopData['hackathon_id'])) {
                $activeEdition = Edition::where('is_current', true)->first();
                $workshopData['hackathon_id'] = $activeEdition ? $activeEdition->id : 1;
            }

            // Handle remote link for online workshops
            if ($data['format'] === 'online' && !empty($data['remote_link'])) {
                $workshopData['location'] = $data['remote_link'];
            }

            // Set defaults
            $workshopData['is_active'] = $workshopData['is_active'] ?? true;
            $workshopData['requires_registration'] = $workshopData['requires_registration'] ?? true;
            $workshopData['current_attendees'] = 0;

            // Create workshop
            $workshop = Workshop::create($workshopData);

            // Attach speakers with metadata
            if (!empty($data['speaker_ids'])) {
                $speakerData = [];
                foreach ($data['speaker_ids'] as $index => $speakerId) {
                    $speakerData[$speakerId] = [
                        'role' => $index === 0 ? 'main_speaker' : 'co_speaker',
                        'order' => $index + 1,
                        'created_at' => now(),
                        'updated_at' => now()
                    ];
                }
                $workshop->speakers()->attach($speakerData);
            }

            // Attach organizations
            if (!empty($data['organization_ids'])) {
                $orgData = [];
                foreach ($data['organization_ids'] as $orgId) {
                    $orgData[$orgId] = [
                        'role' => 'organizer',
                        'created_at' => now(),
                        'updated_at' => now()
                    ];
                }
                $workshop->organizations()->attach($orgData);
            }

            // Log creation activity (if activity logging is available)
            if (function_exists('activity')) {
                activity()
                    ->performedOn($workshop)
                    ->causedBy($createdBy)
                    ->withProperties(['workshop_title' => $workshop->title])
                    ->log('Workshop created');
            }

            // Send notifications to relevant track members and team leaders
//            $this->notifyTrackParticipants($workshop);

            return $workshop;
        });
    }

    /**
     * Notify track participants about new workshop
     * Sends QR code emails to ALL team members and leaders across ALL tracks
     */
    protected function notifyTrackParticipants(Workshop $workshop): void
    {
        try {
            // Get ALL teams from ALL tracks (not just current edition tracks)
            $teams = Team::with(['members'])->get();

            // Collect all users to notify (team members and leaders)
            $usersToNotify = collect();

            foreach ($teams as $team) {
                // Add team leader
                if ($team->leader_id) {
                    $leader = User::find($team->leader_id);
                    if ($leader) {
                        $usersToNotify->push($leader);
                    }
                }

                // Add all team members
                $members = $team->members()->get();
                foreach ($members as $member) {
                    $usersToNotify->push($member);
                }
            }

            // Also add all users with team_leader and team_member roles (in case they don't have teams yet)
            $teamLeaders = User::role('team_leader')->get();
            foreach ($teamLeaders as $leader) {
                $usersToNotify->push($leader);
            }

            $teamMembers = User::role('team_member')->get();
            foreach ($teamMembers as $member) {
                $usersToNotify->push($member);
            }

            // Remove duplicates
            $usersToNotify = $usersToNotify->unique('id');

            // Create registrations and send notifications
            $successCount = 0;
            $failCount = 0;

            foreach ($usersToNotify as $user) {
                try {
                    // Check if already registered
                    $existingRegistration = WorkshopRegistration::where('workshop_id', $workshop->id)
                        ->where('user_id', $user->id)
                        ->first();

                    if (!$existingRegistration) {
                        // Create registration
                        $registration = WorkshopRegistration::create([
                            'workshop_id' => $workshop->id,
                            'user_id' => $user->id,
                            'barcode' => $this->generateUniqueBarcode(),
                            'status' => 'registered',
                            'registered_at' => now(),
                        ]);

                        // Send notification with QR code
                        $user->notify(new WorkshopCreatedNotification($workshop, $registration));
                        $successCount++;
                    }
                } catch (\Exception $e) {
                    $failCount++;
                    \Log::error('Failed to send workshop notification', [
                        'user_id' => $user->id,
                        'workshop_id' => $workshop->id,
                        'error' => $e->getMessage()
                    ]);
                }
            }

            // Update workshop attendee count
            $workshop->update([
                'current_attendees' => WorkshopRegistration::where('workshop_id', $workshop->id)->count()
            ]);

            \Log::info('Workshop notifications sent to ALL tracks', [
                'workshop_id' => $workshop->id,
                'workshop_title' => $workshop->title,
                'total_recipients' => $usersToNotify->count(),
                'successful' => $successCount,
                'failed' => $failCount
            ]);

        } catch (\Exception $e) {
            // Log error but don't fail the workshop creation
            \Log::error('Failed to send workshop notifications', [
                'workshop_id' => $workshop->id,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Generate a unique barcode for registration
     */
    protected function generateUniqueBarcode(): string
    {
        do {
            $barcode = strtoupper(Str::random(10));
        } while (WorkshopRegistration::where('barcode', $barcode)->exists());

        return $barcode;
    }

    public function getAllWorkshops()
    {
        return $this->workshopRepo->getAll();
    }

    public function getUpcomingWorkshops()
    {
        return $this->workshopRepo->getUpcoming();
    }

    public function getUserWorkshops($userId)
    {
        return $this->workshopRepo->getUserWorkshops($userId);
    }

    public function registerForWorkshop($userId, $workshopId)
    {
        return DB::transaction(function () use ($userId, $workshopId) {
            // Check if already registered
            if ($this->workshopRepo->isUserRegistered($userId, $workshopId)) {
                throw new \Exception('Already registered for this workshop');
            }

            // Check capacity using the workshop's own method
            $workshop = $this->workshopRepo->find($workshopId);
            if (!$workshop->canRegister()) {
                if ($workshop->isFull()) {
                    throw new \Exception('Workshop is full');
                } elseif ($workshop->registration_deadline && now()->isAfter($workshop->registration_deadline)) {
                    throw new \Exception('Registration deadline has passed');
                } elseif (!$workshop->is_active) {
                    throw new \Exception('Workshop is not active');
                } else {
                    throw new \Exception('Registration is not available for this workshop');
                }
            }

            return $this->workshopRepo->registerUser($userId, $workshopId);
        });
    }

    public function unregisterFromWorkshop($userId, $workshopId)
    {
        return DB::transaction(function () use ($userId, $workshopId) {
            if (!$this->workshopRepo->isUserRegistered($userId, $workshopId)) {
                throw new \Exception('Not registered for this workshop');
            }

            return $this->workshopRepo->unregisterUser($userId, $workshopId);
        });
    }

    /**
     * Update an existing workshop
     */
    public function updateWorkshop(int $workshopId, array $data, User $updatedBy): Workshop
    {
        return DB::transaction(function () use ($workshopId, $data, $updatedBy) {
            // Find the workshop
            $workshop = Workshop::findOrFail($workshopId);

            // Prepare workshop data
            $workshopData = collect($data)->except(['speaker_ids', 'organization_ids'])->toArray();

            // Update workshop
            $workshop->update($workshopData);

            // Update speakers if provided
            if (isset($data['speaker_ids'])) {
                $speakerData = [];
                foreach ($data['speaker_ids'] as $index => $speakerId) {
                    if ($speakerId) {
                        $speakerData[$speakerId] = [
                            'role' => $index === 0 ? 'main_speaker' : 'co_speaker',
                            'order' => $index + 1,
                            'updated_at' => now()
                        ];
                    }
                }
                $workshop->speakers()->sync($speakerData);
            }

            // Update organizations if provided
            if (isset($data['organization_ids'])) {
                $orgData = [];
                foreach ($data['organization_ids'] as $orgId) {
                    if ($orgId) {
                        $orgData[$orgId] = [
                            'role' => 'organizer',
                            'updated_at' => now()
                        ];
                    }
                }
                $workshop->organizations()->sync($orgData);
            }

            // Log update activity (if activity logging is available)
            if (function_exists('activity')) {
                activity()
                    ->performedOn($workshop)
                    ->causedBy($updatedBy)
                    ->withProperties(['workshop_title' => $workshop->title])
                    ->log('Workshop updated');
            }

            return $workshop->fresh(['speakers', 'organizations']);
        });
    }

    /**
     * Delete a workshop
     */
    public function deleteWorkshop(int $workshopId): bool
    {
        return DB::transaction(function () use ($workshopId) {
            $workshop = Workshop::findOrFail($workshopId);

            // Remove all relationships
            $workshop->speakers()->detach();
            $workshop->organizations()->detach();
            $workshop->attendees()->detach();

            // Delete the workshop
            return $workshop->delete();
        });
    }
}
