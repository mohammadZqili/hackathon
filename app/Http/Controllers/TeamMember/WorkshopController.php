<?php

namespace App\Http\Controllers\TeamMember;

use App\Http\Controllers\Controller;
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
        $workshops = $this->workshopService->getUpcomingWorkshops();
        $myWorkshops = $this->workshopService->getUserWorkshops(auth()->id());
        
        return Inertia::render('TeamMember/Workshops/Index', [
            'workshops' => $workshops,
            'myWorkshops' => $myWorkshops
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
