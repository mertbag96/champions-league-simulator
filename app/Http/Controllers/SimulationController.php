<?php

namespace App\Http\Controllers;

use App\Models\Fixture;
use App\Models\Team;
use App\Services\SimulationService;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class SimulationController extends Controller
{
    /**
     * The constructor of the controller.
     */
    public function __construct(protected SimulationService $simulationService) {}

    /**
     * Show the first week of the simulation.
     */
    public function index(int $week): RedirectResponse|Response
    {
        $teams = Team::query()
            ->withFixtures()
            ->active()
            ->get()
            ->map(function ($team) use ($week): array {
                $stats = $team->statsUntilWeek($week);

                return [
                    ...$team->toArray(),
                    ...$stats,
                    'next_opponent' => $team->nextOpponentForWeek($week),
                ];
            })
            ->sort(function ($a, $b) {
                return
                    $b['points'] <=> $a['points']
                    ?: $b['goal_difference'] <=> $a['goal_difference']
                    ?: $b['goals_for'] <=> $a['goals_for']
                    ?: $a['goals_against'] <=> $b['goals_against'];
            })
            ->values();

        $matches = Fixture::query()
            ->with(['homeTeam', 'awayTeam'])
            ->byWeek($week)
            ->get()
            ->map(fn($fixture): array => [
                'id' => $fixture->id,
                'week' => $fixture->week,
                'played' => $fixture->played,
                'home_score' => $fixture->home_score,
                'away_score' => $fixture->away_score,
                'home_team' => $fixture->homeTeam,
                'away_team' => $fixture->awayTeam,
            ]);

        $weeks = $weeks = Fixture::query()
            ->distinct()
            ->orderBy('week')
            ->pluck('week')
            ->values();

        if ($matches->isEmpty()) {
            return redirect()->route('fixtures.index');
        }

        return Inertia::render('Simulation/Index', [
            'week' => $week,
            'weeks' => $weeks,
            'teams' => $teams,
            'matches' => $matches,
        ]);
    }

    /**
     * Simulate the given week.
     */
    public function simulate(int $week): RedirectResponse
    {
        $canSimulateWeek = $this->simulationService->canSimulateWeek($week);

        if (! $canSimulateWeek) {
            return redirect()
                ->route('simulation.index', $week)
                ->with('error', 'Please complete the previous weeks before simulating this week');
        }

        $played = $this->simulationService->simulateWeek($week);

        if (! $played) {
            return redirect()
                ->route('simulation.index', $week)
                ->with('error', 'No unplayed matches found to simulate this week!');
        }

        $nextWeek = Fixture::query()
            ->where('week', '>', $week)
            ->distinct()
            ->orderBy('week')
            ->value('week');

        if (! $nextWeek) {
            return redirect()
                ->route('simulation.index', $week)
                ->with('success', 'Simulation is successfully completed!');
        }

        return redirect()
            ->route('simulation.index', $week)
            ->with('success', "Week {$week} was successfully simulated!");
    }

    /**
     * Reset simulation.
     */
    public function reset(): RedirectResponse
    {
        $teams = Team::query()
            ->withFixtures()
            ->active()
            ->get();

        foreach ($teams as $team) {
            $team->update([
                'played'        => 0,
                'points'        => 0,
                'goals_for'     => 0,
                'goals_against' => 0,
            ]);
        }

        $fixtures = Fixture::all();

        foreach ($fixtures as $fixture) {
            $fixture->update([
                'home_score'    => 0,
                'away_score'    => 0,
                'played'        => false,
            ]);
        }

        return redirect()
            ->route('simulation.index', 1)
            ->with('success', 'Simulation was successfully reset!');
    }
}
