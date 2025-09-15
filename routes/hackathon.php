<?php

use Illuminate\Support\Facades\Route;

// Hackathon Controllers
use App\Http\Controllers\SystemAdmin\DashboardController as SystemAdminDashboardController;
use App\Http\Controllers\SystemAdmin\HackathonEditionController as SystemAdminEditionController;
use App\Http\Controllers\SystemAdmin\UserController as SystemAdminUserController;
use App\Http\Controllers\SystemAdmin\TeamController as SystemAdminTeamController;
use App\Http\Controllers\SystemAdmin\TrackController as SystemAdminTrackController;
use App\Http\Controllers\SystemAdmin\IdeaController as SystemAdminIdeaController;
use App\Http\Controllers\SystemAdmin\WorkshopController as SystemAdminWorkshopController;
use App\Http\Controllers\SystemAdmin\SpeakerController as SystemAdminSpeakerController;
use App\Http\Controllers\SystemAdmin\OrganizationController as SystemAdminOrganizationController;
use App\Http\Controllers\SystemAdmin\NewsController as SystemAdminNewsController;
use App\Http\Controllers\SystemAdmin\SettingsController as SystemAdminSettingsController;
use App\Http\Controllers\SystemAdmin\ReportController as SystemAdminReportController;
use App\Http\Controllers\SystemAdmin\CheckinController as SystemAdminCheckinController;

use App\Http\Controllers\HackathonAdmin\DashboardController as HackathonAdminDashboardController;
use App\Http\Controllers\HackathonAdmin\HackathonEditionController as HackathonAdminEditionController;
use App\Http\Controllers\HackathonAdmin\UserController as HackathonAdminUserController;
use App\Http\Controllers\HackathonAdmin\TeamController as HackathonAdminTeamController;
use App\Http\Controllers\HackathonAdmin\TrackController as HackathonAdminTrackController;
use App\Http\Controllers\HackathonAdmin\IdeaController as HackathonAdminIdeaController;
use App\Http\Controllers\HackathonAdmin\WorkshopController as HackathonAdminWorkshopController;
use App\Http\Controllers\HackathonAdmin\SpeakerController as HackathonAdminSpeakerController;
use App\Http\Controllers\HackathonAdmin\OrganizationController as HackathonAdminOrganizationController;
use App\Http\Controllers\HackathonAdmin\NewsController as HackathonAdminNewsController;
use App\Http\Controllers\HackathonAdmin\SettingsController as HackathonAdminSettingsController;
use App\Http\Controllers\HackathonAdmin\ReportController as HackathonAdminReportController;
use App\Http\Controllers\HackathonAdmin\CheckinController as HackathonAdminCheckinController;


//use App\Http\Controllers\HackathonAdmin1\DashboardController as HackathonAdminDashboardController;
//use App\Http\Controllers\HackathonAdmin1\TeamController as HackathonAdminTeamController;
//use App\Http\Controllers\HackathonAdmin1\IdeaController as HackathonAdminIdeaController;
//use App\Http\Controllers\HackathonAdmin1\TrackController as HackathonAdminTrackController;
//use App\Http\Controllers\HackathonAdmin1\WorkshopController as HackathonAdminWorkshopController;
//use App\Http\Controllers\HackathonAdmin1\NewsController as HackathonAdminNewsController;


//track supervisor
use App\Http\Controllers\TrackSupervisor\DashboardController as TrackSupervisorDashboardController;
use App\Http\Controllers\TrackSupervisor\HackathonEditionController as TrackSupervisorEditionController;
use App\Http\Controllers\TrackSupervisor\UserController as TrackSupervisorUserController;
use App\Http\Controllers\TrackSupervisor\TeamController as TrackSupervisorTeamController;
use App\Http\Controllers\TrackSupervisor\TrackController as TrackSupervisorTrackController;
use App\Http\Controllers\TrackSupervisor\IdeaController as TrackSupervisorIdeaController;
use App\Http\Controllers\TrackSupervisor\WorkshopController as TrackSupervisorWorkshopController;
use App\Http\Controllers\TrackSupervisor\SpeakerController as TrackSupervisorSpeakerController;
use App\Http\Controllers\TrackSupervisor\OrganizationController as TrackSupervisorOrganizationController;
use App\Http\Controllers\TrackSupervisor\NewsController as TrackSupervisorNewsController;
use App\Http\Controllers\TrackSupervisor\SettingsController as TrackSupervisorSettingsController;
use App\Http\Controllers\TrackSupervisor\ReportController as TrackSupervisorReportController;
use App\Http\Controllers\TrackSupervisor\CheckinController as TrackSupervisorCheckinController;




//use App\Http\Controllers\TrackSupervisor1\DashboardController as TrackSupervisorDashboardController;
//use App\Http\Controllers\TrackSupervisor1\IdeaController as TrackSupervisorIdeaController;
//use App\Http\Controllers\TrackSupervisor1\WorkshopController as TrackSupervisorWorkshopController;

use App\Http\Controllers\TeamLead\DashboardController as TeamLeaderDashboardController;
use App\Http\Controllers\TeamLead\TeamController as TeamLeaderTeamController;
use App\Http\Controllers\TeamLead\IdeaController as TeamLeaderIdeaController;

use App\Http\Controllers\TeamMember\DashboardController as TeamMemberDashboardController;
use App\Http\Controllers\TeamMember\TeamController as TeamMemberTeamController;
use App\Http\Controllers\TeamMember\WorkshopController as TeamMemberWorkshopController;

/*
|--------------------------------------------------------------------------
| SYSTEM ADMIN ROUTES
|--------------------------------------------------------------------------
| Routes for system administrators with full system control
*/

Route::middleware(['auth', 'role:system_admin|permission:manage-hackathon-editions'])->prefix('system-admin')->name('system-admin.')->group(function () {
    // Dashboard
    Route::get('/dashboard', [SystemAdminDashboardController::class, 'index'])->name('dashboard');

    // Hackathon Edition Management
    Route::resource('editions', SystemAdminEditionController::class);
    Route::post('editions/{edition}/set-current', [SystemAdminEditionController::class, 'setCurrent'])->name('editions.set-current');
    Route::post('editions/{edition}/archive', [SystemAdminEditionController::class, 'archive'])->name('editions.archive');
    Route::get('editions/{edition}/export', [SystemAdminEditionController::class, 'export'])->name('editions.export');

    // User Management (All Users Across All Editions)
    Route::resource('users', SystemAdminUserController::class);
    Route::post('users/{user}/activate', [SystemAdminUserController::class, 'activate'])->name('users.activate');
    Route::post('users/{user}/deactivate', [SystemAdminUserController::class, 'deactivate'])->name('users.deactivate');
    Route::post('users/bulk-action', [SystemAdminUserController::class, 'bulkAction'])->name('users.bulk-action');
    Route::get('users/export', [SystemAdminUserController::class, 'export'])->name('users.export');

    // Global Team Management
    Route::resource('teams', SystemAdminTeamController::class);
    Route::post('teams/{team}/add-member', [SystemAdminTeamController::class, 'addMember'])->name('teams.add-member');
    Route::delete('teams/{team}/remove-member/{user}', [SystemAdminTeamController::class, 'removeMember'])->name('teams.remove-member');
    Route::get('teams/export', [SystemAdminTeamController::class, 'export'])->name('teams.export');
    Route::get('users/search', [SystemAdminUserController::class, 'search'])->name('users.search');

    // Global Track Management
    Route::resource('tracks', SystemAdminTrackController::class);
    Route::get('tracks/export', [SystemAdminTrackController::class, 'export'])->name('tracks.export');

    // Global Idea Management
    Route::resource('ideas', SystemAdminIdeaController::class);
    Route::get('ideas/{idea}/review', [SystemAdminIdeaController::class, 'review'])->name('ideas.review');
    Route::post('ideas/{idea}/process-review', [SystemAdminIdeaController::class, 'processReview'])->name('ideas.process-review');
    Route::post('ideas/{idea}/review/accept', [SystemAdminIdeaController::class, 'accept'])->name('ideas.review.accept');
    Route::post('ideas/{idea}/review/reject', [SystemAdminIdeaController::class, 'reject'])->name('ideas.review.reject');
    Route::post('ideas/{idea}/review/need-edit', [SystemAdminIdeaController::class, 'needEdit'])->name('ideas.review.need-edit');
    Route::post('ideas/{idea}/assign-supervisor', [SystemAdminIdeaController::class, 'assignSupervisor'])->name('ideas.assign-supervisor');
    Route::post('ideas/{idea}/update-score', [SystemAdminIdeaController::class, 'updateScore'])->name('ideas.update-score');
    Route::post('ideas/{idea}/score', [SystemAdminIdeaController::class, 'updateScore'])->name('ideas.score');
    Route::get('ideas/{idea}/files/{file}/download', [SystemAdminIdeaController::class, 'downloadFile'])->name('ideas.download-file');
    Route::get('ideas/statistics', [SystemAdminIdeaController::class, 'statistics'])->name('ideas.statistics');
    Route::get('ideas/export', [SystemAdminIdeaController::class, 'export'])->name('ideas.export');

    // Workshop Management (All Editions)
    Route::resource('workshops', SystemAdminWorkshopController::class);
    Route::get('workshops/{workshop}/attendance', [SystemAdminWorkshopController::class, 'attendance'])->name('workshops.attendance');
    Route::post('workshops/{workshop}/generate-qr', [SystemAdminWorkshopController::class, 'generateQR'])->name('workshops.generate-qr');
    Route::get('workshops/export', [SystemAdminWorkshopController::class, 'export'])->name('workshops.export');

    // Speaker Management
    Route::resource('speakers', SystemAdminSpeakerController::class);
    Route::post('speakers/{speaker}/activate', [SystemAdminSpeakerController::class, 'activate'])->name('speakers.activate');
    Route::post('speakers/{speaker}/deactivate', [SystemAdminSpeakerController::class, 'deactivate'])->name('speakers.deactivate');

    // Organization Management
    Route::resource('organizations', SystemAdminOrganizationController::class);
    Route::post('organizations/{organization}/activate', [SystemAdminOrganizationController::class, 'activate'])->name('organizations.activate');
    Route::post('organizations/{organization}/deactivate', [SystemAdminOrganizationController::class, 'deactivate'])->name('organizations.deactivate');

    // News Management (All Editions)
    Route::get('news/media-center', [SystemAdminNewsController::class, 'mediaCenter'])->name('news.media-center');
    Route::post('news/upload-temp', [SystemAdminNewsController::class, 'uploadTemp'])->name('news.upload-temp');
    Route::delete('news/delete-temp', [SystemAdminNewsController::class, 'deleteTemp'])->name('news.delete-temp');
    Route::get('news/media/{mediaId}', [SystemAdminNewsController::class, 'getMedia'])->name('news.get-media');
    Route::delete('news/media/{mediaId}', [SystemAdminNewsController::class, 'deleteMedia'])->name('news.media.delete');
    Route::resource('news', SystemAdminNewsController::class);
    Route::post('news/{news}/publish', [SystemAdminNewsController::class, 'publish'])->name('news.publish');
    Route::post('news/{news}/unpublish', [SystemAdminNewsController::class, 'unpublish'])->name('news.unpublish');

    // System Settings
    Route::prefix('settings')->name('settings.')->group(function () {
        Route::get('/', [SystemAdminSettingsController::class, 'index'])->name('index');
        Route::get('/smtp', [SystemAdminSettingsController::class, 'smtp'])->name('smtp');
        Route::post('/smtp', [SystemAdminSettingsController::class, 'updateSmtp'])->name('smtp.update');
        Route::get('/branding', [SystemAdminSettingsController::class, 'branding'])->name('branding');
        Route::post('/branding', [SystemAdminSettingsController::class, 'updateBranding'])->name('branding.update');
        Route::get('/twitter', [SystemAdminSettingsController::class, 'twitter'])->name('twitter');
        Route::post('/twitter', [SystemAdminSettingsController::class, 'updateTwitter'])->name('twitter.update');
        Route::get('/sms', [SystemAdminSettingsController::class, 'sms'])->name('sms');
        Route::post('/sms', [SystemAdminSettingsController::class, 'updateSms'])->name('sms.update');
        Route::post('/notifications', [SystemAdminSettingsController::class, 'updateNotifications'])->name('notifications.update');
    });

    // Reports & Analytics
    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('/', [SystemAdminReportController::class, 'index'])->name('index');
        Route::get('/users', [SystemAdminReportController::class, 'users'])->name('users');
        Route::get('/teams', [SystemAdminReportController::class, 'teams'])->name('teams');
        Route::get('/ideas', [SystemAdminReportController::class, 'ideas'])->name('ideas');
        Route::get('/workshops', [SystemAdminReportController::class, 'workshops'])->name('workshops');
        Route::get('/system-health', [SystemAdminReportController::class, 'systemHealth'])->name('system-health');
        Route::post('/generate', [SystemAdminReportController::class, 'generateReport'])->name('generate');
        Route::post('/export-pdf', [SystemAdminReportController::class, 'exportPdf'])->name('export-pdf');
        Route::post('/schedule', [SystemAdminReportController::class, 'scheduleReports'])->name('schedule');
    });

    // Check-ins Management
    Route::prefix('checkins')->name('checkins.')->group(function () {
        Route::get('/', [SystemAdminCheckinController::class, 'index'])->name('index');
        Route::post('/process-qr', [SystemAdminCheckinController::class, 'processQR'])->name('process-qr');
        Route::post('/mark-attendance', [SystemAdminCheckinController::class, 'markAttendance'])->name('mark-attendance');
        Route::get('/search', [SystemAdminCheckinController::class, 'search'])->name('search');
        Route::get('/export', [SystemAdminCheckinController::class, 'export'])->name('export');
        Route::get('/workshop/{workshop}', [SystemAdminCheckinController::class, 'workshopAttendance'])->name('workshop-attendance');
        Route::get('/generate-qr/{registration}', [SystemAdminCheckinController::class, 'generateQR'])->name('generate-qr');
    });
});

/*
|--------------------------------------------------------------------------
| HACKATHON ADMIN ROUTES
|--------------------------------------------------------------------------
| Routes for hackathon administrators managing current edition
*/

Route::middleware(['auth', 'role:hackathon_admin|permission:manage-current-edition'])->prefix('hackathon-admin')->name('hackathon-admin.')->group(function () {
    // Dashboard
    Route::get('/dashboard', [HackathonAdminDashboardController::class, 'index'])->name('dashboard');

    // Hackathon Edition Management
    Route::resource('editions', HackathonAdminEditionController::class);
    Route::post('editions/{edition}/set-current', [HackathonAdminEditionController::class, 'setCurrent'])->name('editions.set-current');
    Route::post('editions/{edition}/archive', [HackathonAdminEditionController::class, 'archive'])->name('editions.archive');
    Route::get('editions/{edition}/export', [HackathonAdminEditionController::class, 'export'])->name('editions.export');

    // User Management (All Users Across All Editions)
    Route::resource('users', HackathonAdminUserController::class);
    Route::post('users/{user}/activate', [HackathonAdminUserController::class, 'activate'])->name('users.activate');
    Route::post('users/{user}/deactivate', [HackathonAdminUserController::class, 'deactivate'])->name('users.deactivate');
    Route::post('users/bulk-action', [HackathonAdminUserController::class, 'bulkAction'])->name('users.bulk-action');
    Route::get('users/export', [HackathonAdminUserController::class, 'export'])->name('users.export');

    // Global Team Management
    Route::resource('teams', HackathonAdminTeamController::class);
    Route::post('teams/{team}/add-member', [HackathonAdminTeamController::class, 'addMember'])->name('teams.add-member');
    Route::delete('teams/{team}/remove-member/{user}', [HackathonAdminTeamController::class, 'removeMember'])->name('teams.remove-member');
    Route::get('teams/export', [HackathonAdminTeamController::class, 'export'])->name('teams.export');
    Route::get('users/search', [HackathonAdminUserController::class, 'search'])->name('users.search');

    // Global Track Management
    Route::resource('tracks', HackathonAdminTrackController::class);
    Route::get('tracks/export', [HackathonAdminTrackController::class, 'export'])->name('tracks.export');

    // Global Idea Management
    Route::resource('ideas', HackathonAdminIdeaController::class);
    Route::get('ideas/{idea}/review', [HackathonAdminIdeaController::class, 'review'])->name('ideas.review');
    Route::post('ideas/{idea}/process-review', [HackathonAdminIdeaController::class, 'processReview'])->name('ideas.process-review');
    Route::post('ideas/{idea}/review/accept', [HackathonAdminIdeaController::class, 'accept'])->name('ideas.review.accept');
    Route::post('ideas/{idea}/review/reject', [HackathonAdminIdeaController::class, 'reject'])->name('ideas.review.reject');
    Route::post('ideas/{idea}/review/need-edit', [HackathonAdminIdeaController::class, 'needEdit'])->name('ideas.review.need-edit');
    Route::post('ideas/{idea}/assign-supervisor', [HackathonAdminIdeaController::class, 'assignSupervisor'])->name('ideas.assign-supervisor');
    Route::post('ideas/{idea}/update-score', [HackathonAdminIdeaController::class, 'updateScore'])->name('ideas.update-score');
    Route::post('ideas/{idea}/score', [HackathonAdminIdeaController::class, 'updateScore'])->name('ideas.score');
    Route::get('ideas/{idea}/files/{file}/download', [HackathonAdminIdeaController::class, 'downloadFile'])->name('ideas.download-file');
    Route::get('ideas/statistics', [HackathonAdminIdeaController::class, 'statistics'])->name('ideas.statistics');
    Route::get('ideas/export', [HackathonAdminIdeaController::class, 'export'])->name('ideas.export');

    // Workshop Management (All Editions)
    Route::resource('workshops', HackathonAdminWorkshopController::class);
    Route::get('workshops/{workshop}/attendance', [HackathonAdminWorkshopController::class, 'attendance'])->name('workshops.attendance');
    Route::post('workshops/{workshop}/generate-qr', [HackathonAdminWorkshopController::class, 'generateQR'])->name('workshops.generate-qr');
    Route::get('workshops/export', [HackathonAdminWorkshopController::class, 'export'])->name('workshops.export');

    // Speaker Management
    Route::resource('speakers', HackathonAdminSpeakerController::class);
    Route::post('speakers/{speaker}/activate', [HackathonAdminSpeakerController::class, 'activate'])->name('speakers.activate');
    Route::post('speakers/{speaker}/deactivate', [HackathonAdminSpeakerController::class, 'deactivate'])->name('speakers.deactivate');

    // Organization Management
    Route::resource('organizations', HackathonAdminOrganizationController::class);
    Route::post('organizations/{organization}/activate', [HackathonAdminOrganizationController::class, 'activate'])->name('organizations.activate');
    Route::post('organizations/{organization}/deactivate', [HackathonAdminOrganizationController::class, 'deactivate'])->name('organizations.deactivate');

    // News Management (All Editions)
    Route::get('news/media-center', [HackathonAdminNewsController::class, 'mediaCenter'])->name('news.media-center');
    Route::post('news/upload-temp', [HackathonAdminNewsController::class, 'uploadTemp'])->name('news.upload-temp');
    Route::delete('news/delete-temp', [HackathonAdminNewsController::class, 'deleteTemp'])->name('news.delete-temp');
    Route::get('news/media/{mediaId}', [HackathonAdminNewsController::class, 'getMedia'])->name('news.get-media');
    Route::delete('news/media/{mediaId}', [HackathonAdminNewsController::class, 'deleteMedia'])->name('news.media.delete');
    Route::resource('news', HackathonAdminNewsController::class);
    Route::post('news/{news}/publish', [HackathonAdminNewsController::class, 'publish'])->name('news.publish');
    Route::post('news/{news}/unpublish', [HackathonAdminNewsController::class, 'unpublish'])->name('news.unpublish');

    // System Settings
    Route::prefix('settings')->name('settings.')->group(function () {
        Route::get('/', [HackathonAdminSettingsController::class, 'index'])->name('index');
        Route::get('/smtp', [HackathonAdminSettingsController::class, 'smtp'])->name('smtp');
        Route::post('/smtp', [HackathonAdminSettingsController::class, 'updateSmtp'])->name('smtp.update');
        Route::get('/branding', [HackathonAdminSettingsController::class, 'branding'])->name('branding');
        Route::post('/branding', [HackathonAdminSettingsController::class, 'updateBranding'])->name('branding.update');
        Route::get('/twitter', [HackathonAdminSettingsController::class, 'twitter'])->name('twitter');
        Route::post('/twitter', [HackathonAdminSettingsController::class, 'updateTwitter'])->name('twitter.update');
        Route::get('/sms', [HackathonAdminSettingsController::class, 'sms'])->name('sms');
        Route::post('/sms', [HackathonAdminSettingsController::class, 'updateSms'])->name('sms.update');
        Route::post('/notifications', [HackathonAdminSettingsController::class, 'updateNotifications'])->name('notifications.update');
    });

    // Reports & Analytics
    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('/', [HackathonAdminReportController::class, 'index'])->name('index');
        Route::get('/users', [HackathonAdminReportController::class, 'users'])->name('users');
        Route::get('/teams', [HackathonAdminReportController::class, 'teams'])->name('teams');
        Route::get('/ideas', [HackathonAdminReportController::class, 'ideas'])->name('ideas');
        Route::get('/workshops', [HackathonAdminReportController::class, 'workshops'])->name('workshops');
        Route::get('/system-health', [HackathonAdminReportController::class, 'systemHealth'])->name('system-health');
        Route::post('/generate', [HackathonAdminReportController::class, 'generateReport'])->name('generate');
        Route::post('/export-pdf', [HackathonAdminReportController::class, 'exportPdf'])->name('export-pdf');
        Route::post('/schedule', [HackathonAdminReportController::class, 'scheduleReports'])->name('schedule');
    });

    // Check-ins Management
    Route::prefix('checkins')->name('checkins.')->group(function () {
        Route::get('/', [HackathonAdminCheckinController::class, 'index'])->name('index');
        Route::post('/process-qr', [HackathonAdminCheckinController::class, 'processQR'])->name('process-qr');
        Route::post('/mark-attendance', [HackathonAdminCheckinController::class, 'markAttendance'])->name('mark-attendance');
        Route::get('/search', [HackathonAdminCheckinController::class, 'search'])->name('search');
        Route::get('/export', [HackathonAdminCheckinController::class, 'export'])->name('export');
        Route::get('/workshop/{workshop}', [HackathonAdminCheckinController::class, 'workshopAttendance'])->name('workshop-attendance');
        Route::get('/generate-qr/{registration}', [HackathonAdminCheckinController::class, 'generateQR'])->name('generate-qr');
    });
});

/*
|--------------------------------------------------------------------------
| TRACK SUPERVISOR ROUTES
|--------------------------------------------------------------------------
| Routes for track supervisors managing assigned tracks
*/

Route::middleware(['auth', 'role:track_supervisor|permission:view-assigned-tracks'])->prefix('track-supervisor')->name('track-supervisor.')->group(function () {
    // Dashboard
    Route::get('/dashboard', [TrackSupervisorDashboardController::class, 'index'])->name('dashboard');

    // Hackathon Edition Management
    Route::resource('editions', TrackSupervisorEditionController::class);
    Route::post('editions/{edition}/set-current', [TrackSupervisorEditionController::class, 'setCurrent'])->name('editions.set-current');
    Route::post('editions/{edition}/archive', [TrackSupervisorEditionController::class, 'archive'])->name('editions.archive');
    Route::get('editions/{edition}/export', [TrackSupervisorEditionController::class, 'export'])->name('editions.export');

    // User Management (All Users Across All Editions)
    Route::resource('users', TrackSupervisorUserController::class);
    Route::post('users/{user}/activate', [TrackSupervisorUserController::class, 'activate'])->name('users.activate');
    Route::post('users/{user}/deactivate', [TrackSupervisorUserController::class, 'deactivate'])->name('users.deactivate');
    Route::post('users/bulk-action', [TrackSupervisorUserController::class, 'bulkAction'])->name('users.bulk-action');
    Route::get('users/export', [TrackSupervisorUserController::class, 'export'])->name('users.export');

    // Global Team Management
    Route::resource('teams', TrackSupervisorTeamController::class);
    Route::post('teams/{team}/add-member', [TrackSupervisorTeamController::class, 'addMember'])->name('teams.add-member');
    Route::delete('teams/{team}/remove-member/{user}', [TrackSupervisorTeamController::class, 'removeMember'])->name('teams.remove-member');
    Route::get('teams/export', [TrackSupervisorTeamController::class, 'export'])->name('teams.export');
    Route::get('users/search', [TrackSupervisorUserController::class, 'search'])->name('users.search');

    // Global Track Management
    Route::resource('tracks', TrackSupervisorTrackController::class);
    Route::get('tracks/export', [TrackSupervisorTrackController::class, 'export'])->name('tracks.export');

    // Global Idea Management
    Route::resource('ideas', TrackSupervisorIdeaController::class);
    Route::get('ideas/{idea}/review', [TrackSupervisorIdeaController::class, 'review'])->name('ideas.review');
    Route::post('ideas/{idea}/process-review', [TrackSupervisorIdeaController::class, 'processReview'])->name('ideas.process-review');
    Route::post('ideas/{idea}/review/accept', [TrackSupervisorIdeaController::class, 'accept'])->name('ideas.review.accept');
    Route::post('ideas/{idea}/review/reject', [TrackSupervisorIdeaController::class, 'reject'])->name('ideas.review.reject');
    Route::post('ideas/{idea}/review/need-edit', [TrackSupervisorIdeaController::class, 'needEdit'])->name('ideas.review.need-edit');
    Route::post('ideas/{idea}/assign-supervisor', [TrackSupervisorIdeaController::class, 'assignSupervisor'])->name('ideas.assign-supervisor');
    Route::post('ideas/{idea}/update-score', [TrackSupervisorIdeaController::class, 'updateScore'])->name('ideas.update-score');
    Route::post('ideas/{idea}/score', [TrackSupervisorIdeaController::class, 'updateScore'])->name('ideas.score');
    Route::get('ideas/{idea}/files/{file}/download', [TrackSupervisorIdeaController::class, 'downloadFile'])->name('ideas.download-file');
    Route::get('ideas/statistics', [TrackSupervisorIdeaController::class, 'statistics'])->name('ideas.statistics');
    Route::get('ideas/export', [TrackSupervisorIdeaController::class, 'export'])->name('ideas.export');

    // Workshop Management (All Editions)
    Route::resource('workshops', TrackSupervisorWorkshopController::class);
    Route::get('workshops/{workshop}/attendance', [TrackSupervisorWorkshopController::class, 'attendance'])->name('workshops.attendance');
    Route::post('workshops/{workshop}/generate-qr', [TrackSupervisorWorkshopController::class, 'generateQR'])->name('workshops.generate-qr');
    Route::get('workshops/export', [TrackSupervisorWorkshopController::class, 'export'])->name('workshops.export');

    // Speaker Management
    Route::resource('speakers', TrackSupervisorSpeakerController::class);
    Route::post('speakers/{speaker}/activate', [TrackSupervisorSpeakerController::class, 'activate'])->name('speakers.activate');
    Route::post('speakers/{speaker}/deactivate', [TrackSupervisorSpeakerController::class, 'deactivate'])->name('speakers.deactivate');

    // Organization Management
    Route::resource('organizations', TrackSupervisorOrganizationController::class);
    Route::post('organizations/{organization}/activate', [TrackSupervisorOrganizationController::class, 'activate'])->name('organizations.activate');
    Route::post('organizations/{organization}/deactivate', [TrackSupervisorOrganizationController::class, 'deactivate'])->name('organizations.deactivate');

    // News Management (All Editions)
    Route::get('news/media-center', [TrackSupervisorNewsController::class, 'mediaCenter'])->name('news.media-center');
    Route::post('news/upload-temp', [TrackSupervisorNewsController::class, 'uploadTemp'])->name('news.upload-temp');
    Route::delete('news/delete-temp', [TrackSupervisorNewsController::class, 'deleteTemp'])->name('news.delete-temp');
    Route::get('news/media/{mediaId}', [TrackSupervisorNewsController::class, 'getMedia'])->name('news.get-media');
    Route::delete('news/media/{mediaId}', [TrackSupervisorNewsController::class, 'deleteMedia'])->name('news.media.delete');
    Route::resource('news', TrackSupervisorNewsController::class);
    Route::post('news/{news}/publish', [TrackSupervisorNewsController::class, 'publish'])->name('news.publish');
    Route::post('news/{news}/unpublish', [TrackSupervisorNewsController::class, 'unpublish'])->name('news.unpublish');

    // System Settings
    Route::prefix('settings')->name('settings.')->group(function () {
        Route::get('/', [TrackSupervisorSettingsController::class, 'index'])->name('index');
        Route::get('/smtp', [TrackSupervisorSettingsController::class, 'smtp'])->name('smtp');
        Route::post('/smtp', [TrackSupervisorSettingsController::class, 'updateSmtp'])->name('smtp.update');
        Route::get('/branding', [TrackSupervisorSettingsController::class, 'branding'])->name('branding');
        Route::post('/branding', [TrackSupervisorSettingsController::class, 'updateBranding'])->name('branding.update');
        Route::get('/twitter', [TrackSupervisorSettingsController::class, 'twitter'])->name('twitter');
        Route::post('/twitter', [TrackSupervisorSettingsController::class, 'updateTwitter'])->name('twitter.update');
        Route::get('/sms', [TrackSupervisorSettingsController::class, 'sms'])->name('sms');
        Route::post('/sms', [TrackSupervisorSettingsController::class, 'updateSms'])->name('sms.update');
        Route::post('/notifications', [TrackSupervisorSettingsController::class, 'updateNotifications'])->name('notifications.update');
    });

    // Reports & Analytics
    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('/', [TrackSupervisorReportController::class, 'index'])->name('index');
        Route::get('/users', [TrackSupervisorReportController::class, 'users'])->name('users');
        Route::get('/teams', [TrackSupervisorReportController::class, 'teams'])->name('teams');
        Route::get('/ideas', [TrackSupervisorReportController::class, 'ideas'])->name('ideas');
        Route::get('/workshops', [TrackSupervisorReportController::class, 'workshops'])->name('workshops');
        Route::get('/system-health', [TrackSupervisorReportController::class, 'systemHealth'])->name('system-health');
        Route::post('/generate', [TrackSupervisorReportController::class, 'generateReport'])->name('generate');
        Route::post('/export-pdf', [TrackSupervisorReportController::class, 'exportPdf'])->name('export-pdf');
        Route::post('/schedule', [TrackSupervisorReportController::class, 'scheduleReports'])->name('schedule');
    });

    // Check-ins Management
    Route::prefix('checkins')->name('checkins.')->group(function () {
        Route::get('/', [TrackSupervisorCheckinController::class, 'index'])->name('index');
        Route::post('/process-qr', [TrackSupervisorCheckinController::class, 'processQR'])->name('process-qr');
        Route::post('/mark-attendance', [TrackSupervisorCheckinController::class, 'markAttendance'])->name('mark-attendance');
        Route::get('/search', [TrackSupervisorCheckinController::class, 'search'])->name('search');
        Route::get('/export', [TrackSupervisorCheckinController::class, 'export'])->name('export');
        Route::get('/workshop/{workshop}', [TrackSupervisorCheckinController::class, 'workshopAttendance'])->name('workshop-attendance');
        Route::get('/generate-qr/{registration}', [TrackSupervisorCheckinController::class, 'generateQR'])->name('generate-qr');
    });
});

/*
|--------------------------------------------------------------------------
| TEAM LEADER ROUTES
|--------------------------------------------------------------------------
| Routes for team leaders managing their teams and ideas
*/

Route::middleware(['auth', 'role:team_leader|permission:create-manage-team'])->prefix('team-leader')->name('team-leader.')->group(function () {
    // Dashboard
    Route::get('/dashboard', [TeamLeaderDashboardController::class, 'index'])->name('dashboard');

    // Team Management (Own Team Only)
    Route::get('/team', [TeamLeaderTeamController::class, 'show'])->name('team.show');
    Route::get('/team/edit', [TeamLeaderTeamController::class, 'edit'])->name('team.edit');
    Route::put('/team', [TeamLeaderTeamController::class, 'update'])->name('team.update');
    Route::post('/team/invite-member', [TeamLeaderTeamController::class, 'inviteMember'])->name('team.invite-member');
    Route::delete('/team/members/{member}', [TeamLeaderTeamController::class, 'removeMember'])->name('team.remove-member');
    Route::delete('/team', [TeamLeaderTeamController::class, 'disbandTeam'])->name('team.disband');

    // Idea Management (Own Team's Idea Only)
    Route::get('/idea', [TeamLeaderIdeaController::class, 'show'])->name('idea.show');
    Route::get('/idea/create', [TeamLeaderIdeaController::class, 'create'])->name('idea.create');
    Route::post('/idea', [TeamLeaderIdeaController::class, 'store'])->name('idea.store');
    Route::get('/idea/edit', [TeamLeaderIdeaController::class, 'edit'])->name('idea.edit');
    Route::put('/idea', [TeamLeaderIdeaController::class, 'update'])->name('idea.update');
    Route::post('/idea/submit', [TeamLeaderIdeaController::class, 'submit'])->name('idea.submit');
    Route::post('/idea/withdraw', [TeamLeaderIdeaController::class, 'withdraw'])->name('idea.withdraw');
    Route::post('/idea/upload-file', [TeamLeaderIdeaController::class, 'uploadFile'])->name('idea.upload-file');
    Route::delete('/idea/files/{file}', [TeamLeaderIdeaController::class, 'deleteFile'])->name('idea.delete-file');
});

/*
|--------------------------------------------------------------------------
| TEAM MEMBER ROUTES
|--------------------------------------------------------------------------
| Routes for team members with read-only access
*/

// Team member routes are now defined in routes/team-member.php to avoid conflicts
// Commented out to prevent duplicate route names
// Route::middleware(['auth', 'role:team_member|permission:view-team-info'])->prefix('team-member')->name('team-member.')->group(function () {
//     // Dashboard
//     Route::get('/dashboard', [TeamMemberDashboardController::class, 'index'])->name('dashboard');
//
//     // Team Information (Read-Only)
//     Route::get('/team', [TeamMemberTeamController::class, 'show'])->name('team.show');
//     Route::post('/team/leave', [TeamMemberTeamController::class, 'leaveTeam'])->name('team.leave');
//     Route::post('/team/contact-leader', [TeamMemberTeamController::class, 'contactLeader'])->name('team.contact-leader');
//
//     // Workshop Registration
//     Route::get('/workshops', [TeamMemberWorkshopController::class, 'index'])->name('workshops.index');
//     Route::get('/workshops/{workshop}', [TeamMemberWorkshopController::class, 'show'])->name('workshops.show');
//     Route::post('/workshops/{workshop}/register', [TeamMemberWorkshopController::class, 'register'])->name('workshops.register');
//     Route::delete('/workshops/{workshop}/unregister', [TeamMemberWorkshopController::class, 'unregister'])->name('workshops.unregister');
//     Route::get('/workshops/{workshop}/certificate', [TeamMemberWorkshopController::class, 'downloadCertificate'])->name('workshops.certificate');
// });

/*
|--------------------------------------------------------------------------
| PUBLIC ROUTES (Available to all authenticated users)
|--------------------------------------------------------------------------
*/

Route::middleware(['auth'])->group(function () {
    // Public Workshop Routes
    Route::prefix('workshops')->name('workshops.')->group(function () {
        Route::get('/', [HackathonAdminWorkshopController::class, 'publicIndex'])->name('public.index');
        Route::get('/{workshop}', [HackathonAdminWorkshopController::class, 'publicShow'])->name('public.show');
        Route::post('/{workshop}/register', [HackathonAdminWorkshopController::class, 'publicRegister'])->name('public.register');
        Route::delete('/{workshop}/unregister', [HackathonAdminWorkshopController::class, 'publicUnregister'])->name('public.unregister');
    });

    // Public News Routes
    Route::prefix('news')->name('news.')->group(function () {
        Route::get('/', [HackathonAdminNewsController::class, 'publicIndex'])->name('public.index');
        Route::get('/{news}', [HackathonAdminNewsController::class, 'publicShow'])->name('public.show');
    });
});

/*
|--------------------------------------------------------------------------
| QR CODE SCANNER ROUTES (Mobile Optimized)
|--------------------------------------------------------------------------
*/

Route::middleware(['auth'])->prefix('qr')->name('qr.')->group(function () {
    Route::get('/scanner', [\App\Http\Controllers\QRScannerController::class, 'index'])->name('scanner');
    Route::post('/scan-workshop', [\App\Http\Controllers\QRScannerController::class, 'scanWorkshop'])->name('scan-workshop');
    Route::post('/mark-attendance', [\App\Http\Controllers\QRScannerController::class, 'markAttendance'])->name('mark-attendance');
});
