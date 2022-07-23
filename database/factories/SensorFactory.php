<?php

namespace Database\Factories;

use App\Models\Location;
use Illuminate\Database\Eloquent\Factories\Factory;

class SensorFactory extends Factory
{
    /**
     * Define the model's default state.
     * 
     * id	`type`	alias	plant_id	uuid	relay_pins_id	ipaddr	created_at	updated_at
     *
     * @return array
     */
    public function definition()
    {
        return [
            'alias' => $this->faker->word(),
            'uuid' => $this->faker->uuid(),
            'ipaddr' => $this->faker->ipv4(),
            'location_id' => function () {
                return Location::factory()->create()->id;
            }
        ];
    }
}
