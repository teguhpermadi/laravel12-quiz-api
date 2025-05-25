<?php

namespace Database\Seeders;

use App\Enums\QuestionTypeEnum;
use App\Models\MultipleChoice;
use App\Models\Question;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MultipleChoiceSeeder extends Seeder
{
    public function run(): void
    {
        // Create questions with multiple choice answers
        Question::factory()
            ->count(10)
            ->state(['question_type' => QuestionTypeEnum::MULTIPLE_CHOICE])
            ->has(
                MultipleChoice::factory()
                    ->count(3)
                    ->state(['is_correct' => false]),
                'multipleChoices'
            )
            ->has(
                MultipleChoice::factory()
                    ->state(['is_correct' => true]),
                'multipleChoices'
            )
            ->create();
    }
}
