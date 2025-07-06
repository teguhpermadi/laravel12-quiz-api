<?php

namespace App\Models;

use App\Events\StudentCreated;
use App\Events\StudentDeleted;
use App\Events\StudentUpdated;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Support\Facades\Log;
use Spatie\QueryBuilder\AllowedFilter;

class Student extends Model
{
    use HasFactory, HasUlids;

    protected $fillable = [
        'name',
        'gender',
        'nisn',
        'nis',
    ];

    protected static function boot()
    {
        parent::boot();

        static::created(function (Student $student) {
            Log::info('DEBUG: Student model created event triggered.', ['student_id' => $student->id, 'name' => $student->name]);
            event(new StudentCreated($student));
        });

        static::updated(function (Student $student) {
            Log::info('DEBUG: Student model updated event triggered.', ['student_id' => $student->id, 'name' => $student->name, 'changes' => $student->getDirty()]);
            $student->refresh();
            event(new StudentUpdated($student));
        });

        static::deleted(function (Student $student) {
            Log::info('DEBUG: Student model deleted event triggered.', ['student_id' => $student->id, 'name' => $student->name]);
            event(new StudentDeleted($student->id));
        });
    }

    public static function allowedFilters()
    {
        return [
            AllowedFilter::exact('id'),
            'name',
            'gender',
            'nisn',
            'nis',
        ];
    }

    public static function allowedSorts()
    {
        return ['id', 'name', 'gender', 'nisn', 'nis', 'created_at', 'updated_at'];
    }

    public static function allowedIncludes()
    {
        return [
            'grades', 
            'grades.grade', 
            'grades.academicYear', 
            'user'
        ];
    }
    
    // Relasi ke StudentGrade
    public function grades()
    {
        return $this->hasMany(StudentGrade::class);
    }
    
    // Method untuk mendapatkan grade berdasarkan tahun akademik
    public function gradesByAcademicYear($academicYearId)
    {
        return $this->grades()->where('academic_year_id', $academicYearId)
                    ->with(['grade', 'academicYear']);
    }

    public function user(): MorphOne
    {
        return $this->morphOne(User::class, 'userable');
    }

    /**
     * Dapatkan semua token link yang terkait dengan guru ini.
     */
    public function profileLinkTokens(): MorphMany // Ubah nama metode dari linkTokens()
    {
        return $this->morphMany(ProfileLinkToken::class, 'linkable');
    }
}
