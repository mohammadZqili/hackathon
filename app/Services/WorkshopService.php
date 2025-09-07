<?php

namespace App\Services;

use App\Models\Workshop;
use App\Models\User;
use App\Models\WorkshopRegistration;
use App\Repositories\WorkshopRepository;
use App\Services\Contracts\WorkshopServiceInterface;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\Image\ImagickImageBackEnd;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;

class WorkshopService implements WorkshopServiceInterface
{
    public function __construct(
        private WorkshopRepository $workshopRepo
    ) {}

    /**
     * Register user for a workshop.
     */
    public function registerForWorkshop(Workshop $workshop, User $user): ?WorkshopRegistration
    {
        return DB::transaction(function () use ($workshop, $user) {
            // Check if workshop is open for registration
            if (!$this->isRegistrationOpen($workshop)) {
                throw new \Exception('التسجيل في هذه الورشة غير متاح حالياً');
            }

            // Check if user is already registered
            $existingRegistration = WorkshopRegistration::where([
                'workshop_id' => $workshop->id,
                'user_id' => $user->id,
            ])->first();

            if ($existingRegistration) {
                if ($existingRegistration->status === 'cancelled') {
                    // Reactivate cancelled registration
                    $existingRegistration->update([
                        'status' => 'registered',
                        'registered_at' => now(),
                        'cancelled_at' => null,
                    ]);
                    
                    $this->updateWorkshopCapacity($workshop, 1);
                    return $existingRegistration;
                } else {
                    throw new \Exception('أنت مسجل في هذه الورشة بالفعل');
                }
            }

            // Check capacity
            if ($workshop->current_attendees >= $workshop->max_attendees) {
                throw new \Exception('الورشة ممتلئة');
            }

            // Create registration
            $registration = WorkshopRegistration::create([
                'workshop_id' => $workshop->id,
                'user_id' => $user->id,
                'status' => 'registered',
                'registered_at' => now(),
                'barcode' => $this->generateBarcode(),
            ]);

            // Update workshop capacity
            $this->updateWorkshopCapacity($workshop, 1);

            Log::info('Workshop registration created', [
                'workshop_id' => $workshop->id,
                'user_id' => $user->id,
                'registration_id' => $registration->id,
            ]);

            return $registration;
        });
    }

    /**
     * Cancel a workshop registration.
     */
    public function cancelRegistration(int $registrationId, User $user): bool
    {
        return DB::transaction(function () use ($registrationId, $user) {
            $registration = WorkshopRegistration::findOrFail($registrationId);

            // Check ownership
            if ($registration->user_id !== $user->id) {
                throw new \Exception('غير مصرح لك بإلغاء هذا التسجيل');
            }

            // Check if can be cancelled
            if (!$this->canCancelRegistration($registration)) {
                throw new \Exception('لا يمكن إلغاء التسجيل في هذا الوقت');
            }

            // Update registration
            $result = $registration->update([
                'status' => 'cancelled',
                'cancelled_at' => now(),
            ]);

            if ($result) {
                // Update workshop capacity
                $this->updateWorkshopCapacity($registration->workshop, -1);

                Log::info('Workshop registration cancelled', [
                    'registration_id' => $registrationId,
                    'user_id' => $user->id,
                ]);
            }

            return $result;
        });
    }

    /**
     * Generate QR code for workshop registration.
     */
    public function generateQRCode(WorkshopRegistration $registration): string
    {
        $qrData = json_encode([
            'type' => 'workshop_registration',
            'registration_id' => $registration->id,
            'workshop_id' => $registration->workshop_id,
            'user_id' => $registration->user_id,
            'barcode' => $registration->barcode,
            'timestamp' => now()->timestamp,
        ]);

        try {
            // Check if Imagick extension is available, fall back to SVG if not
            if (extension_loaded('imagick')) {
                $renderer = new ImageRenderer(
                    new RendererStyle(200, 1),
                    new ImagickImageBackEnd()
                );
                $writer = new Writer($renderer);
                $qrCode = $writer->writeString($qrData);
                
                // Return as base64 PNG
                return 'data:image/png;base64,' . base64_encode($qrCode);
            } else {
                // Use SVG backend as fallback
                $renderer = new ImageRenderer(
                    new RendererStyle(200, 1),
                    new \BaconQrCode\Renderer\Image\SvgImageBackEnd()
                );
                $writer = new Writer($renderer);
                $qrCode = $writer->writeString($qrData);
                
                // Return as base64 SVG
                return 'data:image/svg+xml;base64,' . base64_encode($qrCode);
            }

        } catch (\Exception $e) {
            Log::error('QR Code generation failed', [
                'registration_id' => $registration->id,
                'error' => $e->getMessage(),
            ]);

            // Return a simple text fallback
            return 'data:text/plain;base64,' . base64_encode($registration->barcode);
        }
    }

    /**
     * Scan attendance using barcode data.
     */
    public function scanAttendance(string $barcodeData, User $supervisor): array
    {
        try {
            // Decode QR data
            $data = json_decode($barcodeData, true);
            
            if (!$data || $data['type'] !== 'workshop_registration') {
                return [
                    'success' => false,
                    'message' => 'رمز QR غير صحيح',
                ];
            }

            $registration = WorkshopRegistration::with(['workshop', 'user'])
                ->where('id', $data['registration_id'])
                ->where('barcode', $data['barcode'])
                ->first();

            if (!$registration) {
                return [
                    'success' => false,
                    'message' => 'تسجيل غير موجود',
                ];
            }

            // Check supervisor permission
            if (!$this->canSupervisorScanWorkshop($registration->workshop, $supervisor)) {
                return [
                    'success' => false,
                    'message' => 'غير مصرح لك بمسح هذه الورشة',
                ];
            }

            // Check registration status
            if ($registration->status === 'cancelled') {
                return [
                    'success' => false,
                    'message' => 'التسجيل ملغى',
                ];
            }

            // Check if already attended
            if ($registration->attended_at) {
                return [
                    'success' => true,
                    'message' => 'تم تسجيل الحضور مسبقاً',
                    'registration' => $registration,
                    'already_attended' => true,
                ];
            }

            // Mark attendance
            $registration->update([
                'attended_at' => now(),
                'scanned_by' => $supervisor->id,
            ]);

            Log::info('Workshop attendance scanned', [
                'registration_id' => $registration->id,
                'workshop_id' => $registration->workshop_id,
                'scanned_by' => $supervisor->id,
            ]);

            return [
                'success' => true,
                'message' => 'تم تسجيل الحضور بنجاح',
                'registration' => $registration,
                'already_attended' => false,
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'خطأ في قراءة الرمز: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Get workshop attendance report.
     */
    public function getWorkshopAttendanceReport(int $workshopId): array
    {
        $workshop = Workshop::with(['registrations.user'])->findOrFail($workshopId);

        $registrations = $workshop->registrations;
        $attended = $registrations->whereNotNull('attended_at');
        $registered = $registrations->where('status', 'registered');
        $cancelled = $registrations->where('status', 'cancelled');

        // Calculate attendance stats
        $totalRegistered = $registered->count();
        $totalAttended = $attended->count();
        $attendanceRate = $totalRegistered > 0 ? ($totalAttended / $totalRegistered) * 100 : 0;

        // Attendance timeline
        $attendanceByHour = $attended->groupBy(function ($registration) {
            return $registration->attended_at->format('H:00');
        })->map->count();

        return [
            'workshop' => [
                'id' => $workshop->id,
                'title' => $workshop->title,
                'start_time' => $workshop->start_time,
                'end_time' => $workshop->end_time,
                'max_attendees' => $workshop->max_attendees,
            ],
            'statistics' => [
                'total_registered' => $totalRegistered,
                'total_attended' => $totalAttended,
                'total_cancelled' => $cancelled->count(),
                'attendance_rate' => round($attendanceRate, 2),
                'no_show_rate' => round(100 - $attendanceRate, 2),
            ],
            'attendees' => $attended->map(function ($registration) {
                return [
                    'user_name' => $registration->user->name,
                    'user_email' => $registration->user->email,
                    'attended_at' => $registration->attended_at,
                    'registration_id' => $registration->id,
                ];
            }),
            'registered_not_attended' => $registered->whereNull('attended_at')->map(function ($registration) {
                return [
                    'user_name' => $registration->user->name,
                    'user_email' => $registration->user->email,
                    'registered_at' => $registration->registered_at,
                ];
            }),
            'attendance_timeline' => $attendanceByHour,
        ];
    }

    /**
     * Get user's workshop registrations.
     */
    public function getUserRegistrations(User $user): Collection
    {
        return WorkshopRegistration::with(['workshop'])
            ->where('user_id', $user->id)
            ->where('status', '!=', 'cancelled')
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Get available workshops for registration.
     */
    public function getAvailableWorkshops(): Collection
    {
        return $this->workshopRepo->getAvailableWorkshops();
    }

    /**
     * Get workshop statistics.
     */
    public function getWorkshopStatistics(int $workshopId): array
    {
        $workshop = Workshop::with(['registrations'])->findOrFail($workshopId);

        $registrations = $workshop->registrations;
        $registered = $registrations->where('status', 'registered');
        $attended = $registrations->whereNotNull('attended_at');
        $cancelled = $registrations->where('status', 'cancelled');

        return [
            'capacity' => [
                'max' => $workshop->max_attendees,
                'registered' => $registered->count(),
                'attended' => $attended->count(),
                'available' => $workshop->max_attendees - $registered->count(),
            ],
            'registration' => [
                'total' => $registrations->count(),
                'active' => $registered->count(),
                'cancelled' => $cancelled->count(),
            ],
            'attendance' => [
                'count' => $attended->count(),
                'rate' => $registered->count() > 0 ? round(($attended->count() / $registered->count()) * 100, 2) : 0,
            ],
            'timeline' => [
                'is_registration_open' => $this->isRegistrationOpen($workshop),
                'is_ongoing' => $workshop->start_time <= now() && $workshop->end_time >= now(),
                'is_completed' => $workshop->end_time < now(),
            ],
        ];
    }

    /**
     * Check if registration is open for workshop.
     */
    private function isRegistrationOpen(Workshop $workshop): bool
    {
        $now = now();
        
        // Check if workshop has started
        if ($workshop->start_time <= $now) {
            return false;
        }

        // Check if registration deadline passed
        if ($workshop->registration_deadline && $workshop->registration_deadline < $now) {
            return false;
        }

        // Check capacity
        return $workshop->current_attendees < $workshop->max_attendees;
    }

    /**
     * Check if registration can be cancelled.
     */
    private function canCancelRegistration(WorkshopRegistration $registration): bool
    {
        // Cannot cancel if workshop already started
        if ($registration->workshop->start_time <= now()) {
            return false;
        }

        // Cannot cancel if already attended
        if ($registration->attended_at) {
            return false;
        }

        // Can cancel up to 1 hour before workshop starts
        return $registration->workshop->start_time->subHour() > now();
    }

    /**
     * Check if supervisor can scan workshop attendance.
     */
    private function canSupervisorScanWorkshop(Workshop $workshop, User $supervisor): bool
    {
        // Workshop supervisors can scan
        if ($supervisor->hasRole('workshop_supervisor')) {
            return true;
        }

        // Admin roles can scan any workshop
        if ($supervisor->hasRole(['hackathon_admin', 'system_admin'])) {
            return true;
        }

        // Track supervisors can scan workshops in their tracks
        if ($supervisor->hasRole('track_supervisor')) {
            return $supervisor->supervisedTracks()
                ->whereHas('workshops', function ($query) use ($workshop) {
                    $query->where('id', $workshop->id);
                })->exists();
        }

        return false;
    }

    /**
     * Update workshop capacity.
     */
    private function updateWorkshopCapacity(Workshop $workshop, int $change): void
    {
        $workshop->increment('current_attendees', $change);
    }

    /**
     * Generate unique barcode for registration.
     */
    private function generateBarcode(): string
    {
        do {
            $barcode = 'WS' . strtoupper(Str::random(8));
        } while (WorkshopRegistration::where('barcode', $barcode)->exists());

        return $barcode;
    }
}