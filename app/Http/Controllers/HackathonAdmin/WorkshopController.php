<?php

namespace App\Http\Controllers\HackathonAdmin;

use App\Http\Controllers\Controller;
use App\Models\Workshop;
use App\Models\HackathonEdition;
use App\Models\Hackathon;
use App\Models\Speaker;
use App\Models\WorkshopRegistration;
use App\Http\Requests\HackathonAdmin\CreateWorkshopRequest;
use App\Http\Requests\HackathonAdmin\UpdateWorkshopRequest;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Carbon\Carbon;

class WorkshopController extends Controller
{
    /**
     * Display a listing of workshops.
     */
    public function index(Request $request): Response
    {
        $currentEdition = HackathonEdition::where('is_current', true)->first();
        
        if (!$currentEdition) {
            return Inertia::render('HackathonAdmin/NoEdition');
        }

        // Get current hackathon for workshop queries
        $currentHackathon = Hackathon::where('is_current', true)->first();
        
        if (!$currentHackathon) {
            return Inertia::render('HackathonAdmin/NoEdition');
        }

        $query = Workshop::where('hackathon_id', $currentHackathon->id)
            ->with(['speaker', 'registrations']);

        // Apply filters
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $now = Carbon::now();
            switch ($request->status) {
                case 'upcoming':
                    $query->where('start_time', '>', $now);
                    break;
                case 'ongoing':
                    $query->where('start_time', '<=', $now)
                          ->where('end_time', '>=', $now);
                    break;
                case 'completed':
                    $query->where('end_time', '<', $now);
                    break;
            }
        }

        if ($request->filled('date')) {
            $date = Carbon::parse($request->date);
            $query->whereDate('start_time', $date);
        }

        $workshops = $query->orderBy('start_time')->paginate(15)->withQueryString();

        // Get statistics
        $statistics = [
            'total' => Workshop::where('hackathon_id', $currentHackathon->id)->count(),
            'upcoming' => Workshop::where('hackathon_id', $currentHackathon->id)
                ->where('start_time', '>', Carbon::now())
                ->count(),
            'ongoing' => Workshop::where('hackathon_id', $currentHackathon->id)
                ->where('start_time', '<=', Carbon::now())
                ->where('end_time', '>=', Carbon::now())
                ->count(),
            'completed' => Workshop::where('hackathon_id', $currentHackathon->id)
                ->where('end_time', '<', Carbon::now())
                ->count(),
            'total_registrations' => WorkshopRegistration::whereHas('workshop', function($q) use ($currentHackathon) {
                $q->where('hackathon_id', $currentHackathon->id);
            })->count(),
        ];

        $speakers = Speaker::all();

        return Inertia::render('HackathonAdmin/Workshops/Index', [
            'workshops' => $workshops,
            'statistics' => $statistics,
            'speakers' => $speakers,
            'filters' => $request->only(['search', 'status', 'date']),
        ]);
    }

    /**
     * Show the form for creating a new workshop.
     */
    public function create(): Response
    {
        $currentEdition = HackathonEdition::where('is_current', true)->first();
        
        if (!$currentEdition) {
            return Inertia::render('HackathonAdmin/NoEdition');
        }

        $speakers = Speaker::all();

        return Inertia::render('HackathonAdmin/Workshops/Create', [
            'speakers' => $speakers,
            'currentEdition' => $currentEdition,
        ]);
    }

    /**
     * Store a newly created workshop.
     */
    public function store(CreateWorkshopRequest $request)
    {
        $currentEdition = HackathonEdition::where('is_current', true)->first();
        
        if (!$currentEdition) {
            return back()->with('error', 'No current hackathon edition found.');
        }

        $currentHackathon = Hackathon::where('is_current', true)->first();
        
        if (!$currentHackathon) {
            return back()->with('error', 'No current hackathon found.');
        }

        $validated = $request->validated();
        $validated['hackathon_id'] = $currentHackathon->id;
        
        // Generate unique QR code identifier
        $validated['qr_code'] = 'WORKSHOP-' . strtoupper(uniqid());
        
        $workshop = Workshop::create($validated);

        return redirect()->route('hackathon-admin.workshops.index')
            ->with('success', 'Workshop created successfully.');
    }

    /**
     * Display the specified workshop.
     */
    public function show(Workshop $workshop): Response
    {
        $workshop->load(['speaker', 'registrations.user', 'attendances.user']);

        // Get attendance statistics
        $attendanceStats = [
            'registered' => $workshop->registrations()->count(),
            'attended' => $workshop->attendances()->count(),
            'attendance_rate' => $workshop->registrations()->count() > 0 
                ? round(($workshop->attendances()->count() / $workshop->registrations()->count()) * 100, 2)
                : 0,
        ];

        return Inertia::render('HackathonAdmin/Workshops/Show', [
            'workshop' => $workshop,
            'attendanceStats' => $attendanceStats,
        ]);
    }

    /**
     * Show the form for editing the workshop.
     */
    public function edit(Workshop $workshop): Response
    {
        $speakers = Speaker::all();

        return Inertia::render('HackathonAdmin/Workshops/Edit', [
            'workshop' => $workshop,
            'speakers' => $speakers,
        ]);
    }

    /**
     * Update the specified workshop.
     */
    public function update(UpdateWorkshopRequest $request, Workshop $workshop)
    {
        $workshop->update($request->validated());

        return redirect()->route('hackathon-admin.workshops.index')
            ->with('success', 'Workshop updated successfully.');
    }

    /**
     * Remove the specified workshop.
     */
    public function destroy(Workshop $workshop)
    {
        // Check if workshop has registrations
        if ($workshop->registrations()->count() > 0) {
            return back()->with('error', 'Cannot delete workshop with existing registrations.');
        }

        $workshop->delete();

        return redirect()->route('hackathon-admin.workshops.index')
            ->with('success', 'Workshop deleted successfully.');
    }

    /**
     * Display workshop attendance.
     */
    public function attendance(Workshop $workshop): Response
    {
        $workshop->load(['registrations.user', 'attendances.user']);

        $registrations = $workshop->registrations()
            ->with('user')
            ->get()
            ->map(function($registration) use ($workshop) {
                $attendance = $workshop->attendances()
                    ->where('user_id', $registration->user_id)
                    ->first();
                
                return [
                    'user' => $registration->user,
                    'registered_at' => $registration->created_at,
                    'attended' => $attendance ? true : false,
                    'attended_at' => $attendance ? $attendance->checked_in_at : null,
                ];
            });

        return Inertia::render('HackathonAdmin/Workshops/Attendance', [
            'workshop' => $workshop,
            'registrations' => $registrations,
        ]);
    }

    /**
     * Export workshop attendance.
     */
    public function exportAttendance(Workshop $workshop)
    {
        $workshop->load(['registrations.user', 'attendances.user']);

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="workshop-' . $workshop->id . '-attendance-' . date('Y-m-d') . '.csv"',
        ];

        $callback = function() use ($workshop) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['Name', 'Email', 'Team', 'Registered At', 'Attended', 'Checked In At']);
            
            foreach ($workshop->registrations as $registration) {
                $attendance = $workshop->attendances()
                    ->where('user_id', $registration->user_id)
                    ->first();
                
                fputcsv($file, [
                    $registration->user->name,
                    $registration->user->email,
                    $registration->user->team->name ?? 'N/A',
                    $registration->created_at->format('Y-m-d H:i'),
                    $attendance ? 'Yes' : 'No',
                    $attendance ? $attendance->checked_in_at->format('Y-m-d H:i') : 'N/A',
                ]);
            }
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Generate QR code for workshop.
     */
    public function generateQR(Workshop $workshop)
    {
        // Generate QR code data
        $qrData = [
            'type' => 'workshop',
            'id' => $workshop->id,
            'code' => $workshop->qr_code,
            'title' => $workshop->title,
        ];

        // Generate QR code image
        $qrCode = QrCode::format('png')
            ->size(300)
            ->margin(2)
            ->generate(json_encode($qrData));

        return response($qrCode, 200, [
            'Content-Type' => 'image/png',
            'Content-Disposition' => 'attachment; filename="workshop-' . $workshop->id . '-qr.png"',
        ]);
    }
}