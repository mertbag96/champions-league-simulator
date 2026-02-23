<?php

namespace App\Http\Controllers;

use App\Models\Team;
use App\Models\Fixture;
use Inertia\Inertia;
use Inertia\Response;

class HomeController extends Controller
{
    /**
     * Show home page.
     */
    public function __invoke(): Response
    {
        $fixturesGenerated = Fixture::exists();

        $query = Team::query()
            ->orderBy('power', 'desc')
            ->active();

        if ($fixturesGenerated) {
            $query = $query->withFixtures();
        }

        $teams = $query->get();

        $readyForSimulation = $teams->count() > 3;

        return Inertia::render('Home', [
            'teams' => $teams,
            'readyForSimulation' => $readyForSimulation,
            'fixturesGenerated' => $fixturesGenerated,
        ]);
    }
}
