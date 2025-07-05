<?php

namespace App\Models;

use App\Events\TeacherCreated;
use App\Events\TeacherDeleted;
use App\Events\TeacherUpdated;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use Spatie\QueryBuilder\AllowedFilter;

class Teacher extends Model
{
    /** @use HasFactory<\Database\Factories\TeacherFactory> */
    use HasFactory, HasUlids;

    protected $fillable = [
        'name',
        'gender',
        'nip',
    ];

    protected static function boot()
    {
        parent::boot();

        static::created(function (Teacher $teacher) {
            Log::info('DEBUG: Teacher model created event triggered.', ['teacher_id' => $teacher->id, 'name' => $teacher->name]);
            event(new TeacherCreated($teacher));
        });

        static::updated(function (Teacher $teacher) {
            Log::info('DEBUG: Teacher model updated event triggered.', ['teacher_id' => $teacher->id, 'name' => $teacher->name, 'changes' => $teacher->getDirty()]);
            $teacher->refresh();
            event(new TeacherUpdated($teacher));
        });

        static::deleted(function (Teacher $teacher) {
            Log::info('DEBUG: Teacher model deleted event triggered.', ['teacher_id' => $teacher->id, 'name' => $teacher->name]);
            event(new TeacherDeleted($teacher->id));
        });
    }

    public static function allowedFilters()
    {
        return [
            AllowedFilter::exact('id'),
            'name',
            'gender',
            'nip',
        ];
    }

    public static function allowedSorts()
    {
        return [
            'id', 
            'name', 
            'gender', 
            'nip', 
            'created_at', 
            'updated_at'
        ];
    }

    public static function allowedIncludes()
    {
        return ['subjects', 'subjects.subject', 'subjects.academicYear'];
    }

    public function getNipAttribute($value)
    {
        return $value ?: '-';
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
