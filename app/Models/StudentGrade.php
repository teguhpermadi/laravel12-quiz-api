<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentGrade extends Model
{
    /** @use HasFactory<\Database\Factories\StudentGradeFactory> */
    use HasFactory, HasUlids;

    protected $fillable = [
        'academic_year_id',
        'student_id',
        'grade_id',
    ];

    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }
    
    public function grade()
    {
        return $this->belongsTo(Grade::class);
    }
}
