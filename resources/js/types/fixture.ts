import type { Team } from '@/types/team';

export type Fixture = {
    id: number;
    home_team_id: number;
    away_team_id: number;
    week: number;
    home_score: number;
    away_score: number;
    played: boolean;
};

export type FixtureWithTeams = {
    id: number;
    home_team_id: number;
    away_team_id: number;
    week: number;
    home_score: number;
    away_score: number;
    played: boolean;
    home_team: Team;
    away_team: Team;
};
