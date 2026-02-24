<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * @property int $id
 * @property int $home_team_id
 * @property int $away_team_id
 * @property int $week
 * @property int $home_score
 * @property int $away_score
 * @property bool $played
 * @property \Carbon\CarbonImmutable|null $created_at
 * @property \Carbon\CarbonImmutable|null $updated_at
 * @property-read \App\Models\Team $awayTeam
 * @property-read \App\Models\Team $homeTeam
 *
 * @method static Builder<static>|Fixture awayWon()
 * @method static Builder<static>|Fixture byWeek(int $week)
 * @method static Builder<static>|Fixture draws()
 * @method static Builder<static>|Fixture homeWon()
 * @method static Builder<static>|Fixture newModelQuery()
 * @method static Builder<static>|Fixture newQuery()
 * @method static Builder<static>|Fixture played()
 * @method static Builder<static>|Fixture query()
 * @method static Builder<static>|Fixture unplayed()
 * @method static Builder<static>|Fixture whereAwayScore($value)
 * @method static Builder<static>|Fixture whereAwayTeamId($value)
 * @method static Builder<static>|Fixture whereCreatedAt($value)
 * @method static Builder<static>|Fixture whereHomeScore($value)
 * @method static Builder<static>|Fixture whereHomeTeamId($value)
 * @method static Builder<static>|Fixture whereId($value)
 * @method static Builder<static>|Fixture wherePlayed($value)
 * @method static Builder<static>|Fixture whereUpdatedAt($value)
 * @method static Builder<static>|Fixture whereWeek($value)
 *
 * @mixin \Eloquent
 */
class Fixture extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'home_team_id',
        'away_team_id',
        'week',
        'home_score',
        'away_score',
        'played',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'home_team_id'  => 'integer',
            'away_team_id'  => 'integer',
            'week'          => 'integer',
            'home_score'    => 'integer',
            'away_score'    => 'integer',
            'played'        => 'boolean',
        ];
    }

    /**
     * Get fixtures / matches for the given week.
     */
    public function scopeByWeek($query, int $week): Builder
    {
        return $query->where('week', $week);
    }

    /**
     * Get played matches.
     */
    public function scopePlayed($query): Builder
    {
        return $query->where('played', true);
    }

    /**
     * Get unplayed matches.
     */
    public function scopeUnplayed($query): Builder
    {
        return $query->where('played', false);
    }

    /**
     * Get the matches where home team won.
     */
    public function scopeHomeWon($query): Builder
    {
        return $query->whereColumn('home_score', '>', 'away_score');
    }

    /**
     * Get the matches where away team won.
     */
    public function scopeAwayWon($query): Builder
    {
        return $query->whereColumn('away_score', '>', 'home_score');
    }

    /**
     * Get the draw matches.
     */
    public function scopeDraws($query): Builder
    {
        return $query->whereColumn('home_score', 'away_score');
    }

    /**
     * Is the match draw?
     */
    public function isDraw(): bool
    {
        if (! $this->played) {
            return false;
        }

        return $this->home_score === $this->away_score;
    }

    /**
     * Did home team win the match?
     */
    public function homeWon(): bool
    {
        if (! $this->played) {
            return false;
        }

        return $this->home_score > $this->away_score;
    }

    /**
     * Did away team win the match?
     */
    public function awayWon(): bool
    {
        if (! $this->played) {
            return false;
        }

        return $this->away_score > $this->home_score;
    }

    /**
     * Get the home team that plays in this fixture.
     */
    public function homeTeam(): BelongsTo
    {
        return $this->belongsTo(Team::class, 'home_team_id');
    }

    /**
     * Get the away team that plays in this fixture.
     */
    public function awayTeam(): BelongsTo
    {
        return $this->belongsTo(Team::class, 'away_team_id');
    }
}
