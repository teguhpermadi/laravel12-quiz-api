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
    ];
    
    public static function allowedFilters()
    {
        return [
            AllowedFilter::exact('id'),
            'name',
            'level',
        ];
    }
    
    public static function allowedSorts()
    {
        return ['id', 'name', 'level', 'created_at', 'updated_at'];
    }
    
    public static function allowedIncludes()
    {
        return [];
    }
}
