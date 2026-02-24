<?php

namespace App\Services;

use App\Models\Fixture;
use App\Models\Team;
use Illuminate\Support\Collection;

class FixtureService
{
    /**
     * Generate fixtures for all active teams (double round-robin, balanced weeks).
     */
    public function generate(): void
    {
        Fixture::truncate();

        $teams = Team::query()
            ->active()
            ->get();

        if ($teams->count() < 2) {
            throw new \Exception('At least 2 active teams are required to generate fixtures!');
        }

        $shuffledTeams = $teams->shuffle();

        $singleFixtures = $this->generateSingleRoundRobin($shuffledTeams);
        $reverseFixtures = $singleFixtures->map(function ($week) {
            return $week->map(fn($match) => [
                'home' => $match['away'],
                'away' => $match['home'],
            ]);
        });

        $allFixtures = $singleFixtures->concat($reverseFixtures);

        $allFixtures = $allFixtures->shuffle();

        $week = 1;
        foreach ($allFixtures as $weekFixtures) {
            foreach ($weekFixtures as $fixture) {
                Fixture::create([
                    'home_team_id' => $fixture['home']->id,
                    'away_team_id' => $fixture['away']->id,
                    'week' => $week,
                ]);
            }
            $week++;
        }
    }

    /**
     * Generate single round-robin schedule.
     */
    protected function generateSingleRoundRobin(Collection $teams): Collection
    {
        $teamCount = $teams->count();

        $isOdd = $teamCount % 2 === 1;
        if ($isOdd) {
            $teams->push(null);
            $teamCount += 1;
        }

        $fixtures = collect();
        $teamArray = $teams->all();

        for ($round = 0; $round < $teamCount - 1; $round++) {
            $weekFixtures = collect();
            for ($i = 0; $i < $teamCount / 2; $i++) {
                $home = $teamArray[$i];
                $away = $teamArray[$teamCount - 1 - $i];
                if ($home !== null && $away !== null) {
                    $weekFixtures->push([
                        'home' => $home,
                        'away' => $away,
                    ]);
                }
            }
            $fixtures->push($weekFixtures);

            $teamArray = array_merge(
                [$teamArray[0]],
                array_slice($teamArray, -1, 1),
                array_slice($teamArray, 1, -1),
            );
        }

        return $fixtures;
    }
}
