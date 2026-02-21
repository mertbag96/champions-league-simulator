<?php

namespace App\Http\Controllers;

use App\Models\Team;
use App\Models\Fixture;
use Inertia\Inertia;
use Inertia\Response;

class SimulationController extends Controller
{
    /**
     * Show index page for simulation
     */
    public function index(): Response
    {
        return Inertia::render('Home', [
            'teams' => Team::all(),
            'fixturesGenerated' => Fixture::exists(),
        ]);
    }
}
