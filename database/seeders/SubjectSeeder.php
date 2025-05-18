<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SubjectSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $subjects = [
            [
                'name' => 'Math',
                'code' => 'MATH101',
            ],
            [
                'name' => 'Science',
                'code' => 'SCI101',
            ],
            [
                'name' => 'English',
                'code' => 'ENG101',
            ],
            [
                'name' => 'History',
                'code' => 'HIST101',
            ],
            [
                'name' => 'Geography',
                'code' => 'GEO101',
            ],
        ];

        foreach ($subjects as $subject) {
            \App\Models\Subject::create($subject);
        }
    }
}
