<?php

namespace Database\Factories;

use App\Models\AcademicYear;
use App\Models\Subject;
use App\Models\Teacher;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\TeacherSubject>
 */
class TeacherSubjectFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'academic_year_id' => AcademicYear::get()->random()->id,
            'teacher_id' => Teacher::get()->random()->id,
            'subject_id' => Subject::get()->random()->id,
        ];
    }
}
