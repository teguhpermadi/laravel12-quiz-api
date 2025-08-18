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
        'teacher_id',
        'subject_id',
        'grade_id',
    ];

    public static function allowedFilters()
    {
        return [
            AllowedFilter::exact('id'),
            AllowedFilter::exact('teacher_id'),
            AllowedFilter::exact('subject_id'),
        ];
    }
    
    public static function allowedSorts()
    {
        return ['id', 'teacher_id', 'subject_id', 'grade_id', 'created_at', 'updated_at'];
    }
    
    public static function allowedIncludes()
    {
        return ['teacher', 'subject', 'grade'];
    }

    public function teacher()
    {
        return $this->belongsTo(Teacher::class);
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }
    
    public function grade()
    {
        return $this->belongsTo(Grade::class);
    }
}
