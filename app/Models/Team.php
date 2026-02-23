<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

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
