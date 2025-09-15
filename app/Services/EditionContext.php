<?php

namespace App\Services;

use App\Models\HackathonEdition;
use Illuminate\Support\Facades\Cache;

class EditionContext
{
    /**
     * Get the current edition
     * Priority: session > is_current flag > latest edition
     */
    public function current()
    {
        // Check session first
        $sessionEditionId = session('current_edition_id');
        if ($sessionEditionId) {
            $edition = HackathonEdition::find($sessionEditionId);
            if ($edition) {
                return $edition;
            }
        }

        // Check for edition marked as current
        $currentEdition = Cache::remember('current_edition', 3600, function () {
            return HackathonEdition::where('is_current', true)->first();
        });

        if ($currentEdition) {
            return $currentEdition;
        }

        // Fall back to the latest edition
        return HackathonEdition::latest('created_at')->first();
    }

    /**
     * Set the current edition in session
     */
    public function setCurrentEdition($edition)
    {
        if (is_numeric($edition)) {
            $edition = HackathonEdition::find($edition);
        }

        if ($edition) {
            session(['current_edition_id' => $edition->id]);
            return $edition;
        }

        return null;
    }

    /**
     * Clear the current edition from session
     */
    public function clearCurrentEdition()
    {
        session()->forget('current_edition_id');
        Cache::forget('current_edition');
    }

    /**
     * Check if an edition is the current one
     */
    public function isCurrent($edition)
    {
        $current = $this->current();
        if (!$current) {
            return false;
        }

        $editionId = is_object($edition) ? $edition->id : $edition;
        return $current->id === $editionId;
    }
}