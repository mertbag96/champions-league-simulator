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
