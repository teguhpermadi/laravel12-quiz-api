<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\TrueFalse>
 */
class TrueFalseFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'choice' => $this->faker->randomElement(['True', 'False']),
            'is_correct' => $this->faker->randomElement(['True', 'False']),
        ];
    }
}
