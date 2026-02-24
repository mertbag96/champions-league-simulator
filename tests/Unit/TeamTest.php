<?php

declare(strict_types=1);

use App\Models\Fixture;
use App\Models\Team;

it('computes stats until a given week', function (): void {
    /** @var Team $team */
    $team = Team::factory()->create();

    /** @var Team $opponent */
    $opponent = Team::factory()->create();

    // Week 1: team wins 2-1 at home
    Fixture::factory()->create([
        'home_team_id' => $team->id,
        'away_team_id' => $opponent->id,
        'week' => 1,
        'home_score' => 2,
        'away_score' => 1,
        'played' => true,
    ]);

    // Week 2: 1-1 draw, team plays away (different home/away pair)
    Fixture::factory()->create([
        'home_team_id' => $opponent->id,
        'away_team_id' => $team->id,
        'week' => 2,
        'home_score' => 1,
        'away_score' => 1,
        'played' => true,
    ]);

    $week1 = $team->statsUntilWeek(1);
    $week2 = $team->statsUntilWeek(2);

    expect($week1)->toMatchArray([
        'played' => 1,
        'wins' => 1,
        'draws' => 0,
        'losses' => 0,
        'goals_for' => 2,
        'goals_against' => 1,
        'goal_difference' => 1,
        'points' => 3,
    ]);

    expect($week2)->toMatchArray([
        'played' => 2,
        'wins' => 1,
        'draws' => 1,
        'losses' => 0,
        'goals_for' => 3,
        'goals_against' => 2,
        'goal_difference' => 1,
        'points' => 4,
    ]);
});

it('returns next opponent name for a week', function (): void {
    /** @var Team $team */
    $team = Team::factory()->create();

    /** @var Team $firstOpponent */
    $firstOpponent = Team::factory()->create();

    /** @var Team $secondOpponent */
    $secondOpponent = Team::factory()->create();

    // Week 1 fixture
    Fixture::factory()->create([
        'home_team_id' => $team->id,
        'away_team_id' => $firstOpponent->id,
        'week' => 1,
        'played' => true,
    ]);

    // Week 2 fixture
    Fixture::factory()->create([
        'home_team_id' => $secondOpponent->id,
        'away_team_id' => $team->id,
        'week' => 2,
        'played' => false,
    ]);

    $opponentName = $team->nextOpponentForWeek(1);

    expect($opponentName)->toBe($secondOpponent->name);
});

it('reports whether a team has fixtures', function (): void {
    /** @var Team $team */
    $team = Team::factory()->create();

    expect($team->hasFixtures())->toBeFalse();

    /** @var Team $opponent */
    $opponent = Team::factory()->create();

    Fixture::factory()->create([
        'home_team_id' => $team->id,
        'away_team_id' => $opponent->id,
        'week' => 1,
    ]);

    expect($team->hasFixtures())->toBeTrue();
});
