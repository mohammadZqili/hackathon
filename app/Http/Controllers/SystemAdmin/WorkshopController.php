<?php

namespace App\Http\Controllers\SystemAdmin;

use App\Http\Controllers\Controller;
use App\Services\WorkshopService;
use App\Rules\WorkshopTimeValidation;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Workshop;
use App\Models\Speaker;
use App\Models\Organization;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class WorkshopController extends Controller
{
    protected WorkshopService $workshopService;

    public function __construct(WorkshopService $workshopService)
    {
        $this->workshopService = $workshopService;
    }
    public function index()
    {
        $workshops = Workshop::with(['speakers', 'organizations'])
            ->latest()
            ->paginate(15);

        $speakers = Speaker::orderBy('name')->get();
        $organizations = Organization::orderBy('name')->get();

        return Inertia::render('SystemAdmin/Workshops/Index', [
            'workshops' => $workshops,
            'speakers' => $speakers,
            'organizations' => $organizations
        ]);
    }

    public function create()
    {
        $speakers = Speaker::orderBy('name')->get();
        $organizations = Organization::orderBy('name')->get();

        \Log::info('Workshop Create Data', [
            'speakers_count' => $speakers->count(),
            'organizations_count' => $organizations->count()
        ]);

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
            'end_time' => ['required', 'date', new WorkshopTimeValidation()],
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
            'end_time.required' => 'Please specify when the workshop ends.',
            'location.required_unless' => 'Please specify the workshop location.',
            'remote_link.required_if' => 'Please provide the online meeting link.',
            'registration_deadline.before' => 'Registration must close before the workshop starts.',
        ]);

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
        $workshop->load(['hackathon', 'speakers', 'organizations', 'registrations']);

        return Inertia::render('SystemAdmin/Workshops/Show', [
            'workshop' => $workshop
        ]);
    }

    public function edit(Workshop $workshop)
    {
        $speakers = Speaker::orderBy('name')->get();
        $organizations = Organization::orderBy('name')->get();
        $workshop->load(['speakers', 'organizations']);

        return Inertia::render('SystemAdmin/Workshops/Edit', [
            'workshop' => $workshop,
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

        // Remove relation fields from validated data
        $workshopData = collect($validated)->except(['speaker_ids', 'organization_ids'])->toArray();
        
        // Update the workshop
        $workshop->update($workshopData);

        // Sync speakers if provided
        if (isset($validated['speaker_ids'])) {
            $speakerData = [];
            foreach ($validated['speaker_ids'] as $index => $speakerId) {
                $speakerData[$speakerId] = ['role' => 'main_speaker', 'order' => $index + 1];
            }
            $workshop->speakers()->sync($speakerData);
        }

        // Sync organizations if provided
        if (isset($validated['organization_ids'])) {
            $orgData = [];
            foreach ($validated['organization_ids'] as $orgId) {
                $orgData[$orgId] = ['role' => 'organizer'];
            }
            $workshop->organizations()->sync($orgData);
        }

        return redirect()->route('system-admin.workshops.index')
            ->with('success', 'Workshop updated successfully.');
    }

    public function destroy(Workshop $workshop)
    {
        $workshop->delete();

        return redirect()->route('system-admin.workshops.index')
            ->with('success', 'Workshop deleted successfully.');
    }

    public function attendance(Workshop $workshop)
    {
        $workshop->load('attendances.user');

        return Inertia::render('SystemAdmin/Workshops/Attendance', [
            'workshop' => $workshop
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
