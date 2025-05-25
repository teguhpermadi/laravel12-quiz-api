<?php

namespace Database\Seeders;

use App\Enums\QuestionTypeEnum;
use App\Models\ComplexMultipleChoice;
use App\Models\Question;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ComplexMultipleChoiceSeeder extends Seeder
{
    public function run(): void
    {
        // Create questions with complex multiple choice answers
        Question::factory()
            ->count(10)
            ->state(['question_type' => QuestionTypeEnum::COMPLEX_MULTIPLE_CHOICE])
            ->has(
                ComplexMultipleChoice::factory()
                    ->count(3)
                    ->state(['is_correct' => false]),
                'complexMultipleChoices'
            )
            ->has(
                ComplexMultipleChoice::factory()
                    ->state(['is_correct' => true]),
                'complexMultipleChoices'
            )
            ->create();
    }
}
