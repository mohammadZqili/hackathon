<?php

namespace App\Http\Controllers\TrackSupervisor;

use App\Http\Controllers\Controller;
use App\Services\WorkshopService;
use App\Services\SpeakerService;
use App\Services\OrganizationService;
use App\Services\EditionContext;
use App\Services\TeamService;
use App\Rules\WorkshopTimeValidation;
use App\Notifications\WorkshopCreatedNotification;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Workshop;
use App\Models\Team;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Notification;

class WorkshopController extends Controller
{
    protected WorkshopService $workshopService;
    protected SpeakerService $speakerService;
    protected OrganizationService $organizationService;
    protected EditionContext $editionContext;
    protected TeamService $teamService;

    public function __construct(
        WorkshopService $workshopService,
        SpeakerService $speakerService,
        OrganizationService $organizationService,
        EditionContext $editionContext,
        TeamService $teamService
    ) {
        $this->workshopService = $workshopService;
        $this->speakerService = $speakerService;
        $this->organizationService = $organizationService;
        $this->editionContext = $editionContext;
        $this->teamService = $teamService;
    }
    public function index()
    {
        $edition = $this->editionContext->current();

        // Filter workshops by current edition (using hackathon_edition_id)
        $workshops = Workshop::where('hackathon_id', $edition->id)
            ->with(['speakers', 'organizations', 'attendees'])
            ->paginate(15);

        $speakers = $this->speakerService->getAllSpeakers();
        $organizations = $this->organizationService->getAllOrganizations();

        return Inertia::render('TrackSupervisor/Workshops/Index', [
            'workshops' => $workshops,
            'speakers' => $speakers,
            'organizations' => $organizations,
            'current_edition' => $edition
        ]);
    }

    public function create()
    {
        $edition = $this->editionContext->current();
        $speakers = $this->speakerService->getAllSpeakers();
        $organizations = $this->organizationService->getAllOrganizations();

        return Inertia::render('TrackSupervisor/Workshops/Create', [
            'speakers' => $speakers,
            'current_edition' => $edition,
            'organizations' => $organizations
        ]);
    }

    public function store(Request $request)
    {
        // First level validation - basic field requirements
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:5000',
            'type' => 'required|string|in:workshop,seminar,lecture,panel',
            'start_time' => 'required|date_format:Y-m-d H:i:s|after:now',
            'end_time' => ['nullable', 'date_format:Y-m-d H:i:s'],
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

            // Add current edition to the validated data
            $edition = $this->editionContext->current();
            $validated['hackathon_edition_id'] = $edition->id;

            // Create workshop through service
            $workshop = $this->workshopService->createWorkshop(
                $validated,
                auth()->user()
            );

            // Send notifications to all track members and team leaders
//            $this->sendWorkshopNotifications($workshop);

            DB::commit();

            return redirect()->route('track-supervisor.workshops.index')
                ->with('success', 'Workshop created successfully!');

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

        return Inertia::render('TrackSupervisor/Workshops/Show', [
            'workshop' => $workshopData
        ]);
    }

    public function edit(Workshop $workshop)
    {
        $speakers = $this->speakerService->getAllSpeakers();
        $organizations = $this->organizationService->getAllOrganizations();
        $workshopData = $this->workshopService->getWorkshopWithRelations($workshop->id);

        return Inertia::render('TrackSupervisor/Workshops/Edit', [
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

        return redirect()->route('track-supervisor.workshops.index')
            ->with('success', 'Workshop updated successfully.');
    }

    public function destroy(Workshop $workshop)
    {
        $this->workshopService->deleteWorkshop($workshop->id);

        return redirect()->route('track-supervisor.workshops.index')
            ->with('success', 'Workshop deleted successfully.');
    }

    public function attendance(Workshop $workshop)
    {
        $workshopData = $this->workshopService->getWorkshopWithAttendance($workshop->id);

        return Inertia::render('TrackSupervisor/Workshops/Attendance', [
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

    /**
     * Send workshop notifications to all track members and team leaders
     *
     * @param Workshop $workshop
     * @return void
     */
    protected function sendWorkshopNotifications(Workshop $workshop)
    {
        try {
            // Get ALL teams from ALL tracks (not just supervisor's track)
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
            $uniqueUsers = $usersToNotify->unique('id');

            // Send notification to each user
            $successCount = 0;
            $failCount = 0;

            foreach ($uniqueUsers as $userToNotify) {
                try {
                    $userToNotify->notify(new WorkshopCreatedNotification($workshop));
                    $successCount++;
                } catch (\Exception $e) {
                    $failCount++;
                    \Log::error('Failed to send workshop notification', [
                        'user_id' => $userToNotify->id,
                        'workshop_id' => $workshop->id,
                        'error' => $e->getMessage()
                    ]);
                }
            }

            \Log::info('Workshop notifications sent to ALL tracks', [
                'workshop_id' => $workshop->id,
                'workshop_title' => $workshop->title,
                'total_recipients' => $uniqueUsers->count(),
                'successful' => $successCount,
                'failed' => $failCount
            ]);

        } catch (\Exception $e) {
            \Log::error('Failed to send workshop notifications', [
                'workshop_id' => $workshop->id,
                'error' => $e->getMessage()
            ]);
        }
    }
}
