<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\QueryBuilder\AllowedFilter;

class Teacher extends Model
{
    /** @use HasFactory<\Database\Factories\TeacherFactory> */
    use HasFactory, HasUlids;

    protected $fillable = [
        'name',
        'gender',
    ];

    public static function allowedFilters()
    {
        return [
            AllowedFilter::exact('id'),
            'name',
            'gender',
        ];
    }

    public static function allowedSorts()
    {
        return ['id', 'name', 'gender', 'created_at', 'updated_at'];
    }

    public static function allowedIncludes()
    {
        return ['subjects', 'subjects.subject', 'subjects.academicYear'];
    }
    
    // Relasi ke TeacherSubject
    public function subjects()
    {
        return $this->hasMany(TeacherSubject::class);
    }
    
    // Method untuk mendapatkan subject berdasarkan tahun akademik
    public function subjectsByAcademicYear($academicYearId)
    {
        return $this->subjects()->where('academic_year_id', $academicYearId)
                    ->with(['subject', 'academicYear']);
    }
}
