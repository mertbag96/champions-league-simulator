<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

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
