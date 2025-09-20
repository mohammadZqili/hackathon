<?php

namespace App\Http\Controllers\TrackSupervisor;

use App\Http\Controllers\Controller;
use App\Models\Workshop;
use App\Services\WorkshopCheckinService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Carbon\Carbon;

class CheckinController extends Controller
{
    protected WorkshopCheckinService $checkinService;

    public function __construct(WorkshopCheckinService $checkinService)
    {
        $this->checkinService = $checkinService;
    }
    /**
     * Display workshops list for check-ins.
     */
    public function workshops()
    {
        $workshops = Workshop::with(['speakers', 'organizations', 'registrations'])
            ->where('is_active', true)
            ->orderBy('start_time', 'asc')
            ->get()
            ->map(function ($workshop) {
                $registrations = $workshop->registrations;
                return [
                    'id' => $workshop->id,
                    'title' => $workshop->title,
                    'description' => $workshop->description,
                    'start_time' => $workshop->start_time,
                    'date_time' => $workshop->start_time,
                    'speakers' => $workshop->speakers->pluck('name')->implode(', '),
                    'max_attendees' => $workshop->max_attendees,
                    'seats' => $workshop->max_attendees,
                    'registered_count' => $registrations->count(),
                    'checked_in_count' => $registrations->whereNotNull('attended_at')->count(),
                ];
            });

        return Inertia::render('TrackSupervisor/Checkins/Workshops', [
            'workshops' => $workshops,
        ]);
    }

    /**
     * Display specific workshop check-in page.
     */
    public function workshopCheckIn($workshopId)
    {
        try {
            // Use service to get all workshop check-in data
            $data = $this->checkinService->getWorkshopCheckinData($workshopId);

            // Get active workshops for dropdown
            $workshops = Workshop::where('is_active', true)
                ->orderBy('start_time', 'asc')
                ->get()
                ->map(function ($w) {
                    return [
                        'id' => $w->id,
                        'title' => $w->title,
                    ];
                });

            return Inertia::render('TrackSupervisor/Checkins/WorkshopDetail', array_merge($data, [
                'selectedWorkshop' => $workshopId,
                'workshops' => $workshops,
            ]));

        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => 'Workshop not found.']);
        }
    }

    /**
     * Display the check-ins management page.
     */
    public function index(Request $request)
    {
        // Show workshops list for check-ins
        $workshops = Workshop::with(['speakers', 'organizations', 'registrations'])
            ->where('is_active', true)
            ->orderBy('start_time', 'asc')
            ->get()
            ->map(function ($workshop) {
                $registrations = $workshop->registrations;
                return [
                    'id' => $workshop->id,
                    'title' => $workshop->title,
                    'description' => $workshop->description,
                    'start_time' => $workshop->start_time,
                    'date_time' => $workshop->start_time,
                    'speakers' => $workshop->speakers->pluck('name')->implode(', '),
                    'max_attendees' => $workshop->max_attendees,
                    'seats' => $workshop->max_attendees,
                    'registered_count' => $registrations->count(),
                    'checked_in_count' => $registrations->whereNotNull('attended_at')->count(),
                    'date_time' => Carbon::parse($workshop->start_time)->format('M d, Y h:i A'),
                    'edition' => $workshop->edition ? $workshop->edition->name : 'N/A',
                    'speakers' => $workshop->speakers->pluck('name')->implode(', '),
                    'seats' => $workshop->max_attendees ?? 0,
                    'registered_count' => $workshop->registrations()->count(),
                ];
            });

        // Get recent check-ins (last 50)
        $recentCheckIns = WorkshopRegistration::with(['user', 'workshop'])
            ->whereNotNull('attended_at')
            ->orderBy('attended_at', 'desc')
            ->take(50)
            ->get()
            ->map(function ($registration) {
                return [
                    'id' => $registration->id,
                    'name' => $registration->user->name ?? 'Guest',
                    'email' => $registration->user->email ?? 'N/A',
                    'workshop' => $registration->workshop->title ?? 'N/A',
                    'checkinTime' => Carbon::parse($registration->attended_at)->format('h:i A, M d, Y'),
                    'registered' => $registration->user_id !== null,
                    'barcode' => $registration->barcode,
                ];
            });

        // Calculate statistics
        $stats = $this->calculateStatistics($request->get('workshop_id'));

        return Inertia::render('TrackSupervisor/Checkins/Workshops', [
            'workshops' => $workshops,
        ]);
    }

    /**
     * Process QR code scan.
     */
    public function processQR(Request $request)
    {
        $request->validate([
            'code' => 'required|string',
            'workshop_id' => 'required|exists:workshops,id',
        ]);

        $code = $request->code;
        $workshopId = $request->workshop_id;

        // Use QR Code Service to parse the code
        $qrService = new QrCodeService();
        $parsedData = $qrService->parseWorkshopQrCode($code);

        // If we can parse the new format with email
        if ($parsedData && $parsedData['format'] === 'email_based') {
            // Verify workshop ID matches
            if ($parsedData['workshop_id'] != $workshopId) {
                return response()->json([
                    'success' => false,
                    'message' => 'This QR code is for a different workshop.'
                ], 400);
            }

            // Find user by email
            $user = User::where('email', $parsedData['user_email'])->first();

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not found with email: ' . $parsedData['user_email']
                ], 404);
            }

            // Find or create registration
            $registration = WorkshopRegistration::where('workshop_id', $workshopId)
                ->where('user_id', $user->id)
                ->first();

            if (!$registration) {
                // Create new registration
                $registration = WorkshopRegistration::create([
                    'workshop_id' => $workshopId,
                    'user_id' => $user->id,
                    'barcode' => Str::random(10) . '_' . time(),
                    'status' => 'attended',
                    'registered_at' => now(),
                    'attended_at' => now(),
                    'attendance_method' => 'qr_scan',
                    'marked_by' => auth()->id(),
                    'notes' => 'Auto-registered via QR scan',
                ]);

                $workshop = Workshop::find($workshopId);

                return response()->json([
                    'success' => true,
                    'message' => 'User registered and checked in successfully!',
                    'data' => [
                        'id' => $registration->id,
                        'name' => $user->name,
                        'email' => $user->email,
                        'workshop' => $workshop->title,
                        'type' => 'auto_registered',
                        'code' => $registration->barcode
                    ]
                ]);
            }
        } elseif ($parsedData && $parsedData['format'] === 'legacy') {
            // Handle legacy format
            $qrWorkshopId = $parsedData['workshop_id'];
            $registrationId = $parsedData['registration_id'];
            $barcode = $parsedData['barcode'];

            // Verify workshop ID matches
            if ($qrWorkshopId != $workshopId) {
                return response()->json([
                    'success' => false,
                    'message' => 'This QR code is for a different workshop.'
                ], 400);
            }

            // Find registration by barcode or ID
            $registration = WorkshopRegistration::where('workshop_id', $workshopId)
                ->where(function($query) use ($barcode, $registrationId) {
                    $query->where('barcode', $barcode);
                    if ($registrationId) {
                        $query->orWhere('id', $registrationId);
                    }
                })
                ->first();
        } else {
            // Try direct barcode lookup for backward compatibility
            $barcode = $code;
            $registration = WorkshopRegistration::where('workshop_id', $workshopId)
                ->where('barcode', $barcode)
                ->first();
        }

        // If no registration found, create walk-in
        if (!isset($registration) || !$registration) {
            // For email-based QR that somehow didn't match
            if ($parsedData && $parsedData['format'] === 'simple' && isset($parsedData['user_email'])) {
                $user = User::where('email', $parsedData['user_email'])->first();
                if ($user) {
                    $registration = WorkshopRegistration::create([
                        'workshop_id' => $workshopId,
                        'user_id' => $user->id,
                        'barcode' => Str::random(10) . '_' . time(),
                        'status' => 'attended',
                        'registered_at' => now(),
                        'attended_at' => now(),
                        'attendance_method' => 'qr_scan',
                        'marked_by' => auth()->id(),
                        'notes' => 'Walk-in registration via QR scan',
                    ]);
                }
            }

            // Final fallback - create guest registration
            if (!isset($registration) || !$registration) {
                $guestUserId = $this->getOrCreateGuestUser($code);

                $registration = WorkshopRegistration::create([
                    'workshop_id' => $workshopId,
                    'user_id' => $guestUserId,
                    'barcode' => Str::random(10) . '_' . time(),
                    'status' => 'attended',
                    'registered_at' => now(),
                    'attended_at' => now(),
                    'attendance_method' => 'qr_scan',
                    'marked_by' => auth()->id(),
                    'notes' => 'Walk-in registration via QR scan',
                ]);
            }

            $workshop = Workshop::find($workshopId);

            return response()->json([
                'success' => true,
                'message' => 'Walk-in attendee checked in successfully.',
                'data' => [
                    'id' => $registration->id,
                    'name' => isset($user) ? $user->name : 'Walk-in Guest',
                    'email' => isset($user) ? $user->email : null,
                    'workshop' => $workshop->title,
                    'type' => 'walk-in',
                    'code' => $registration->barcode
                ]
            ]);
        }

        // Check if already attended
        if ($registration->attended_at) {
            $user = $registration->user;
            $workshop = $registration->workshop;

            return response()->json([
                'success' => false,
                'message' => 'Already checked in at ' . Carbon::parse($registration->attended_at)->format('h:i A'),
                'data' => [
                    'id' => $registration->id,
                    'name' => $user ? $user->name : 'Guest',
                    'workshop' => $workshop->title,
                    'checkin_time' => Carbon::parse($registration->attended_at)->format('h:i A, M d, Y')
                ]
            ], 400);
        }

        // Mark attendance
        $registration->update([
            'attended_at' => now(),
            'status' => 'attended',
            'attendance_method' => 'qr_scan',
            'marked_by' => auth()->id(),
        ]);

        $user = $registration->user;
        $workshop = $registration->workshop;

        // Update workshop attendee count
        $workshop->increment('current_attendees');

        return response()->json([
            'success' => true,
            'message' => 'Check-in successful!',
            'data' => [
                'id' => $registration->id,
                'name' => $user ? $user->name : 'Guest',
                'email' => $user ? $user->email : null,
                'workshop' => $workshop->title,
                'type' => 'registered',
                'code' => $registration->barcode
            ]
        ]);
    }

    /**
     * Get or create a guest user for walk-ins.
     */
    private function getOrCreateGuestUser($identifier)
    {
        // Generate a unique guest user ID
        $guestId = 'guest_' . Str::random(10);

        // You might want to create actual guest user records
        // For now, we'll use the auth user ID
        return auth()->id();
    }

    /**
     * Generate QR code for a registration.
     */
    public function generateQR($registrationId)
    {
        $registration = WorkshopRegistration::with(['workshop', 'user'])->findOrFail($registrationId);

        // Generate QR code content
        $qrContent = sprintf(
            'WORKSHOP_%d_REG_%d_CODE_%s',
            $registration->workshop_id,
            $registration->id,
            $registration->barcode
        );

        return response()->json([
            'qr_content' => $qrContent,
            'registration' => [
                'id' => $registration->id,
                'workshop' => $registration->workshop->title,
                'user' => $registration->user ? $registration->user->name : 'Guest',
                'barcode' => $registration->barcode
            ]
        ]);
    }
    public function markAttendance(Request $request)
    {
        $request->validate([
            'code' => 'required|string',
            'workshop_id' => 'required|exists:workshops,id',
        ]);

        // Find registration by barcode
        $registration = WorkshopRegistration::where('barcode', $request->code)
            ->where('workshop_id', $request->workshop_id)
            ->first();

        if (!$registration) {
            // Create walk-in registration
            $registration = WorkshopRegistration::create([
                'workshop_id' => $request->workshop_id,
                'user_id' => auth()->id(), // Use a guest user ID or create one
                'barcode' => $request->code,
                'status' => 'attended',
                'registered_at' => now(),
                'attended_at' => now(),
                'attendance_method' => 'manual',
                'marked_by' => auth()->id(),
            ]);

            return redirect()->back()->with('success', 'Walk-in attendee checked in successfully.');
        }

        // Mark attendance if not already marked
        if ($registration->attended_at) {
            return redirect()->back()->with('info', 'Attendance already marked for this participant.');
        }

        $registration->update([
            'attended_at' => now(),
            'status' => 'attended',
            'attendance_method' => 'manual',
            'marked_by' => auth()->id(),
        ]);

        return redirect()->back()->with('success', 'Attendance marked successfully.');
    }

    /**
     * Search for a participant.
     */
    public function search(Request $request)
    {
        $request->validate([
            'query' => 'required|string|min:2',
        ]);

        $query = $request->get('query');

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

        return response()->json(['results' => $results]);
    }

    /**
     * Export attendance report.
     */
    public function export(Request $request)
    {
        $workshopId = $request->get('workshop_id');

        $query = WorkshopRegistration::with(['user', 'workshop']);

        if ($workshopId) {
            $query->where('workshop_id', $workshopId);
        }

        $registrations = $query->whereNotNull('attended_at')
            ->orderBy('attended_at', 'desc')
            ->get();

        // Generate CSV
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

        return response($csvData)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', 'attachment; filename="attendance_report_' . now()->format('Y-m-d_His') . '.csv"');
    }

    /**
     * Calculate attendance statistics.
     */
    private function calculateStatistics($workshopId = null)
    {
        $query = WorkshopRegistration::query();

        if ($workshopId) {
            $query->where('workshop_id', $workshopId);
        }

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
     * Get attendance details for a specific workshop.
     */
    public function workshopAttendance($workshopId)
    {
        $workshop = Workshop::with(['registrations.user', 'speakers', 'organizations'])
            ->findOrFail($workshopId);

        $attendees = $workshop->registrations()
            ->with('user')
            ->whereNotNull('attended_at')
            ->orderBy('attended_at', 'desc')
            ->get()
            ->map(function ($registration) {
                $markedBy = User::find($registration->marked_by);
                return [
                    'id' => $registration->id,
                    'name' => $registration->user->name ?? 'Guest',
                    'email' => $registration->user->email ?? 'N/A',
                    'checkin_time' => Carbon::parse($registration->attended_at)->format('h:i A, M d, Y'),
                    'is_registered' => $registration->user_id !== null,
                    'marked_by' => $markedBy ? $markedBy->name : 'System',
                ];
            });

        $stats = [
            'total_seats' => $workshop->max_attendees ?? 0,
            'registered' => $workshop->registrations()->count(),
            'attended' => $workshop->registrations()->whereNotNull('attended_at')->count(),
            'walk_ins' => $workshop->registrations()->whereNull('user_id')->count(),
            'attendance_rate' => $workshop->registrations()->count() > 0
                ? round(($workshop->registrations()->whereNotNull('attended_at')->count() / $workshop->registrations()->count()) * 100, 1)
                : 0,
        ];

        return Inertia::render('TrackSupervisor/Checkins/WorkshopDetail', [
            'workshop' => [
                'id' => $workshop->id,
                'title' => $workshop->title,
                'description' => $workshop->description,
                'date_time' => Carbon::parse($workshop->start_time)->format('M d, Y h:i A'),
                'speakers' => $workshop->speakers->pluck('name'),
                'organizations' => $workshop->organizations->pluck('name'),
            ],
            'attendees' => $attendees,
            'stats' => $stats,
        ]);
    }
}
