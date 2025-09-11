<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Visitor\WorkshopController;
use App\Http\Controllers\Visitor\ProfileController;

Route::middleware(['auth', 'verified', 'visitor'])->prefix('visitor')->name('visitor.')->group(function () {
    // Dashboard
    Route::get('/dashboard', function () {
        return \Inertia\Inertia::render('Visitor/Dashboard');
    })->name('dashboard');
    
    // All Workshops
    Route::prefix('workshops')->name('workshops.')->group(function () {
        Route::get('/', [WorkshopController::class, 'index'])->name('index');
        Route::get('/my-workshops', [WorkshopController::class, 'myWorkshops'])->name('my');
        Route::post('/{id}/register', [WorkshopController::class, 'register'])->name('register');
        Route::delete('/{id}/unregister', [WorkshopController::class, 'unregister'])->name('unregister');
    });
    
    // Profile
    Route::prefix('profile')->name('profile.')->group(function () {
        Route::get('/', [ProfileController::class, 'index'])->name('index');
        Route::put('/', [ProfileController::class, 'update'])->name('update');
    });
});
