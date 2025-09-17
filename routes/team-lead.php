<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TeamLead\DashboardController;
use App\Http\Controllers\TeamLead\TeamController;
use App\Http\Controllers\TeamLead\IdeaController;
use App\Http\Controllers\TeamLead\TrackController;
use App\Http\Controllers\TeamLead\WorkshopController;
use App\Http\Controllers\TeamLead\ProfileController;

Route::middleware(['auth', 'verified', 'team.lead'])->prefix('team-lead')->name('team-lead.')->group(function () {

    // Idea creation routes (without ensure.idea middleware)
    Route::prefix('idea')->name('idea.')->group(function () {
        Route::get('/submit', [IdeaController::class, 'create'])->name('create');
        Route::post('/', [IdeaController::class, 'store'])->name('store');
    });

    // All other routes with ensure.idea middleware
    Route::middleware(['ensure.idea'])->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

        // Team Management
        Route::prefix('team')->name('team.')->group(function () {
            Route::get('/', [TeamController::class, 'index'])->name('index');
            Route::get('/show', [TeamController::class, 'show'])->name('show');
            Route::get('/create', [TeamController::class, 'create'])->name('create');
            Route::post('/', [TeamController::class, 'store'])->name('store');
            Route::get('/{id}/edit', [TeamController::class, 'edit'])->name('edit');
            Route::put('/{id}', [TeamController::class, 'update'])->name('update');
            Route::get('/add-member', [TeamController::class, 'showAddMember'])->name('add-member');
            Route::post('/add-member', [TeamController::class, 'addMember'])->name('add-member.store');
            Route::delete('/remove-member/{id}', [TeamController::class, 'removeMember'])->name('remove-member');
        });

        // Idea Management (except create/store)
        Route::prefix('idea')->name('idea.')->group(function () {
            Route::get('/', [IdeaController::class, 'index'])->name('index');
            Route::get('/show', [IdeaController::class, 'show'])->name('show');
            Route::get('/edit', [IdeaController::class, 'edit'])->name('edit-my');
            Route::put('/update', [IdeaController::class, 'update'])->name('update');
            Route::post('/{id}/comment', [IdeaController::class, 'addComment'])->name('comment');
            Route::delete('/file/{id}', [IdeaController::class, 'deleteFile'])->name('file.delete');
        });

        // Tracks
        Route::prefix('tracks')->name('tracks.')->group(function () {
            Route::get('/', [TrackController::class, 'index'])->name('index');
            Route::get('/{id}', [TrackController::class, 'show'])->name('show');
        });

        // Workshops
        Route::prefix('workshops')->name('workshops.')->group(function () {
            Route::get('/', [WorkshopController::class, 'index'])->name('index');
            Route::post('/{id}/register', [WorkshopController::class, 'register'])->name('register');
            Route::delete('/{id}/unregister', [WorkshopController::class, 'unregister'])->name('unregister');
        });

        // Profile
        Route::prefix('profile')->name('profile.')->group(function () {
            Route::get('/', [ProfileController::class, 'index'])->name('index');
            Route::put('/', [ProfileController::class, 'update'])->name('update');
        });
    });
});
