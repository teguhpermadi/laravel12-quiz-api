<?php

namespace Database\Seeders;

use App\Models\Exam;
use App\Models\Question;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ExamQuestionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get all exams and questions
        $exams = Exam::all();
        $questions = Question::all();

        // Attach random questions to each exam with random order
        $exams->each(function ($exam) use ($questions) {
            $randomQuestions = $questions->random(rand(5, 15));
            $order = 1;
            
            foreach ($randomQuestions as $question) {
                $exam->questions()->attach($question->id, ['order' => $order++]);
            }
        });
    }
}
