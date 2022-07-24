<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class TypeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * * Sensor Types
     * 
     * @return array
     */
    public function definition()
    {
        return [
            'alias' => 'Humidity'
        ];
    }
}
