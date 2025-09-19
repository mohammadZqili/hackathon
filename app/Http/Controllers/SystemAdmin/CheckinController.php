<?php

namespace App\Http\Controllers\SystemAdmin;

use App\Http\Controllers\Controller;
use App\Models\Workshop;
use App\Models\WorkshopRegistration;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Carbon\Carbon;
use Illuminate\Support\Str;

class CheckinController extends Controller
{
    /**
     * Display the check-ins management page.
     */
    public function index(Request $request)
    {
        // Get active workshops for selection
        $workshops = Workshop::with(['edition', 'speakers', 'organizations', 'registrations'])
            ->where('is_active', true)
            ->orderBy('start_time', 'asc')
            ->get()
            ->map(function ($workshop) {
                return [
                    'id' => $workshop->id,
                    'title' => $workshop->title,
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

        return Inertia::render('SystemAdmin/Checkins/Index', [
            'workshops' => $workshops,
            'recentCheckIns' => $recentCheckIns,
            'stats' => $stats,
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

        // Try to parse the QR code
        $barcode = $code;
        $registrationId = null;
        $userEmail = null;

        // Check if it's JSON format from our workshop registration emails
        $jsonData = json_decode($code, true);
        if ($jsonData && isset($jsonData['workshop_id'])) {
            // Extract data from JSON QR code
            $qrWorkshopId = $jsonData['workshop_id'];
            $registrationId = $jsonData['registration_id'] ?? null;
            $userEmail = $jsonData['user_email'] ?? null;

            // Verify workshop ID matches
            if ($qrWorkshopId != $workshopId) {
                return response()->json([
                    'success' => false,
                    'message' => 'This QR code is for a different workshop.'
                ], 400);
            }
        }
        // Check if it's the old structured format
        elseif (preg_match('/WORKSHOP_(\d+)_REG_(\d+)_CODE_(.+)/', $code, $matches)) {
            $qrWorkshopId = $matches[1];
            $registrationId = $matches[2];
            $barcode = $matches[3];

            // Verify workshop ID matches
            if ($qrWorkshopId != $workshopId) {
                return response()->json([
                    'success' => false,
                    'message' => 'This QR code is for a different workshop.'
                ], 400);
            }
        }

        // Find registration by ID, email, or barcode
        $registration = WorkshopRegistration::where('workshop_id', $workshopId)
            ->where(function($query) use ($barcode, $registrationId, $userEmail) {
                if ($registrationId) {
                    $query->orWhere('id', $registrationId);
                }
                if ($userEmail) {
                    $query->orWhereHas('user', function($q) use ($userEmail) {
                        $q->where('email', $userEmail);
                    });
                }
                $query->orWhere('barcode', $barcode);
            })
            ->first();

        if (!$registration) {
            // Create walk-in registration with generated user ID
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

            $workshop = Workshop::find($workshopId);
            
            return response()->json([
                'success' => true,
                'message' => 'Walk-in attendee checked in successfully.',
                'data' => [
                    'id' => $registration->id,
                    'name' => 'Walk-in Guest',
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

        return Inertia::render('SystemAdmin/Checkins/WorkshopDetail', [
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
