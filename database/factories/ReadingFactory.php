<?php

namespace Database\Factories;

use App\Models\Plant;
use App\Models\Status;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\Sequence;

class ReadingFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * id	sensor_id	value	status_id	created_at	updated_at plant_id
     *
     * @return array
     */
    public function definition()
    {
        return [
            'value' => rand(1, 500),
            'status_id' => 1
        ];
    }
}
