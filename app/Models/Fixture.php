<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
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
