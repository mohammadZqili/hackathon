<?php

namespace App\Http\Controllers\HackathonAdmin1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CheckInController extends Controller
{
    /**
     * Display the check-in dashboard
     */
    public function index()
    {
        return Inertia::render('HackathonAdmin/CheckIns/Index', [
            'checkIns' => []  // Will be implemented with actual data
        ]);
    }

    /**
     * Process QR code scan for check-in
     */
    public function scan(Request $request)
    {
        $request->validate([
            'qr_code' => 'required|string'
        ]);

        // TODO: Implement actual check-in logic

        return back()->with('success', 'Check-in successful!');
    }

    /**
     * Export check-in data
     */
    public function export(Request $request)
    {
        // TODO: Implement export functionality

        return response()->download('check-ins.csv');
    }
}
