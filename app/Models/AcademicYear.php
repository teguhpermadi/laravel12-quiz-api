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
    ];

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
}
