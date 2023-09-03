<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class CattleStateLogsFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    
    public function definition()
    {
        return [
            //'sent' => $this->faker->boolean,
            'json_data' => json_encode([
                'bpm' => $this->faker->numberBetween(40, 100),
                'temperature' => $this->faker->randomFloat(2, 20.0, 40.0),
                'acceleration_x' => $this->faker->randomFloat(3, 0, 1.5),
                'acceleration_y' => $this->faker->randomFloat(3, 0, 1.5),
                'acceleration_z' => $this->faker->randomFloat(3, 0, 1.5)
            ])
        ];
    }
}
