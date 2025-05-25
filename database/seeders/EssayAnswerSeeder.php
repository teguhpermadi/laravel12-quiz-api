<?php

namespace Database\Seeders;

use App\Enums\QuestionTypeEnum;
use App\Models\EssayAnswer;
use App\Models\Question;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class EssayAnswerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Question::factory()
            ->count(10)
            ->state(['question_type' => QuestionTypeEnum::ESSAY])
            ->has(
                EssayAnswer::factory()
                    ->count(1),
                    'essayAnswers'
            )
            ->create();
    }
}
