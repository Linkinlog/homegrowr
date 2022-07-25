<?php

namespace Database\Factories;

use App\Models\Relay_type;
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
            'type_id' => function () {
                return Relay_type::factory()->createOne(['alias' => 'Power'])->id;
            }
        ];
    }
}
