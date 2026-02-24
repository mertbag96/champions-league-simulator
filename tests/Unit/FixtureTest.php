<?php

declare(strict_types=1);

use App\Models\Fixture;
use App\Models\Team;

it('determines match outcome helpers correctly', function (): void {
    /** @var Team $home */
    $home = Team::factory()->create();
    /** @var Team $away */
    $away = Team::factory()->create();

    // Not played yet
    /** @var Fixture $fixture */
    $fixture = Fixture::factory()->create([
        'home_team_id' => $home->id,
        'away_team_id' => $away->id,
        'played' => false,
        'home_score' => 0,
        'away_score' => 0,
    ]);

    expect($fixture->isDraw())->toBeFalse()
        ->and($fixture->homeWon())->toBeFalse()
        ->and($fixture->awayWon())->toBeFalse();

    // Draw
    $fixture->update([
        'played' => true,
        'home_score' => 1,
        'away_score' => 1,
    ]);

    expect($fixture->isDraw())->toBeTrue()
        ->and($fixture->homeWon())->toBeFalse()
        ->and($fixture->awayWon())->toBeFalse();

    // Home win
    $fixture->update([
        'home_score' => 2,
        'away_score' => 1,
    ]);

    expect($fixture->homeWon())->toBeTrue()
        ->and($fixture->awayWon())->toBeFalse();

    // Away win
    $fixture->update([
        'home_score' => 0,
        'away_score' => 3,
    ]);

    expect($fixture->awayWon())->toBeTrue()
        ->and($fixture->homeWon())->toBeFalse();
});

it('applies basic query scopes', function (): void {
    /** @var Team $home */
    $home = Team::factory()->create();
    /** @var Team $away */
    $away = Team::factory()->create();

    // Week 1 played draw
    Fixture::factory()->create([
        'home_team_id' => $home->id,
        'away_team_id' => $away->id,
        'week' => 1,
        'home_score' => 1,
        'away_score' => 1,
        'played' => true,
    ]);

    // Week 2 unplayed
    Fixture::factory()->create([
        'home_team_id' => $home->id,
        'away_team_id' => Team::factory()->create()->id,
        'week' => 2,
        'played' => false,
    ]);

    expect(Fixture::byWeek(1)->count())->toBe(1)
        ->and(Fixture::played()->count())->toBe(1)
        ->and(Fixture::unplayed()->count())->toBe(1);
});
