<?php

namespace Database\Factories;

use App\Models\PlantLocation;
use Illuminate\Database\Eloquent\Factories\Factory;

class PlantFactory extends Factory
{
    /**
     * Define the model's default state.
     * 
     * id	name	plant_date	location	harvest_date	created_at	updated_at
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $this->faker->lastName(),
            'plant_date' => $this->faker->dateTime(),
            'harvest_date' => $this->faker->dateTime(),
            'location_id' => function () {
                return PlantLocation::factory()->createOne()->id;
            }
        ];
    }
}
