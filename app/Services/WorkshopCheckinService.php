<?php

namespace App\Services;

use App\Models\User;
use App\Models\Workshop;
use App\Models\WorkshopRegistration;
use App\Repositories\WorkshopRegistrationRepository;
use App\Services\QrCodeService;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class WorkshopCheckinService extends BaseService
{
    protected WorkshopRegistrationRepository $registrationRepository;
    protected QrCodeService $qrCodeService;

    public function __construct(
        WorkshopRegistrationRepository $registrationRepository,
        QrCodeService $qrCodeService
    ) {
        $this->registrationRepository = $registrationRepository;
        $this->qrCodeService = $qrCodeService;
    }

    /**
     * Get workshop check-in data
     */
    public function getWorkshopCheckinData(int $workshopId): array
    {
        $workshop = Workshop::with(['speakers', 'organizations', 'registrations.user'])
            ->findOrFail($workshopId);

        return [
            'workshop' => $this->formatWorkshopData($workshop),
            'recentCheckIns' => $this->getRecentCheckIns($workshop),
            'stats' => $this->getWorkshopStats($workshop),
            'workshopRegistrations' => $this->getPendingRegistrations($workshop),
        ];
    }

    /**
     * Process QR code for workshop check-in
     */
    public function processQRCode(string $code, int $workshopId, User $markedBy): array
    {
        DB::beginTransaction();
        try {
            // Parse QR code using service
            $parsedData = $this->qrCodeService->parseWorkshopQrCode($code);

            // Process based on QR format
            if ($parsedData && $parsedData['format'] === 'email_based') {
                $result = $this->processEmailBasedQR($parsedData, $workshopId, $markedBy);
            } elseif ($parsedData && $parsedData['format'] === 'legacy') {
                $result = $this->processLegacyQR($parsedData, $workshopId, $markedBy);
            } else {
                $result = $this->processBarcodeQR($code, $workshopId, $markedBy);
            }

            DB::commit();
            return $result;

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('QR processing failed', [
                'workshop_id' => $workshopId,
                'code' => $code,
                'error' => $e->getMessage(),
                'marked_by' => $markedBy->id
            ]);
            throw $e;
        }
    }

    /**
     * Mark manual attendance
     */
    public function markManualAttendance(string $identifier, int $workshopId, User $markedBy): array
    {
        DB::beginTransaction();
        try {
            // Find registration by barcode
            $registration = $this->registrationRepository->findByWorkshopAndBarcode($workshopId, $identifier);

            if (!$registration) {
                // Create walk-in registration
                $registration = $this->createWalkInRegistration($workshopId, $identifier, $markedBy);

                DB::commit();
                return [
                    'success' => true,
                    'message' => 'Walk-in attendee checked in successfully.',
                    'data' => $this->formatRegistrationData($registration, 'walk-in')
                ];
            }

            // Check if already attended
            if ($registration->attended_at) {
                return [
                    'success' => false,
                    'message' => 'Already checked in at ' . Carbon::parse($registration->attended_at)->format('h:i A'),
                    'data' => $this->formatRegistrationData($registration, 'already_checked_in')
                ];
            }

            // Mark attendance
            $this->markAttendance($registration, $markedBy);

            DB::commit();
            return [
                'success' => true,
                'message' => 'Check-in successful!',
                'data' => $this->formatRegistrationData($registration, 'registered')
            ];

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Manual attendance failed', [
                'workshop_id' => $workshopId,
                'identifier' => $identifier,
                'error' => $e->getMessage(),
                'marked_by' => $markedBy->id
            ]);
            throw $e;
        }
    }

    /**
     * Get workshop attendance statistics
     */
    public function getWorkshopAttendanceStats(int $workshopId): array
    {
        return $this->registrationRepository->getWorkshopStats($workshopId);
    }

    /**
     * Export attendance report
     */
    public function exportAttendanceReport(?int $workshopId = null): string
    {
        $registrations = $this->registrationRepository->getAttendanceForExport($workshopId);

        $csvData = "Name,Email,Workshop,Check-in Time,Status\n";

        foreach ($registrations as $registration) {
            $csvData .= sprintf(
                "%s,%s,%s,%s,%s\n",
                $registration->user->name ?? 'Guest',
                $registration->user->email ?? 'N/A',
                $registration->workshop->title ?? 'N/A',
                Carbon::parse($registration->attended_at)->format('Y-m-d H:i:s'),
                $registration->user_id ? 'Registered' : 'Walk-in'
            );
        }

        return $csvData;
    }

    /**
     * Search participants
     */
    public function searchParticipants(string $query): array
    {
        return $this->registrationRepository->searchUsers($query);
    }

    /**
     * Process email-based QR code
     */
    protected function processEmailBasedQR(array $parsedData, int $workshopId, User $markedBy): array
    {
        // Verify workshop ID matches
        if ($parsedData['workshop_id'] != $workshopId) {
            return [
                'success' => false,
                'message' => 'This QR code is for a different workshop.'
            ];
        }

        // Find user by email
        $user = User::where('email', $parsedData['user_email'])->first();
        if (!$user) {
            return [
                'success' => false,
                'message' => 'User not found with email: ' . $parsedData['user_email']
            ];
        }

        // Find or create registration
        $registration = $this->registrationRepository->findByWorkshopAndUser($workshopId, $user->id);

        if (!$registration) {
            // Create new registration
            $registration = $this->createAutoRegistration($workshopId, $user, $markedBy);

            return [
                'success' => true,
                'message' => 'User registered and checked in successfully!',
                'data' => $this->formatRegistrationData($registration, 'auto_registered')
            ];
        }

        return $this->handleExistingRegistration($registration, $markedBy);
    }

    /**
     * Process legacy QR code format
     */
    protected function processLegacyQR(array $parsedData, int $workshopId, User $markedBy): array
    {
        // Verify workshop ID matches
        if ($parsedData['workshop_id'] != $workshopId) {
            return [
                'success' => false,
                'message' => 'This QR code is for a different workshop.'
            ];
        }

        // Find registration by barcode or ID
        $registration = $this->registrationRepository->findByLegacyData(
            $workshopId,
            $parsedData['barcode'],
            $parsedData['registration_id']
        );

        if (!$registration) {
            return [
                'success' => false,
                'message' => 'Registration not found.'
            ];
        }

        return $this->handleExistingRegistration($registration, $markedBy);
    }

    /**
     * Process direct barcode QR
     */
    protected function processBarcodeQR(string $code, int $workshopId, User $markedBy): array
    {
        $registration = $this->registrationRepository->findByWorkshopAndBarcode($workshopId, $code);

        if (!$registration) {
            // Create walk-in registration
            $registration = $this->createWalkInRegistration($workshopId, $code, $markedBy);

            return [
                'success' => true,
                'message' => 'Walk-in attendee checked in successfully.',
                'data' => $this->formatRegistrationData($registration, 'walk-in')
            ];
        }

        return $this->handleExistingRegistration($registration, $markedBy);
    }

    /**
     * Handle existing registration check-in
     */
    protected function handleExistingRegistration(WorkshopRegistration $registration, User $markedBy): array
    {
        // Check if already attended
        if ($registration->attended_at) {
            return [
                'success' => false,
                'message' => 'Already checked in at ' . Carbon::parse($registration->attended_at)->format('h:i A'),
                'data' => $this->formatRegistrationData($registration, 'already_checked_in')
            ];
        }

        // Mark attendance
        $this->markAttendance($registration, $markedBy);

        return [
            'success' => true,
            'message' => 'Check-in successful!',
            'data' => $this->formatRegistrationData($registration, 'registered')
        ];
    }

    /**
     * Create auto registration from QR scan
     */
    protected function createAutoRegistration(int $workshopId, User $user, User $markedBy): WorkshopRegistration
    {
        return $this->registrationRepository->create([
            'workshop_id' => $workshopId,
            'user_id' => $user->id,
            'barcode' => Str::random(10) . '_' . time(),
            'status' => 'attended',
            'registered_at' => now(),
            'attended_at' => now(),
            'attendance_method' => 'qr_scan',
            'marked_by' => $markedBy->id,
            'notes' => 'Auto-registered via QR scan',
        ]);
    }

    /**
     * Create walk-in registration
     */
    protected function createWalkInRegistration(int $workshopId, string $identifier, User $markedBy): WorkshopRegistration
    {
        return $this->registrationRepository->create([
            'workshop_id' => $workshopId,
            'user_id' => $markedBy->id, // Use auth user as placeholder
            'barcode' => Str::random(10) . '_' . time(),
            'status' => 'attended',
            'registered_at' => now(),
            'attended_at' => now(),
            'attendance_method' => 'manual',
            'marked_by' => $markedBy->id,
            'notes' => 'Walk-in registration',
        ]);
    }

    /**
     * Mark attendance for registration
     */
    protected function markAttendance(WorkshopRegistration $registration, User $markedBy): void
    {
        $this->registrationRepository->update($registration->id, [
            'attended_at' => now(),
            'status' => 'attended',
            'attendance_method' => 'qr_scan',
            'marked_by' => $markedBy->id,
        ]);

        // Update workshop attendee count
        $registration->workshop->increment('current_attendees');
    }

    /**
     * Format workshop data for frontend
     */
    protected function formatWorkshopData(Workshop $workshop): array
    {
        return [
            'id' => $workshop->id,
            'title' => $workshop->title,
            'description' => $workshop->description,
            'start_time' => $workshop->start_time,
            'date_time' => $workshop->start_time ? Carbon::parse($workshop->start_time)->format('M d, Y h:i A') : null,
            'speakers' => $workshop->speakers->pluck('name')->implode(', '),
        ];
    }

    /**
     * Get recent check-ins for workshop
     */
    protected function getRecentCheckIns(Workshop $workshop): array
    {
        return $workshop->registrations()
            ->with('user')
            ->whereNotNull('attended_at')
            ->orderBy('attended_at', 'desc')
            ->get()
            ->map(function ($registration) {
                return [
                    'id' => $registration->id,
                    'name' => $registration->user->name ?? 'Guest',
                    'email' => $registration->user->email ?? 'N/A',
                    'checkinTime' => Carbon::parse($registration->attended_at)->format('h:i A, M d, Y'),
                    'registered' => $registration->user_id !== null,
                    'barcode' => $registration->barcode,
                ];
            })
            ->toArray();
    }

    /**
     * Get workshop statistics
     */
    protected function getWorkshopStats(Workshop $workshop): array
    {
        return [
            'registered' => $workshop->registrations()->count(),
            'attendees' => $workshop->registrations()->whereNotNull('attended_at')->count(),
            'unregistered' => $workshop->registrations()->whereNull('user_id')->whereNotNull('attended_at')->count(),
        ];
    }

    /**
     * Get pending registrations for QR generation
     */
    protected function getPendingRegistrations(Workshop $workshop): array
    {
        return $workshop->registrations()
            ->with('user')
            ->whereNull('attended_at')
            ->get()
            ->map(function ($registration) {
                return [
                    'id' => $registration->id,
                    'user_name' => $registration->user->name ?? 'Guest',
                    'user_email' => $registration->user->email ?? '',
                    'barcode' => $registration->barcode,
                ];
            })
            ->toArray();
    }

    /**
     * Format registration data for response
     */
    protected function formatRegistrationData(WorkshopRegistration $registration, string $type): array
    {
        $workshop = $registration->workshop;
        $user = $registration->user;

        return [
            'id' => $registration->id,
            'name' => $user ? $user->name : 'Guest',
            'email' => $user ? $user->email : null,
            'workshop' => $workshop->title,
            'type' => $type,
            'code' => $registration->barcode,
            'checkin_time' => $registration->attended_at ?
                Carbon::parse($registration->attended_at)->format('h:i A, M d, Y') : null
        ];
    }
}