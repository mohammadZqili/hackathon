<?php

namespace App\Http\Controllers\SystemAdmin;

use App\Http\Controllers\Controller;
use App\Services\WorkshopService;
use App\Services\SpeakerService;
use App\Services\OrganizationService;
use App\Rules\WorkshopTimeValidation;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Workshop;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Validator;

class WorkshopController extends Controller
{
    protected WorkshopService $workshopService;
    protected SpeakerService $speakerService;
    protected OrganizationService $organizationService;

    public function __construct(
        WorkshopService $workshopService,
        SpeakerService $speakerService,
        OrganizationService $organizationService
    ) {
        $this->workshopService = $workshopService;
        $this->speakerService = $speakerService;
        $this->organizationService = $organizationService;
    }
    public function index()
    {
        $workshops = $this->workshopService->getPaginatedWorkshops(15);
        $speakers = $this->speakerService->getAllSpeakers();
        $organizations = $this->organizationService->getAllOrganizations();

        return Inertia::render('SystemAdmin/Workshops/Index', [
            'workshops' => $workshops,
            'speakers' => $speakers,
            'organizations' => $organizations
        ]);
    }

    public function create()
    {
        $speakers = $this->speakerService->getAllSpeakers();
        $organizations = $this->organizationService->getAllOrganizations();

        return Inertia::render('SystemAdmin/Workshops/Create', [
            'speakers' => $speakers,
            'organizations' => $organizations
        ]);
    }

    public function store(Request $request)
    {
        // First level validation - basic field requirements
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:5000',
            'type' => 'nullable|string|in:workshop,seminar,lecture,panel',
            'start_time' => 'required|date|after:now',
            'end_time' => ['nullable', 'date'],
            'duration' => 'nullable|numeric|min:0.5|max:8', // Duration in hours
            'format' => 'required|in:online,offline,hybrid',
            'location' => 'required_unless:format,online|nullable|string|max:255',
            'remote_link' => 'required_if:format,online|nullable|url|max:500',
            'max_attendees' => 'nullable|integer|min:1|max:1000',
            'prerequisites' => 'nullable|string|max:1000',
            'materials' => 'nullable|array',
            'is_active' => 'boolean',
            'requires_registration' => 'boolean',
            'registration_deadline' => 'nullable|date|before:start_time|after:now',
            'speaker_ids' => 'nullable|array',
            'speaker_ids.*' => 'exists:speakers,id',
            'organization_ids' => 'nullable|array',
            'organization_ids.*' => 'exists:organizations,id',
        ], [
            // Custom error messages for better UX
            'start_time.after' => 'The workshop start time must be in the future.',
            'location.required_unless' => 'Please specify the workshop location.',
            'remote_link.required_if' => 'Please provide the online meeting link.',
            'registration_deadline.before' => 'Registration must close before the workshop starts.',
        ]);

        // Calculate end_time from duration if provided
        if (empty($validated['end_time']) && !empty($validated['duration'])) {
            $startTime = \Carbon\Carbon::parse($validated['start_time']);
            $durationHours = floatval($validated['duration']);
            $validated['end_time'] = $startTime->addHours($durationHours)->format('Y-m-d H:i:s');
        }

        // Validate end_time after calculation
        if (empty($validated['end_time'])) {
            return back()->withErrors(['end_time' => 'Please specify either end time or duration.'])->withInput();
        }

        // Apply time validation rule
        $validator = \Validator::make($validated, [
            'end_time' => [new WorkshopTimeValidation()]
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        // Use service layer for business logic and additional validation
        DB::beginTransaction();
        try {
            // Additional business validation through service
            $additionalValidation = $this->workshopService->validateWorkshopData($validated);
            if (!$additionalValidation['valid']) {
                throw ValidationException::withMessages($additionalValidation['errors']);
            }

            // Create workshop through service
            $workshop = $this->workshopService->createWorkshop(
                $validated,
                auth()->user()
            );

            DB::commit();

            return redirect()->route('system-admin.workshops.index')
                ->with('success', 'Workshop created successfully.');

        } catch (ValidationException $e) {
            DB::rollBack();
            throw $e;
        } catch (\Exception $e) {
            DB::rollBack();

            // Log the error for monitoring (Meta/Google best practice)
            \Log::error('Workshop creation failed', [
                'user_id' => auth()->id(),
                'data' => $validated,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return back()->withInput()
                ->withErrors(['error' => 'Failed to create workshop. Please try again.']);
        }
    }

    public function show(Workshop $workshop)
    {
        $workshopData = $this->workshopService->getWorkshopDetails($workshop->id);

        return Inertia::render('SystemAdmin/Workshops/Show', [
            'workshop' => $workshopData
        ]);
    }

    public function edit(Workshop $workshop)
    {
        $speakers = $this->speakerService->getAllSpeakers();
        $organizations = $this->organizationService->getAllOrganizations();
        $workshopData = $this->workshopService->getWorkshopWithRelations($workshop->id);

        return Inertia::render('SystemAdmin/Workshops/Edit', [
            'workshop' => $workshopData,
            'speakers' => $speakers,
            'organizations' => $organizations
        ]);
    }

    public function update(Request $request, Workshop $workshop)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'nullable|string|max:50',
            'start_time' => 'required|date',
            'end_time' => 'required|date|after:start_time',
            'format' => 'nullable|in:online,offline,hybrid',
            'location' => 'nullable|string|max:255',
            'max_attendees' => 'nullable|integer|min:1',
            'prerequisites' => 'nullable|string',
            'materials' => 'nullable|array',
            'is_active' => 'boolean',
            'requires_registration' => 'boolean',
            'registration_deadline' => 'nullable|date|before:start_time',
            'speaker_ids' => 'nullable|array',
            'speaker_ids.*' => 'exists:speakers,id',
            'organization_ids' => 'nullable|array',
            'organization_ids.*' => 'exists:organizations,id',
        ]);

        // Update workshop through service
        $this->workshopService->updateWorkshop($workshop->id, $validated, auth()->user());

        return redirect()->route('system-admin.workshops.index')
            ->with('success', 'Workshop updated successfully.');
    }

    public function destroy(Workshop $workshop)
    {
        $this->workshopService->deleteWorkshop($workshop->id);

        return redirect()->route('system-admin.workshops.index')
            ->with('success', 'Workshop deleted successfully.');
    }

    public function attendance(Workshop $workshop)
    {
        $workshopData = $this->workshopService->getWorkshopWithAttendance($workshop->id);

        return Inertia::render('SystemAdmin/Workshops/Attendance', [
            'workshop' => $workshopData
        ]);
    }

    public function generateQR(Workshop $workshop)
    {
        // TODO: Implement QR generation
        return response()->json(['message' => 'QR generation to be implemented']);
    }

    public function export()
    {
        // TODO: Implement export functionality
        return response()->json(['message' => 'Export functionality to be implemented']);
    }
}
