<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\QueryBuilder\AllowedFilter;

class Grade extends Model
{
    /** @use HasFactory<\Database\Factories\GradeFactory> */
    use HasFactory, HasUlids;

    protected $fillable = [
        'name',
        'level',
        'academic_year_id',
    ];
    
    public static function allowedFilters()
    {
        return [
            AllowedFilter::exact('id'),
            'name',
            'level',
            AllowedFilter::exact('academic_year_id'),
        ];
    }
    
    public static function allowedSorts()
    {
        return ['id', 'name', 'level', 'academic_year_id', 'created_at', 'updated_at'];
    }
    
    public static function allowedIncludes()
    {
        return ['students', 'students.student', 'academicYear'];
    }

    public function students()
    {
        return $this->hasMany(StudentGrade::class);
    }

    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class);
    }
}
