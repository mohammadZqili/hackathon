<?php

namespace App\Http\Controllers\HackathonAdmin1;

use App\Http\Controllers\Controller;
use App\Models\HackathonEdition;
use Illuminate\Support\Facades\Auth;

class BaseController extends Controller
{
    protected $currentEdition;

    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            // Get the current edition for this hackathon admin
            $this->currentEdition = $this->getCurrentEdition();

            // Share with all views
            view()->share('currentEdition', $this->currentEdition);

            // Share with Inertia
            if (class_exists('\Inertia\Inertia')) {
                \Inertia\Inertia::share('currentEdition', $this->currentEdition);
            }

            return $next($request);
        });
    }

    protected function getCurrentEdition()
    {
        // Get the current active edition
        // In production, this might be based on the admin's assignment
        return HackathonEdition::where('is_current', true)->first()
            ?? HackathonEdition::latest()->first();
    }

    protected function scopeToEdition($query)
    {
        if ($this->currentEdition) {
            return $query->where('edition_id', $this->currentEdition->id);
        }
        return $query;
    }

    protected function checkEditionOwnership($model)
    {
        if ($this->currentEdition && isset($model->edition_id)) {
            return $model->edition_id === $this->currentEdition->id;
        }
        return true;
    }
}
