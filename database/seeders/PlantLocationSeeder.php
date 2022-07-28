<?php

namespace Database\Seeders;

use App\Models\PlantLocation;
use Illuminate\Database\Seeder;

class PlantLocationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        PlantLocation::factory()
            ->count(10)
            ->create();
    }
}
