<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TrackSupervisor\DashboardController;
use App\Http\Controllers\TrackSupervisor\TeamController;
use App\Http\Controllers\TrackSupervisor\IdeaController;
use App\Http\Controllers\TrackSupervisor\TrackController;
use App\Http\Controllers\TrackSupervisor\WorkshopController;
use App\Http\Controllers\TrackSupervisor\CheckinController;
use App\Http\Controllers\TrackSupervisor\NewsController;
use App\Http\Controllers\TrackSupervisor\SpeakerController;
use App\Http\Controllers\TrackSupervisor\OrganizationController;
use App\Http\Controllers\TrackSupervisor\SettingsController;
use App\Http\Controllers\TrackSupervisor\EditionController;
use App\Http\Controllers\TrackSupervisor\UserController;

Route::middleware(['auth', 'role:track_supervisor', 'track_supervisor_scope'])->prefix('track-supervisor')->name('track-supervisor.')->group(function () {

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Editions (read-only for track supervisors)
    Route::resource('editions', EditionController::class)->only(['index', 'show']);
    Route::get('/editions/{edition}/export', [EditionController::class, 'export'])->name('editions.export');
    Route::post('/editions/{edition}/set-current', [EditionController::class, 'setCurrent'])->name('editions.set-current');
    Route::post('/editions/{edition}/archive', [EditionController::class, 'archive'])->name('editions.archive');

    // Tracks Management (for assigned tracks only)
    Route::resource('tracks', TrackController::class);
    Route::get('/tracks/export', [TrackController::class, 'export'])->name('tracks.export');

    // Teams Management (for teams in assigned tracks)
    Route::resource('teams', TeamController::class);
    Route::post('/teams/{team}/approve', [TeamController::class, 'approve'])->name('teams.approve');
    Route::post('/teams/{team}/reject', [TeamController::class, 'reject'])->name('teams.reject');
    Route::post('/teams/{team}/add-member', [TeamController::class, 'addMember'])->name('teams.add-member');
    Route::delete('/teams/{team}/remove-member/{member}', [TeamController::class, 'removeMember'])->name('teams.remove-member');
    Route::get('/teams/export', [TeamController::class, 'export'])->name('teams.export');

    // Ideas Management (for ideas in assigned tracks)
    Route::resource('ideas', IdeaController::class);
    Route::get('/ideas/{idea}/review', [IdeaController::class, 'review'])->name('ideas.review');
    Route::post('/ideas/{idea}/review', [IdeaController::class, 'submitReview'])->name('ideas.submit-review');
    Route::post('/ideas/{idea}/process-review', [IdeaController::class, 'processReview'])->name('ideas.process-review');
    Route::post('/ideas/{idea}/approve', [IdeaController::class, 'approve'])->name('ideas.approve');
    Route::post('/ideas/{idea}/reject', [IdeaController::class, 'reject'])->name('ideas.reject');
    Route::post('/ideas/{idea}/need-edit', [IdeaController::class, 'needEdit'])->name('ideas.need-edit');
    Route::post('/ideas/{idea}/score', [IdeaController::class, 'updateScore'])->name('ideas.update-score');
    Route::get('/ideas/export', [IdeaController::class, 'export'])->name('ideas.export');

    // Workshops Management (full access within edition)
    Route::resource('workshops', WorkshopController::class);
    Route::get('/workshops/{workshop}/attendance', [WorkshopController::class, 'attendance'])->name('workshops.attendance');
    Route::post('/workshops/{workshop}/generate-qr', [WorkshopController::class, 'generateQR'])->name('workshops.generate-qr');
    Route::get('/workshops/export', [WorkshopController::class, 'export'])->name('workshops.export');

    // Check-in Management
    Route::resource('checkins', CheckinController::class)->only(['index', 'create', 'store']);
    Route::get('/checkins/search', [CheckinController::class, 'search'])->name('checkins.search');
    Route::post('/checkins/process-qr', [CheckinController::class, 'processQR'])->name('checkins.process-qr');
    Route::get('/checkins/workshop/{workshop}', [CheckinController::class, 'workshopAttendance'])->name('checkins.workshop');
    Route::post('/checkins/mark-attendance', [CheckinController::class, 'markAttendance'])->name('checkins.mark-attendance');
    Route::get('/checkins/generate-qr/{registration}', [CheckinController::class, 'generateQR'])->name('checkins.generate-qr');
    Route::get('/checkins/export', [CheckinController::class, 'export'])->name('checkins.export');

    // News Management
    Route::resource('news', NewsController::class);
    Route::post('/news/{news}/publish', [NewsController::class, 'publish'])->name('news.publish');
    Route::post('/news/{news}/unpublish', [NewsController::class, 'unpublish'])->name('news.unpublish');

    // Speakers Management
    Route::resource('speakers', SpeakerController::class);
    Route::get('/speakers/export', [SpeakerController::class, 'export'])->name('speakers.export');

    // Organizations Management
    Route::resource('organizations', OrganizationController::class);
    Route::get('/organizations/export', [OrganizationController::class, 'export'])->name('organizations.export');

    // Settings (limited access)
    Route::get('/settings', [SettingsController::class, 'index'])->name('settings.index');
    Route::post('/settings', [SettingsController::class, 'update'])->name('settings.update');

    // Reports
    Route::get('/reports', [DashboardController::class, 'reports'])->name('reports.index');
    Route::get('/reports/export', [DashboardController::class, 'exportReport'])->name('reports.export');
    Route::get('/analytics', [DashboardController::class, 'analytics'])->name('analytics.index');

    // User Search
    Route::get('/users/search', [UserController::class, 'search'])->name('users.search');
});