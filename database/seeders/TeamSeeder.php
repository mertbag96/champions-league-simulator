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
            'Arsenal',
            'Manchester City',
            'Chelsea',
            'Liverpool',
        ];

        /**
         * @var list<int>
         */
        $powers = [96, 94, 88, 80];

        for ($i = 0; $i < 4; $i++) {
            Team::create([
                'name'      => $names[$i],
                'power'     => $powers[$i],
                'active'    => true,
            ]);
        }
    }
}
