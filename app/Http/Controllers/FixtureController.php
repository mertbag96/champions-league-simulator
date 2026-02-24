<?php

namespace App\Http\Controllers;

use App\Models\Fixture;
use App\Models\Team;
use App\Services\FixtureService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class FixtureController extends Controller
{
    /**
     * The constructor of the controller.
     */
    public function __construct(protected FixtureService $fixtureService) {}

    /**
     * Display a listing of the resource.
     */
    public function index(): Response
    {
        $fixturesByWeek = Fixture::query()
            ->with(['homeTeam', 'awayTeam'])
            ->orderBy('week')
            ->get()
            ->groupBy('week');

        $totalWeeks = $fixturesByWeek->keys()->max() ?? 0;

        $teamsCount = Team::query()
            ->withFixtures()
            ->active()
            ->count();

        $fixturesCount = Fixture::count();

        return Inertia::render('Fixtures/Index', [
            'teamsCount' => $teamsCount,
            'totalWeeks' => $totalWeeks,
            'fixturesCount' => $fixturesCount,
            'fixturesByWeek' => $fixturesByWeek,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(): RedirectResponse
    {
        try {

            $this->fixtureService->generate();

            return redirect()
                ->back()
                ->with('success', 'Fixtures were successfully generated!');

        } catch (\Exception $exception) {

            Log::error('An error occured while generating fixtures!', [
                'exception' => $exception,
            ]);

            return redirect()
                ->back()
                ->with('error', $exception->getMessage());

        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function reset(): RedirectResponse
    {
        Cache::flush();

        Fixture::truncate();

        $teams = Team::all();

        foreach ($teams as $team) {
            $team->update([
                'played'        => 0,
                'points'        => 0,
                'goals_for'     => 0,
                'goals_against' => 0,
            ]);
        }

        return redirect()
            ->back()
            ->with('success', 'Fixtures were successfully deleted!');
    }
}
