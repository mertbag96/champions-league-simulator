<?php

declare(strict_types=1);

use App\Models\Team;
use App\Models\User;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\assertDatabaseMissing;
use function Pest\Laravel\get;
use function Pest\Laravel\post;
use function Pest\Laravel\put;
use function Pest\Laravel\delete;

it('lists teams on index page', function (): void {
    /** @var User $user */
    $user = User::factory()->create();

    actingAs($user);

    /** @var Team $team */
    $team = Team::factory()->create();

    get(route('teams.index'))
        ->assertOk()
        ->assertInertia(
            fn($page) => $page
                ->component('Teams/Index')
                ->where('teams.0.id', $team->id),
        );
});

it('creates a team with valid data', function (): void {
    /** @var User $user */
    $user = User::factory()->create();

    actingAs($user);

    $payload = [
        'name' => 'Test Team',
        'power' => 75,
        'active' => true,
    ];

    post(route('teams.store'), $payload)
        ->assertRedirect()
        ->assertSessionHas('success');

    assertDatabaseHas('teams', [
        'name' => 'Test Team',
        'power' => 75,
        'active' => true,
    ]);
});

it('validates team creation input', function (): void {
    /** @var User $user */
    $user = User::factory()->create();

    actingAs($user);

    post(route('teams.store'), [
        'name' => '',
        'power' => 200,
        'active' => 'not-a-boolean',
    ])
        ->assertSessionHasErrors([
            'name',
            'power',
            'active',
        ]);
});

it('updates a team with valid data', function (): void {
    /** @var User $user */
    $user = User::factory()->create();

    actingAs($user);

    /** @var Team $team */
    $team = Team::factory()->create([
        'name' => 'Old Name',
        'power' => 50,
        'active' => true,
    ]);

    $payload = [
        'name' => 'New Name',
        'power' => 80,
        'active' => false,
    ];

    put(route('teams.update', $team), $payload)
        ->assertRedirect()
        ->assertSessionHas('success');

    assertDatabaseHas('teams', [
        'id' => $team->id,
        'name' => 'New Name',
        'power' => 80,
        'active' => false,
    ]);
});

it('validates team update input', function (): void {
    /** @var User $user */
    $user = User::factory()->create();

    actingAs($user);

    /** @var Team $otherTeam */
    $otherTeam = Team::factory()->create([
        'name' => 'Existing Team',
    ]);

    /** @var Team $team */
    $team = Team::factory()->create([
        'name' => 'Team To Update',
    ]);

    put(route('teams.update', $team), [
        'name' => $otherTeam->name, // duplicate
        'power' => -10,
        'active' => 'string',
    ])
        ->assertSessionHasErrors([
            'name',
            'power',
            'active',
        ]);
});

it('deletes a team', function (): void {
    /** @var User $user */
    $user = User::factory()->create();

    actingAs($user);

    /** @var Team $team */
    $team = Team::factory()->create();

    delete(route('teams.destroy', $team))
        ->assertRedirect()
        ->assertSessionHas('success');

    assertDatabaseMissing('teams', [
        'id' => $team->id,
    ]);
});
