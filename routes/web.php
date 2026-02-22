<?php

use App\Http\Controllers\TeamController;
use App\Http\Controllers\SimulationController;
use Illuminate\Support\Facades\Route;

// Home Page
Route::get('/', [SimulationController::class, 'index'])->name('home');

// Teams Resource
Route::resource('teams', TeamController::class)->except('show');
