<?php

namespace App\Services;

use App\Models\Fixture;

class SimulationService
{
    /**
     * Check wheter the given week can be simulated or not.
     */
    public function canSimulateWeek(int $week): bool
    {
        if ($week === 1) {
            return true;
        }

        $hasUnplayed = Fixture::query()
            ->where('week', '<', $week)
            ->unplayed()
            ->exists();

        return ! $hasUnplayed;
    }

    /**
     * Simulate the given week.
     */
    public function simulateWeek(int $week): bool
    {
        $fixtures = Fixture::query()
            ->with(['homeTeam', 'awayTeam'])
            ->byWeek($week)
            ->unplayed()
            ->get();

        if ($fixtures->isEmpty()) {
            return false;
        }

        foreach ($fixtures as $fixture) {
            $this->simulateMatch($fixture);
        }

        return true;
    }

    /**
     * Simulate the given match.
     */
    protected function simulateMatch(Fixture $fixture): void
    {
        $averageGoals = 2.5;
        $homeAdvantage = 1.2;

        $homeTeam = $fixture->homeTeam;
        $awayTeam = $fixture->awayTeam;

        $homeTeamPower = $homeTeam->power;
        $awayTeamPower = $awayTeam->power;
        $totalTeamPower = $homeTeamPower + $awayTeamPower;

        $baseExpactationForHomeTeam = ($homeTeamPower / $totalTeamPower) * $averageGoals * $homeAdvantage;
        $baseExpactationForAwayTeam = ($awayTeamPower / $totalTeamPower) * $averageGoals;

        $varianceForHomeTeam = mt_rand(-10, 10) / 100.0;
        $varianceForAwayTeam = mt_rand(-10, 10) / 100.0;

        $expectedGoalsForHomeTeam = max(0, $baseExpactationForHomeTeam * (1 + $varianceForHomeTeam));
        $expectedGoalsForAwayTeam = max(0, $baseExpactationForAwayTeam * (1 + $varianceForAwayTeam));

        $homeTeamScore = $this->poissonRandom($expectedGoalsForHomeTeam);
        $awayTeamScore = $this->poissonRandom($expectedGoalsForAwayTeam);

        $fixture->update([
            'home_score' => $homeTeamScore,
            'away_score' => $awayTeamScore,
            'played' => true,
        ]);

        $this->updateTeams($fixture, $homeTeamScore, $awayTeamScore);
    }

    /**
     * Update team statistics.
     */
    protected function updateTeams(Fixture $fixture, int $homeTeamScore, int $awayTeamScore): void
    {
        $homeTeam = $fixture->homeTeam;
        $awayTeam = $fixture->awayTeam;

        $homeTeam->increment('played');
        $awayTeam->increment('played');

        $homeTeam->increment('goals_for', $homeTeamScore);
        $homeTeam->increment('goals_against', $awayTeamScore);

        $awayTeam->increment('goals_for', $awayTeamScore);
        $awayTeam->increment('goals_against', $homeTeamScore);

        if ($homeTeamScore > $awayTeamScore) {
            $homeTeam->increment('points', 3);
        } elseif ($awayTeamScore > $homeTeamScore) {
            $awayTeam->increment('points', 3);
        } else {
            $homeTeam->increment('points', 1);
            $awayTeam->increment('points', 1);
        }
    }

    /**
     * Decide scores randomly with Poisson distribution.
     */
    protected function poissonRandom(float $expectation): int
    {
        $l = exp(-$expectation);
        $k = 0;
        $p = 1.0;

        do {
            $k++;
            $p *= rand(0, 999999) / 1000000.0;
        } while ($p > $l);

        return $k - 1;
    }
}
