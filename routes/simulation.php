<?php

use App\Http\Controllers\SimulationController;
use Illuminate\Support\Facades\Route;

// Simulation Routes
Route::prefix('simulation')
    ->name('simulation.')
    ->group(function (): void {
        // Index
        Route::get('/{week}', [SimulationController::class, 'index'])
            ->where('week', '[1-9][0-9]*')
            ->name('index');

        // Simulate Week
        Route::post('simulate/{week}', [SimulationController::class, 'simulate'])->name('simulate');

        // Reset
        Route::delete('reset', [SimulationController::class, 'reset'])->name('reset');
    });
