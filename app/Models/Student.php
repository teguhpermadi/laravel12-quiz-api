<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
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
        return [];
    }
}
