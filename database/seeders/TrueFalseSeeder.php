<?php

namespace Database\Seeders;

use App\Enums\QuestionTypeEnum;
use App\Models\Question;
use App\Models\TrueFalse;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TrueFalseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Question::factory()
            ->count(10)
            ->state(['question_type' => QuestionTypeEnum::TRUE_FALSE])
            ->has(
                TrueFalse::factory()
                    ->state(['is_correct' => false]),
                'trueFalses',
            )
            ->has(
                TrueFalse::factory()
                    ->state(['is_correct' => true]),
                'trueFalses',
            )
            ->create();
    }
}
