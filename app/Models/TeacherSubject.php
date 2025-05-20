<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\QueryBuilder\AllowedFilter;

class TeacherSubject extends Model
{
    /** @use HasFactory<\Database\Factories\TeacherSubjectFactory> */
    use HasFactory, HasUlids;

    protected $fillable = [
        'academic_year_id',
        'teacher_id',
        'subject_id',
    ];

    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function teacher()
    {
        return $this->belongsTo(Teacher::class);
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }
    
    public static function allowedFilters()
    {
        return [
            AllowedFilter::exact('id'),
            AllowedFilter::exact('academic_year_id'),
            AllowedFilter::exact('teacher_id'),
            AllowedFilter::exact('subject_id'),
        ];
    }
    
    public static function allowedSorts()
    {
        return ['id', 'academic_year_id', 'teacher_id', 'subject_id', 'created_at', 'updated_at'];
    }
    
    public static function allowedIncludes()
    {
        return ['academicYear', 'teacher', 'subject'];
    }
}
