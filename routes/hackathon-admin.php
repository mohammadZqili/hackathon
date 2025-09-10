<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Base\TeamController;
use App\Http\Controllers\Base\IdeaController;
use App\Http\Controllers\HackathonAdmin\DashboardController;
use App\Http\Controllers\HackathonAdmin\TrackController;
use App\Http\Controllers\HackathonAdmin\WorkshopController;
use App\Http\Controllers\HackathonAdmin\NewsController;
use App\Http\Controllers\HackathonAdmin\CheckInController;
use App\Http\Controllers\HackathonAdmin\ReportController;

Route::middleware(['auth', 'check_hackathon_admin'])->prefix('hackathon-admin')->name('hackathon-admin.')->group(function () {
    
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Teams Management (using shared controller)
    Route::resource('teams', TeamController::class);
    Route::post('/teams/{team}/approve', [TeamController::class, 'approve'])->name('teams.approve');
    Route::post('/teams/{team}/reject', [TeamController::class, 'reject'])->name('teams.reject');
    
    // Ideas Management (using shared controller)
    Route::resource('ideas', IdeaController::class);
    Route::get('/ideas/{idea}/review', [IdeaController::class, 'review'])->name('ideas.review');
    Route::post('/ideas/{idea}/review', [IdeaController::class, 'submitReview'])->name('ideas.submit-review');
    
    // Tracks Management
    Route::resource('tracks', TrackController::class);
    Route::post('/tracks/{track}/assign-supervisor', [TrackController::class, 'assignSupervisor'])->name('tracks.assign-supervisor');
    
    // Workshops Management
    Route::resource('workshops', WorkshopController::class);
    Route::post('/workshops/{workshop}/assign-supervisor', [WorkshopController::class, 'assignSupervisor'])->name('workshops.assign-supervisor');
    Route::get('/workshops/{workshop}/attendance', [WorkshopController::class, 'attendance'])->name('workshops.attendance');
    Route::get('/workshops/{workshop}/qr-code', [WorkshopController::class, 'generateQRCode'])->name('workshops.qr-code');
    
    // News Management
    Route::resource('news', NewsController::class);
    Route::post('/news/{news}/publish', [NewsController::class, 'publish'])->name('news.publish');
    Route::post('/news/{news}/unpublish', [NewsController::class, 'unpublish'])->name('news.unpublish');
    
    // Check-ins
    Route::get('/check-ins', [CheckInController::class, 'index'])->name('check-ins.index');
    Route::post('/check-ins/scan', [CheckInController::class, 'scan'])->name('check-ins.scan');
    Route::get('/check-ins/export', [CheckInController::class, 'export'])->name('check-ins.export');
    
    // Reports
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/teams', [ReportController::class, 'teams'])->name('reports.teams');
    Route::get('/reports/ideas', [ReportController::class, 'ideas'])->name('reports.ideas');
    Route::get('/reports/workshops', [ReportController::class, 'workshops'])->name('reports.workshops');
    Route::get('/reports/export/{type}', [ReportController::class, 'export'])->name('reports.export');
    
    // Test route without specific controller
    Route::get('/test', function() {
        return \Inertia\Inertia::render('HackathonAdmin/Test', [
            'message' => 'HackathonAdmin route works!',
            'user' => auth()->user(),
        ]);
    })->name('test');
});
