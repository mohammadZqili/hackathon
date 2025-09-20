<?php

namespace App\Http\Controllers\Visitor;

use App\Http\Controllers\Controller;
use App\Models\Workshop;
use App\Services\WorkshopService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class WorkshopController extends Controller
{
    protected $workshopService;

    public function __construct(WorkshopService $workshopService)
    {
        $this->workshopService = $workshopService;
    }

    public function index()
    {
        $workshops = $this->workshopService->getAllWorkshops();

        return Inertia::render('Visitor/Workshops/All', [
            'workshops' => $workshops
        ]);
    }

    public function myWorkshops()
    {
        $userId = auth()->id();

        // Get workshops with registrations including barcode
        $workshops = \App\Models\Workshop::whereHas('registrations', function ($query) use ($userId) {
            $query->where('user_id', $userId);
        })->with(['speakers', 'organizations', 'registrations' => function ($query) use ($userId) {
            $query->where('user_id', $userId);
        }])->get();

        // Add registration data to each workshop
        $workshops = $workshops->map(function ($workshop) use ($userId) {
            $registration = $workshop->registrations->first();
            $workshop->barcode = $registration->barcode ?? null;
            $workshop->registration_status = $registration->status ?? 'registered';
            $workshop->attended_at = $registration->attended_at ?? null;
            $workshop->registered_at = $registration->registered_at ?? $registration->created_at;

            // Format dates for frontend
            $workshop->date = $workshop->start_time ? $workshop->start_time->format('Y-m-d') : null;
            $workshop->start_time_formatted = $workshop->start_time ? $workshop->start_time->format('H:i:s') : null;
            $workshop->end_time_formatted = $workshop->end_time ? $workshop->end_time->format('H:i:s') : null;

            // Remove the registrations collection to avoid sending unnecessary data
            unset($workshop->registrations);

            return $workshop;
        });

        // Calculate statistics
        $statistics = [
            'total_registered' => $workshops->count(),
            'attended' => $workshops->where('registration_status', 'attended')->count(),
            'upcoming' => $workshops->filter(function ($workshop) {
                return $workshop->start_time && $workshop->start_time->isFuture();
            })->count(),
            'completed' => $workshops->filter(function ($workshop) {
                return $workshop->end_time && $workshop->end_time->isPast();
            })->count(),
        ];

        return Inertia::render('Visitor/Workshops/My', [
            'workshops' => $workshops,
            'statistics' => $statistics
        ]);
    }

    public function register($id)
    {
        try {
            $this->workshopService->registerForWorkshop(auth()->id(), $id);
            return back()->with('success', 'Successfully registered for workshop');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function unregister($id)
    {
        try {
            $this->workshopService->unregisterFromWorkshop(auth()->id(), $id);
            return back()->with('success', 'Successfully unregistered from workshop');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
