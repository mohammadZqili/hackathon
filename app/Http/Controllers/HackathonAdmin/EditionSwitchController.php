<?php

namespace App\Http\Controllers\HackathonAdmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class EditionSwitchController extends Controller
{
    /**
     * Switch the current edition for the hackathon admin
     */
    public function switch(Request $request)
    {
        $request->validate([
            'edition_id' => 'required|exists:editions,id'
        ]);

        $user = auth()->user();
        $editionId = $request->edition_id;

        // Verify user has access to this edition
        if (!$user->canAccessEdition($editionId)) {
            return back()->with('error', 'You do not have access to this edition.');
        }

        // Store in session
        session(['current_edition_id' => $editionId]);

        return back()->with('success', 'Edition switched successfully.');
    }

    /**
     * Get the current edition
     */
    public function current()
    {
        $editionId = session('current_edition_id');
        
        if (!$editionId) {
            $user = auth()->user();
            $accessibleEditions = $user->getAccessibleEditionIds();
            
            if (!empty($accessibleEditions)) {
                $editionId = $accessibleEditions[0];
                session(['current_edition_id' => $editionId]);
            }
        }

        return response()->json([
            'edition_id' => $editionId
        ]);
    }
}
