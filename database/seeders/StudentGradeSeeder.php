<?php

namespace Database\Seeders;

use App\Models\AcademicYear;
use App\Models\Grade;
use App\Models\Student;
use App\Models\StudentGrade;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class StudentGradeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // buatkan seeder untuk student_grades
        // dengan jumlah data setiap grade memiliki 10 student dan setiap academic year memiliki 5 grade
        // dan setiap student memiliki 1 grade

        $grades = Grade::get();
        $academicYears = AcademicYear::get();
        $students = Student::get();
        foreach ($academicYears as $academicYear) {
            foreach ($grades as $grade) {
                foreach ($students as $student) {
                    try {
                        //code...
                        StudentGrade::create([
                            'academic_year_id' => $academicYear->id,
                            'student_id' => $student->id,
                            'grade_id' => $grade->id,
                        ]);
                    } catch (\Throwable $th) {
                        //throw $th;
                    }
                }
            }
        }
    }
}
