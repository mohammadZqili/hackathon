<?php

use Illuminate\Support\Facades\Route;

// Hackathon Controllers
use App\Http\Controllers\SystemAdmin\DashboardController as SystemAdminDashboardController;
use App\Http\Controllers\SystemAdmin\HackathonEditionController as SystemAdminEditionController;
use App\Http\Controllers\SystemAdmin\UserController as SystemAdminUserController;
use App\Http\Controllers\SystemAdmin\TeamController as SystemAdminTeamController;
use App\Http\Controllers\SystemAdmin\IdeaController as SystemAdminIdeaController;
use App\Http\Controllers\SystemAdmin\WorkshopController as SystemAdminWorkshopController;
use App\Http\Controllers\SystemAdmin\SpeakerController as SystemAdminSpeakerController;
use App\Http\Controllers\SystemAdmin\OrganizationController as SystemAdminOrganizationController;
use App\Http\Controllers\SystemAdmin\NewsController as SystemAdminNewsController;
use App\Http\Controllers\SystemAdmin\SettingsController as SystemAdminSettingsController;
use App\Http\Controllers\SystemAdmin\ReportController as SystemAdminReportController;

use App\Http\Controllers\HackathonAdmin\DashboardController as HackathonAdminDashboardController;
use App\Http\Controllers\HackathonAdmin\TeamController as HackathonAdminTeamController;
use App\Http\Controllers\HackathonAdmin\IdeaController as HackathonAdminIdeaController;
use App\Http\Controllers\HackathonAdmin\WorkshopController as HackathonAdminWorkshopController;
use App\Http\Controllers\HackathonAdmin\NewsController as HackathonAdminNewsController;

use App\Http\Controllers\TrackSupervisor\DashboardController as TrackSupervisorDashboardController;
use App\Http\Controllers\TrackSupervisor\IdeaController as TrackSupervisorIdeaController;
use App\Http\Controllers\TrackSupervisor\WorkshopController as TrackSupervisorWorkshopController;

use App\Http\Controllers\TeamLeader\DashboardController as TeamLeaderDashboardController;
use App\Http\Controllers\TeamLeader\TeamController as TeamLeaderTeamController;
use App\Http\Controllers\TeamLeader\IdeaController as TeamLeaderIdeaController;

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
    Route::resource('teams', SystemAdminTeamController::class)->only(['index', 'show', 'destroy']);
    Route::get('teams/export', [SystemAdminTeamController::class, 'export'])->name('teams.export');

    // Global Idea Management
    Route::resource('ideas', SystemAdminIdeaController::class)->only(['index', 'show', 'destroy']);
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
    });

    // Reports & Analytics
    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('/', [SystemAdminReportController::class, 'index'])->name('index');
        Route::get('/users', [SystemAdminReportController::class, 'users'])->name('users');
        Route::get('/teams', [SystemAdminReportController::class, 'teams'])->name('teams');
        Route::get('/ideas', [SystemAdminReportController::class, 'ideas'])->name('ideas');
        Route::get('/workshops', [SystemAdminReportController::class, 'workshops'])->name('workshops');
        Route::get('/system-health', [SystemAdminReportController::class, 'systemHealth'])->name('system-health');
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

    // Team Management (Current Edition Only)
    Route::resource('teams', HackathonAdminTeamController::class);
    Route::post('teams/{team}/approve', [HackathonAdminTeamController::class, 'approve'])->name('teams.approve');
    Route::post('teams/{team}/reject', [HackathonAdminTeamController::class, 'reject'])->name('teams.reject');
    Route::post('teams/bulk-approve', [HackathonAdminTeamController::class, 'bulkApprove'])->name('teams.bulk-approve');
    Route::post('teams/bulk-reject', [HackathonAdminTeamController::class, 'bulkReject'])->name('teams.bulk-reject');
    Route::get('teams/export', [HackathonAdminTeamController::class, 'export'])->name('teams.export');

    // Idea Management (Current Edition Only)
    Route::resource('ideas', HackathonAdminIdeaController::class);
    Route::get('ideas/{idea}/review', [HackathonAdminIdeaController::class, 'review'])->name('ideas.review');
    Route::post('ideas/{idea}/process-review', [HackathonAdminIdeaController::class, 'processReview'])->name('ideas.process-review');
    Route::post('ideas/{idea}/assign-supervisor', [HackathonAdminIdeaController::class, 'assignSupervisor'])->name('ideas.assign-supervisor');
    Route::get('ideas/export', [HackathonAdminIdeaController::class, 'export'])->name('ideas.export');

    // Workshop Management (Current Edition Only)
    Route::resource('workshops', HackathonAdminWorkshopController::class);
    Route::get('workshops/{workshop}/attendance', [HackathonAdminWorkshopController::class, 'attendance'])->name('workshops.attendance');
    Route::post('workshops/{workshop}/mark-attendance', [HackathonAdminWorkshopController::class, 'markAttendance'])->name('workshops.mark-attendance');
    Route::post('workshops/{workshop}/generate-qr', [HackathonAdminWorkshopController::class, 'generateQR'])->name('workshops.generate-qr');
    Route::get('workshops/{workshop}/export-attendance', [HackathonAdminWorkshopController::class, 'exportAttendance'])->name('workshops.export-attendance');

    // News Management (Current Edition Only)
    Route::resource('news', HackathonAdminNewsController::class);
    Route::post('news/{news}/publish', [HackathonAdminNewsController::class, 'publish'])->name('news.publish');
    Route::post('news/{news}/unpublish', [HackathonAdminNewsController::class, 'unpublish'])->name('news.unpublish');
    Route::post('news/{news}/tweet', [HackathonAdminNewsController::class, 'tweet'])->name('news.tweet');
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

    // Ideas Review (Assigned Track Only)
    Route::get('/ideas', [TrackSupervisorIdeaController::class, 'index'])->name('ideas.index');
    Route::get('/ideas/{idea}', [TrackSupervisorIdeaController::class, 'show'])->name('ideas.show');
    Route::get('/ideas/{idea}/review', [TrackSupervisorIdeaController::class, 'review'])->name('ideas.review');
    Route::post('/ideas/{idea}/review', [TrackSupervisorIdeaController::class, 'processReview'])->name('ideas.review.process');
    Route::post('/ideas/{idea}/request-revision', [TrackSupervisorIdeaController::class, 'requestRevision'])->name('ideas.request-revision');
    Route::post('/ideas/{idea}/add-comment', [TrackSupervisorIdeaController::class, 'addComment'])->name('ideas.add-comment');

    // Workshop Management (Track Related)
    Route::get('/workshops', [TrackSupervisorWorkshopController::class, 'index'])->name('workshops.index');
    Route::get('/workshops/{workshop}', [TrackSupervisorWorkshopController::class, 'show'])->name('workshops.show');
    Route::post('/workshops/{workshop}/mark-attendance', [TrackSupervisorWorkshopController::class, 'markAttendance'])->name('workshops.mark-attendance');
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

Route::middleware(['auth', 'role:team_member|permission:view-team-info'])->prefix('team-member')->name('team-member.')->group(function () {
    // Dashboard
    Route::get('/dashboard', [TeamMemberDashboardController::class, 'index'])->name('dashboard');

    // Team Information (Read-Only)
    Route::get('/team', [TeamMemberTeamController::class, 'show'])->name('team.show');
    Route::post('/team/leave', [TeamMemberTeamController::class, 'leaveTeam'])->name('team.leave');
    Route::post('/team/contact-leader', [TeamMemberTeamController::class, 'contactLeader'])->name('team.contact-leader');

    // Workshop Registration
    Route::get('/workshops', [TeamMemberWorkshopController::class, 'index'])->name('workshops.index');
    Route::get('/workshops/{workshop}', [TeamMemberWorkshopController::class, 'show'])->name('workshops.show');
    Route::post('/workshops/{workshop}/register', [TeamMemberWorkshopController::class, 'register'])->name('workshops.register');
    Route::delete('/workshops/{workshop}/unregister', [TeamMemberWorkshopController::class, 'unregister'])->name('workshops.unregister');
    Route::get('/workshops/{workshop}/certificate', [TeamMemberWorkshopController::class, 'downloadCertificate'])->name('workshops.certificate');
});

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
