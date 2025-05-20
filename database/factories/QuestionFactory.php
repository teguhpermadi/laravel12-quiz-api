<?php

namespace Database\Factories;

use App\Enums\QuestionTypeEnum;
use App\Enums\ScoreEnum;
use App\Enums\TimeEnum;
use Illuminate\Database\Eloquent\Factories\Factory;

class QuestionFactory extends Factory
{
    public function definition(): array
    {
        return [
            'question' => $this->faker->sentence(),
            'question_type' => $this->faker->randomElement(QuestionTypeEnum::cases()),
            'teacher_id' => \App\Models\Teacher::factory(),
            'time' => $this->faker->randomElement(TimeEnum::cases()),
            'score' => $this->faker->randomElement(ScoreEnum::cases()),
        ];
    }
}
