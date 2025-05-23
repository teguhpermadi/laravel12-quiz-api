<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        $this->call([
            TeacherSeeder::class,
            StudentSeeder::class,
            GradeSeeder::class,
            SubjectSeeder::class,
            AcademicYearSeeder::class,
            StudentGradeSeeder::class,
            TeacherSubjectSeeder::class,
            LiteratureSeeder::class, // Tambahkan LiteratureSeeder sebelum ExamSeeder dan QuestionSeeder
            ExamSeeder::class,
            QuestionSeeder::class,
        ]);
    }
}
