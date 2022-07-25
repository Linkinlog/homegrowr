<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class PlantLocationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'alias' => $this->faker->city()
        ];
    }
}
