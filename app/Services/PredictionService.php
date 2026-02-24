<?php

namespace App\Services;

use App\Models\Fixture;
use App\Models\Team;
use Illuminate\Support\Collection;

class PredictionService
{
    /**
     * Calculate championship predictions.
     */
    public function calculatePredictions(): array
    {
        $teams = Team::query()
            ->withFixtures()
            ->active()
            ->get();

        $remainingFixtures = Fixture::query()
            ->unplayed()
            ->with(['homeTeam', 'awayTeam'])
            ->get();

        $stats = $this->calculateAvarageAttack($teams);

        $expectedPoints = [];

        foreach ($teams as $team) {
            $expectedPoints[$team->id] = $team->points;
        }

        foreach ($remainingFixtures as $fixture) {
            [$homeEP, $awayEP] = $this->expectedPointsForFixture($fixture, $stats);

            $expectedPoints[$fixture->home_team_id] += $homeEP;
            $expectedPoints[$fixture->away_team_id] += $awayEP;
        }

        $expectedPoints = $this->applyMathematicalElimination($teams, $expectedPoints, $remainingFixtures);

        $percentages = $this->softmaxProbabilities($expectedPoints);

        $results = $this->formatResults($teams, $percentages);

        return $results;
    }

    /**
     * Calculate average attack.
     */
    protected function calculateAvarageAttack(Collection $teams): array
    {
        $totalGoalsFor = $teams->sum('goals_for');
        $totalPlayed = max(1, $teams->sum('played'));

        $avgAttack = $totalGoalsFor / $totalPlayed;

        return [
            'avg_attack' => $avgAttack,
        ];
    }

    /**
     * Expected points from a fixture.
     */
    protected function expectedPointsForFixture(Fixture $fixture, array $leagueStats): array
    {
        $drawProbability = 0.25;
        $homeTeamAdvantage = 1.2;

        $homeTeam = $fixture->homeTeam;
        $awayTeam = $fixture->awayTeam;

        $homeTeamAttack = $this->attackPower($homeTeam, $leagueStats);
        $awayTeamAttack = $this->attackPower($awayTeam, $leagueStats);

        $homeTeamDefense = $this->defensePower($homeTeam, $leagueStats);
        $awayTeamDefense = $this->defensePower($awayTeam, $leagueStats);

        $homeTeamPower
            = $homeTeam->power
            * $homeTeamAttack
            * $awayTeamDefense
            * $homeTeamAdvantage;

        $awayTeamPower
            = $awayTeam->power
            * $awayTeamAttack
            * $homeTeamDefense;

        $totalPower = $homeTeamPower + $awayTeamPower;

        $homeTeamWin = ($homeTeamPower / $totalPower) * (1 - $drawProbability);
        $awayTeamWin = ($awayTeamPower / $totalPower) * (1 - $drawProbability);

        $expectedHomeTeamPoints = 3 * $homeTeamWin + $drawProbability;
        $expectedAwayTeamPoints = 3 * $awayTeamWin + $drawProbability;

        return [
            $expectedHomeTeamPoints,
            $expectedAwayTeamPoints,
        ];
    }

    /**
     * Calculate team's attack power depending on past matches.
     */
    protected function attackPower(Team $team, array $leagueStats): float
    {
        if ($team->played === 0) {
            return 1;
        }

        $attack = $team->goals_for / $team->played;

        return max(0.5, $attack / $leagueStats['avg_attack']);
    }

    /**
     * Calculate team's defense power depending on past matches.
     */
    protected function defensePower(Team $team, array $leagueStats): float
    {
        if ($team->played === 0) {
            return 1;
        }

        $defense = $team->goals_against / $team->played;

        return max(0.5, $leagueStats['avg_attack'] / max(0.1, $defense));
    }

    /**
     * Remove mathematically eliminated teams.
     */
    protected function applyMathematicalElimination(
        Collection $teams,
        array $expectedPoints,
        Collection $remainingFixtures,
    ): array {
        $remainingMatches = [];

        foreach ($remainingFixtures as $fixture) {
            $remainingMatches[$fixture->home_team_id]
                = ($remainingMatches[$fixture->home_team_id] ?? 0) + 1;

            $remainingMatches[$fixture->away_team_id]
                = ($remainingMatches[$fixture->away_team_id] ?? 0) + 1;
        }

        $currentLeaderPoints = $teams->max('points');

        $aliveTeams = [];

        foreach ($teams as $team) {
            $maxPossible
                = $team->points + 3 * ($remainingMatches[$team->id] ?? 0);

            if ($maxPossible >= $currentLeaderPoints) {
                $aliveTeams[] = $team->id;
            } else {
                $expectedPoints[$team->id] = 0;
            }
        }

        if (count($aliveTeams) === 1) {
            return collect($expectedPoints)
                ->mapWithKeys(
                    fn($v, $id): array => [$id => $id === $aliveTeams[0] ? 100 : 0],
                )
                ->toArray();
        }

        return $expectedPoints;
    }

    /**
     * Convert scores to probabilities.
     */
    protected function softmaxProbabilities(array $scores): array
    {
        $exp = [];

        foreach ($scores as $id => $score) {
            $exp[$id] = exp($score / 10);
        }

        $sum = array_sum($exp);

        $percentages = [];

        foreach ($exp as $id => $value) {
            $percentages[$id] = round(($value / $sum) * 100, 1);
        }

        return $percentages;
    }

    /**
     * Add team names to percentages.
     */
    protected function formatResults(Collection $teams, array $percentages): array
    {
        $results = [];

        foreach ($teams as $team) {
            $results[] = [
                'name' => $team->name,
                'percentage' => $percentages[$team->id] ?? 0,
            ];
        }

        usort(
            $results,
            fn($a, $b): int => $b['percentage'] <=> $a['percentage'],
        );

        return $results;
    }
}
