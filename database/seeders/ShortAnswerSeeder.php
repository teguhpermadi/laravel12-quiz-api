<?php

namespace Database\Seeders;

use App\Enums\QuestionTypeEnum;
use App\Models\Question;
use App\Models\ShortAnswer;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ShortAnswerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Question::factory()
            ->count(10)
            ->state(['question_type' => QuestionTypeEnum::SHORT_ANSWER])
            ->has(
                ShortAnswer::factory()
                    ->count(1),
                    'shortAnswers',
            )
            ->create();
    }
}
