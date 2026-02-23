<?php

use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Route;

// Home Page
Route::get('/', HomeController::class)->name('home');

require __DIR__ . '/team.php';
require __DIR__ . '/fixture.php';
require __DIR__ . '/simulation.php';
