<?php

// Add these routes to routes/hackathon-admin.php

use App\Http\Controllers\HackathonAdmin\EditionSwitchController;

Route::middleware(['auth', 'hackathon-admin-edition'])->prefix('hackathon-admin')->name('hackathon-admin.')->group(function () {
    
    // Edition switching
    Route::post('/switch-edition', [EditionSwitchController::class, 'switch'])->name('switch-edition');
    Route::get('/current-edition', [EditionSwitchController::class, 'current'])->name('current-edition');
    
    // ... existing routes ...
});
