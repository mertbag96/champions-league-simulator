<?php

use App\Http\Controllers\TeamController;
use Illuminate\Support\Facades\Route;

// Teams Resource
Route::resource('teams', TeamController::class)->except('show', 'update', 'destroy');

Route::name('teams.')
    ->prefix('teams')
    ->middleware('no.fixtures')
    ->group(function (): void {
        // Update
        Route::put('{team}', [TeamController::class, 'update'])->name('update');

        // Destroy
        Route::delete('{team}', [TeamController::class, 'destroy'])->name('destroy');
    });
