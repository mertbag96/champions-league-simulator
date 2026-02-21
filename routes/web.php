<?php

use App\Http\Controllers\SimulationController;
use Illuminate\Support\Facades\Route;

// Index (Simulation)
Route::get('/', [SimulationController::class, 'index'])->name('home');
