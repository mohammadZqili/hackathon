<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TeamMember\DashboardController;
use App\Http\Controllers\TeamMember\TeamController;
use App\Http\Controllers\TeamMember\IdeaController;
use App\Http\Controllers\TeamMember\TrackController;
use App\Http\Controllers\TeamMember\WorkshopController;
use App\Http\Controllers\TeamMember\ProfileController;

Route::middleware(['auth', 'verified', 'team.member'])->prefix('team-member')->name('team-member.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Team View
    Route::prefix('team')->name('team.')->group(function () {
        Route::get('/', [TeamController::class, 'index'])->name('index');
        Route::get('/show', [TeamController::class, 'show'])->name('show');
    });
    
    // Idea View & Comments
    Route::prefix('idea')->name('idea.')->group(function () {
        Route::get('/', [IdeaController::class, 'index'])->name('index');
        Route::post('/{id}/comment', [IdeaController::class, 'addComment'])->name('comment');
        Route::get('/{idea}/files/{file}/download', [IdeaController::class, 'downloadFile'])->name('download-file');
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
