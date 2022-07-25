<?php

namespace Database\Seeders;

use App\Models\SensorLocation;
use Illuminate\Database\Seeder;

class SensorLocationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        SensorLocation::factory()
            ->count(10)
            ->create();
    }
}
