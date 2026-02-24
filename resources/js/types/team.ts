export type Team = {
    id: number;
    name: string;
    power: number;
    played?: number;
    points?: number;
    goals_for?: number;
    goals_against?: number;
    active: boolean;
};

export type TeamCreateForm = {
    name: string | null;
    power: number | string | null;
    active: boolean | null;
};

export type TeamEditForm = {
    name: string | null;
    power: number | string | null;
    active: boolean | null;
};

export type TeamForTable = {
    id: number;
    name: string;
    power: number;
    played?: number;
    wins?: number;
    draws?: number;
    losses?: number;
    points?: number;
    goals_for?: number;
    goals_against?: number;
    goal_difference?: number;
    active: boolean;
    next_opponent: string;
};

export type TeamPrediction = {
    name: string;
    percentage: number;
}
