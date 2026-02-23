<?php

use App\Http\Controllers\FixtureController;
use Illuminate\Support\Facades\Route;

// Fixtures Resource
Route::resource('fixtures', FixtureController::class)->only('index', 'store');

// Reset Fixtures
Route::delete('/fixtures/reset', [FixtureController::class, 'reset'])->name('fixtures.reset');
