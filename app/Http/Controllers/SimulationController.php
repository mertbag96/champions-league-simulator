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
        $teams = Team::query()
            ->orderBy('power', 'desc')
            ->active()
            ->get();

        $readyForSimulation = $teams->count() > 3;

        $fixturesGenerated = Fixture::exists();

        return Inertia::render('Home', [
            'teams' => $teams,
            'readyForSimulation' => $readyForSimulation,
            'fixturesGenerated' => $fixturesGenerated,
        ]);
    }
}
