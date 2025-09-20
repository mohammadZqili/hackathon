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

        // Get recent check-ins using service (follows CLAUDE.md pattern)
        $recentCheckIns = $this->checkinService->getRecentCheckInsFormatted(50);

        // Calculate statistics using service
        $workshopId = $request->get('workshop_id');
        $stats = $workshopId ? $this->checkinService->getWorkshopAttendanceStats($workshopId) : [];

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

        try {
            // Use service to process QR code
            $result = $this->checkinService->processQRCode(
                $request->code,
                $request->workshop_id,
                auth()->user()
            );

            $statusCode = $result['success'] ? 200 : 400;
            return response()->json($result, $statusCode);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to process QR code: ' . $e->getMessage()
            ], 500);
        }
    }


    /**
     * Generate QR code for a registration.
     */
    public function generateQR($registrationId)
    {
        try {
            // Use service to generate QR code (follows CLAUDE.md pattern)
            $result = $this->checkinService->generateQRCode($registrationId);
            return response()->json($result);

        } catch (\Exception $e) {
            return response()->json(['error' => 'Registration not found.'], 404);
        }
    }
    public function markAttendance(Request $request)
    {
        $request->validate([
            'code' => 'required|string',
            'workshop_id' => 'required|exists:workshops,id',
        ]);

        try {
            // Use service to mark manual attendance
            $result = $this->checkinService->markManualAttendance(
                $request->code,
                $request->workshop_id,
                auth()->user()
            );

            $message = $result['success'] ? 'success' : 'info';
            return redirect()->back()->with($message, $result['message']);

        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => 'Failed to mark attendance: ' . $e->getMessage()]);
        }
    }

    /**
     * Search for a participant.
     */
    public function search(Request $request)
    {
        $request->validate([
            'query' => 'required|string|min:2',
        ]);

        try {
            $results = $this->checkinService->searchParticipants($request->get('query'));
            return response()->json($results);

        } catch (\Exception $e) {
            return response()->json(['error' => 'Search failed: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Export attendance report.
     */
    public function export(Request $request)
    {
        try {
            $workshopId = $request->get('workshop_id');
            $csvData = $this->checkinService->exportAttendanceReport($workshopId);

            return response($csvData)
                ->header('Content-Type', 'text/csv')
                ->header('Content-Disposition', 'attachment; filename="attendance_report_' . now()->format('Y-m-d_His') . '.csv"');

        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => 'Export failed: ' . $e->getMessage()]);
        }
    }


    /**
     * Get attendance details for a specific workshop.
     */
    public function workshopAttendance($workshopId)
    {
        try {
            // Use service to get workshop check-in data
            $data = $this->checkinService->getWorkshopCheckinData($workshopId);

            return Inertia::render('TrackSupervisor/Checkins/WorkshopDetail', $data);

        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => 'Workshop not found.']);
        }
    }
}
