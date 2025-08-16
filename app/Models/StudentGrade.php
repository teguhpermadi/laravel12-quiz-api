<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\QueryBuilder\AllowedFilter;

class StudentGrade extends Model
{
    /** @use HasFactory<\Database\Factories\StudentGradeFactory> */
    use HasFactory, HasUlids;

    protected $fillable = [
        'student_id',
        'grade_id',
    ];
    
    public static function allowedFilters()
    {
        return [
            AllowedFilter::exact('id'),
            AllowedFilter::exact('student_id'),
            AllowedFilter::exact('grade_id'),
        ];
    }
    
    public static function allowedSorts()
    {
        return ['id', 'student_id', 'grade_id', 'created_at', 'updated_at'];
    }
    
    public static function allowedIncludes()
    {
        return ['student', 'grade'];
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
