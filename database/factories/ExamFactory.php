<?php

namespace Database\Factories;

use App\Models\Grade;
use App\Models\Subject;
use App\Models\Teacher;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Exam>
 */
class ExamFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => \fake()->title(),
            'teacher_id' => Teacher::get()->random()->id,
            'subject_id' => Subject::get()->random()->id,
            'grade_id' => Grade::get()->random()->id,
        ];
    }
}
