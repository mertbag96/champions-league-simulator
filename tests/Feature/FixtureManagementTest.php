<?php

declare(strict_types=1);

use App\Models\Fixture;
use App\Models\Team;
use App\Models\User;
use App\Services\FixtureService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertDatabaseCount;
use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\delete;
use function Pest\Laravel\get;
use function Pest\Laravel\post;

it('shows fixtures index with summary data', function (): void {
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

    get(route('fixtures.index'))
        ->assertOk()
        ->assertInertia(
            fn($page) => $page
                ->component('Fixtures/Index')
                ->where('teamsCount', 1)
                ->where('totalWeeks', 1)
                ->where('fixturesCount', 1),
        );
});

it('generates fixtures via service on store', function (): void {
    /** @var User $user */
    $user = User::factory()->create();

    actingAs($user);

    $service = Mockery::mock(FixtureService::class);
    $service->shouldReceive('generate')->once();

    $this->app->instance(FixtureService::class, $service);

    post(route('fixtures.store'))
        ->assertRedirect()
        ->assertSessionHas('success');
});

it('handles exceptions when generating fixtures', function (): void {
    /** @var User $user */
    $user = User::factory()->create();

    actingAs($user);

    Log::spy();

    $service = Mockery::mock(FixtureService::class);
    $service->shouldReceive('generate')
        ->once()
        ->andThrow(new Exception('Generation failed'));

    $this->app->instance(FixtureService::class, $service);

    post(route('fixtures.store'))
        ->assertRedirect()
        ->assertSessionHas('error', 'Generation failed');

    Log::shouldHaveReceived('error')
        ->once();
});

it('resets fixtures and team stats', function (): void {
    /** @var User $user */
    $user = User::factory()->create();

    actingAs($user);

    Cache::shouldReceive('flush')->once();

    /** @var Team $team */
    $team = Team::factory()->create([
        'played' => 5,
        'points' => 10,
        'goals_for' => 8,
        'goals_against' => 4,
    ]);

    /** @var Fixture $fixture */
    $fixture = Fixture::factory()->create([
        'home_team_id' => $team->id,
        'away_team_id' => Team::factory()->create()->id,
        'week' => 1,
        'home_score' => 2,
        'away_score' => 1,
        'played' => true,
    ]);

    delete(route('fixtures.reset'))
        ->assertRedirect()
        ->assertSessionHas('success');

    assertDatabaseCount('fixtures', 0);

    assertDatabaseHas('teams', [
        'id' => $team->id,
        'played' => 0,
        'points' => 0,
        'goals_for' => 0,
        'goals_against' => 0,
    ]);
});
