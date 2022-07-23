<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class RelayFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'pin' => $this->faker->numberBetween(1, 100),
            'type' => 'Power'
        ];
    }
}
