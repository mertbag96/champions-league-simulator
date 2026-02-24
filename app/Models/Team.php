<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * @property int $id
 * @property string $name
 * @property int $power
 * @property int $played
 * @property int $points
 * @property int $goals_for
 * @property int $goals_against
 * @property bool $active
 * @property \Carbon\CarbonImmutable|null $created_at
 * @property \Carbon\CarbonImmutable|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Fixture> $awayFixtures
 * @property-read int|null $away_fixtures_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Fixture> $fixtures
 * @property-read int|null $fixtures_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Fixture> $homeFixtures
 * @property-read int|null $home_fixtures_count
 *
 * @method static Builder<static>|Team active()
 * @method static Builder<static>|Team newModelQuery()
 * @method static Builder<static>|Team newQuery()
 * @method static Builder<static>|Team query()
 * @method static Builder<static>|Team whereActive($value)
 * @method static Builder<static>|Team whereCreatedAt($value)
 * @method static Builder<static>|Team whereGoalsAgainst($value)
 * @method static Builder<static>|Team whereGoalsFor($value)
 * @method static Builder<static>|Team whereId($value)
 * @method static Builder<static>|Team whereName($value)
 * @method static Builder<static>|Team wherePlayed($value)
 * @method static Builder<static>|Team wherePoints($value)
 * @method static Builder<static>|Team wherePower($value)
 * @method static Builder<static>|Team whereUpdatedAt($value)
 * @method static Builder<static>|Team withFixtures()
 * @method static \Database\Factories\TeamFactory factory($count = null, $state = [])
 *
 * @mixin \Eloquent
 */
class Team extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'power',
        'played',
        'points',
        'goals_for',
        'goals_against',
        'active',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'name'          => 'string',
            'power'         => 'integer',
            'played'        => 'integer',
            'points'        => 'integer',
            'goals_for'     => 'integer',
            'goals_against' => 'integer',
            'active'        => 'boolean',
        ];
    }

    /**
     * Get statistics of a team until the given week.
     */
    public function statsUntilWeek(int $week): array
    {
        $fixtures = Fixture::query()
            ->where('week', '<=', $week)
            ->played()
            ->where(function ($query): void {
                $query
                    ->where('home_team_id', $this->id)
                    ->orWhere('away_team_id', $this->id);
            })
            ->get();

        $played = 0;
        $wins = 0;
        $draws = 0;
        $losses = 0;
        $goalsFor = 0;
        $goalsAgainst = 0;
        $points = 0;

        foreach ($fixtures as $fixture) {
            $isHomeTeam = $fixture->home_team_id === $this->id;

            $teamGoals = $isHomeTeam
                ? $fixture->home_score
                : $fixture->away_score;

            $opponentGoals = $isHomeTeam
                ? $fixture->away_score
                : $fixture->home_score;

            $goalsFor += $teamGoals;
            $goalsAgainst += $opponentGoals;

            if ($teamGoals > $opponentGoals) {
                $wins++;
                $points += 3;
            } elseif ($teamGoals === $opponentGoals) {
                $draws++;
                $points += 1;
            } else {
                $losses++;
            }

            $played++;
        }

        return [
            'played' => $played,
            'wins' => $wins,
            'draws' => $draws,
            'losses' => $losses,
            'goals_for' => $goalsFor,
            'goals_against' => $goalsAgainst,
            'goal_difference' => $goalsFor - $goalsAgainst,
            'points' => $points,
        ];
    }

    /**
     * Get the next week's opponent team from the given week.
     */
    public function nextOpponentForWeek(int $week): string
    {
        $nextWeek = $week + 1;

        $fixture = Fixture::query()
            ->byWeek($nextWeek)
            ->where(function ($query): void {
                $query
                    ->where('home_team_id', $this->id)
                    ->orWhere('away_team_id', $this->id);
            })
            ->with(['homeTeam', 'awayTeam'])
            ->first();

        if (! $fixture) {
            return '';
        }

        return $fixture->home_team_id === $this->id
            ? $fixture->awayTeam->name
            : $fixture->homeTeam->name;
    }

    /**
     * Get the active teams.
     *
     * @param  mixed  $query
     */
    public function scopeActive($query): Builder
    {
        return $query->where('active', true);
    }

    /**
     * Get the teams with fixtures.
     */
    public function scopeWithFixtures($query): Builder
    {
        return $query->whereHas('fixtures');
    }

    /**
     * Does team have any fixtures?
     */
    public function hasFixtures(): bool
    {
        return Fixture::query()
            ->where('home_team_id', $this->id)
            ->orWhere('away_team_id', $this->id)
            ->exists();
    }

    /**
     * Get the fixtures of the team.
     */
    public function fixtures(): HasMany
    {
        return $this
            ->hasMany(Fixture::class, 'home_team_id')
            ->orWhere('away_team_id', $this->id);
    }

    /**
     * Get the home fixtures of the team.
     */
    public function homeFixtures(): HasMany
    {
        return $this->hasMany(Fixture::class, 'home_team_id');
    }

    /**
     * Get the away fixtures of the team.
     */
    public function awayFixtures(): HasMany
    {
        return $this->hasMany(Fixture::class, 'away_team_id');
    }
}
