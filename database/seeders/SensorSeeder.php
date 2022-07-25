<?php

namespace Database\Seeders;

use App\Models\Plant;
use App\Models\Reading;
use App\Models\Sensor;
use App\Models\Type;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Database\Seeder;

class SensorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Sensor::factory()
            ->count(5)
            ->has(
                Reading::factory()->hasPlants()
            )
            ->hasRelays()
            // ->has(Relay::factory()->hasSensors())
            ->has(Type::factory()->state(new Sequence(['alias' => 'Humidity'], ['alias' => 'Temperature'])))
            ->create();
    }
}
