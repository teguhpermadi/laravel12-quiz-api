<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\EssayAnswer>
 */
class EssayAnswerFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'answer' => $this->faker->paragraph,
            'correction_with_ai' => $this->faker->boolean(),
            'prompt' => $this->faker->sentence,
        ];
    }
}
