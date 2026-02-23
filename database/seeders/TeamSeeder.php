<?php

namespace Database\Seeders;

use App\Models\Team;
use Illuminate\Database\Seeder;

class TeamSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        /**
         * @var list<string>
         */
        $names = [
            'Bayern Munchen',
            'Real Madrid',
            'PSG',
            'Arsenal',
        ];

        /**
         * @var list<int>
         */
        $powers = [95, 98, 97, 93];

        for ($i = 0; $i < 4; $i++) {
            Team::create([
                'name'      => $names[$i],
                'power'     => $powers[$i],
                'active'    => true,
            ]);
        }
    }
}
