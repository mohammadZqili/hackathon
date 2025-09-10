<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Base\TeamController;
use App\Http\Controllers\Base\IdeaController;
use App\Http\Controllers\TrackSupervisor\DashboardController;

Route::middleware(['auth', 'check_track_supervisor'])->prefix('track-supervisor')->name('track-supervisor.')->group(function () {
    
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Teams Management (using shared controller - filtered to supervised tracks)
    Route::resource('teams', TeamController::class);
    Route::post('/teams/{team}/approve', [TeamController::class, 'approve'])->name('teams.approve');
    Route::post('/teams/{team}/reject', [TeamController::class, 'reject'])->name('teams.reject');
    
    // Ideas Management (using shared controller - filtered to supervised tracks)
    Route::resource('ideas', IdeaController::class);
    Route::get('/ideas/{idea}/review', [IdeaController::class, 'review'])->name('ideas.review');
    Route::post('/ideas/{idea}/review', [IdeaController::class, 'submitReview'])->name('ideas.submit-review');
    
    // Track-specific reports
    Route::get('/reports', [DashboardController::class, 'reports'])->name('reports.index');
    Route::get('/reports/export', [DashboardController::class, 'exportReport'])->name('reports.export');
});