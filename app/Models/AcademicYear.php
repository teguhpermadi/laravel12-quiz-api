<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\QueryBuilder\AllowedFilter;

class AcademicYear extends Model
{
    /** @use HasFactory<\Database\Factories\AcademicYearFactory> */
    use HasFactory, HasUlids;

    protected $fillable = [
        'year',
        'semester',
        'teacher_id',
        'is_active',
    ];

    public static function setActive($id)
    {
        // Menonaktifkan semua tahun ajaran
        self::query()->update(['is_active' => false]);

        // Mengaktifkan tahun ajaran yang diberikan
        self::where('id', $id)->update(['is_active' => true]);

        return self::where('is_active', true)->first();
    }

    public function teacher()
    {
        return $this->belongsTo(Teacher::class);
    }

    public static function allowedFilters()
    {
        return [
            AllowedFilter::exact('id'),
            'year',
            'semester',
            AllowedFilter::exact('teacher_id'),
        ];
    }
    
    public static function allowedSorts()
    {
        return ['id', 'year', 'semester', 'created_at', 'updated_at'];
    }
    
    public static function allowedIncludes()
    {
        return ['teacher'];
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
