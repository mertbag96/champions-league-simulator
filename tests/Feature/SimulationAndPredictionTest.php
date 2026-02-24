<?php

declare(strict_types=1);

use App\Models\Fixture;
use App\Models\Team;
use App\Models\User;
use App\Services\PredictionService;
use Illuminate\Support\Facades\Cache;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\delete;
use function Pest\Laravel\get;
use function Pest\Laravel\post;

it('shows simulation index for a given week', function (): void {
    /** @var User $user */
    $user = User::factory()->create();
    actingAs($user);

    /** @var Team $home */
    $home = Team::factory()->create();
    /** @var Team $away */
    $away = Team::factory()->create();

    /** @var Fixture $fixture */
    $fixture = Fixture::factory()->create([
        'home_team_id' => $home->id,
        'away_team_id' => $away->id,
        'week' => 1,
    ]);

    get(route('simulation.index', 1))
        ->assertOk()
        ->assertInertia(
            fn($page) => $page
                ->component('Simulation/Index')
                ->where('week', 1)
                ->has('teams')
                ->has('matches'),
        );
});

it('redirects to fixtures index if no matches for week', function (): void {
    /** @var User $user */
    $user = User::factory()->create();
    actingAs($user);

    get(route('simulation.index', 1))
        ->assertRedirect(route('fixtures.index'));
});

it('prevents simulating week if previous weeks have unplayed fixtures', function (): void {
    /** @var User $user */
    $user = User::factory()->create();
    actingAs($user);

    /** @var Team $home */
    $home = Team::factory()->create();
    /** @var Team $away */
    $away = Team::factory()->create();

    // Week 1 unplayed
    Fixture::factory()->create([
        'home_team_id' => $home->id,
        'away_team_id' => $away->id,
        'week' => 1,
        'played' => false,
    ]);

    post(route('simulation.simulate', 2))
        ->assertRedirect(route('simulation.index', 2))
        ->assertSessionHas('error');
});

it('simulates week successfully when fixtures exist', function (): void {
    /** @var User $user */
    $user = User::factory()->create();
    actingAs($user);

    /** @var Team $home */
    $home = Team::factory()->create(['power' => 80]);
    /** @var Team $away */
    $away = Team::factory()->create(['power' => 70]);

    // Week 1-3 played against different opponents, week 4 unplayed
    foreach (range(1, 3) as $week) {
        $opponent = Team::factory()->create();

        Fixture::factory()->create([
            'home_team_id' => $home->id,
            'away_team_id' => $opponent->id,
            'week' => $week,
            'played' => true,
            'home_score' => 1,
            'away_score' => 0,
        ]);
    }

    Fixture::factory()->create([
        'home_team_id' => $home->id,
        'away_team_id' => $away->id,
        'week' => 4,
        'played' => false,
    ]);

    post(route('simulation.simulate', 4))
        ->assertRedirect(route('simulation.index', 4))
        ->assertSessionHas('success');
});

it('resets simulation stats and cache', function (): void {
    /** @var User $user */
    $user = User::factory()->create();
    actingAs($user);

    Cache::shouldReceive('flush')->once();

    /** @var Team $home */
    $home = Team::factory()->create([
        'played' => 5,
        'points' => 10,
        'goals_for' => 8,
        'goals_against' => 4,
    ]);
    /** @var Team $away */
    $away = Team::factory()->create();

    /** @var Fixture $fixture */
    $fixture = Fixture::factory()->create([
        'home_team_id' => $home->id,
        'away_team_id' => $away->id,
        'week' => 1,
        'home_score' => 2,
        'away_score' => 1,
        'played' => true,
    ]);

    delete(route('simulation.reset'))
        ->assertRedirect(route('simulation.index', 1))
        ->assertSessionHas('success');

    assertDatabaseHas('teams', [
        'id' => $home->id,
        'played' => 0,
        'points' => 0,
        'goals_for' => 0,
        'goals_against' => 0,
    ]);

    assertDatabaseHas('fixtures', [
        'id' => $fixture->id,
        'home_score' => 0,
        'away_score' => 0,
        'played' => false,
    ]);
});

it('calculates predictions with prediction service without errors', function (): void {
    $service = new PredictionService();

    /** @var Team $team1 */
    $team1 = Team::factory()->create([
        'points' => 10,
        'played' => 5,
        'goals_for' => 8,
        'goals_against' => 4,
        'power' => 80,
        'active' => true,
    ]);

    /** @var Team $team2 */
    $team2 = Team::factory()->create([
        'points' => 8,
        'played' => 5,
        'goals_for' => 6,
        'goals_against' => 3,
        'power' => 70,
        'active' => true,
    ]);

    // leave remaining fixtures empty for a minimal scenario
    $results = $service->calculatePredictions();

    expect($results)->toBeArray();
});
