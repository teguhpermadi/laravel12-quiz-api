<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\QueryBuilder\AllowedFilter;

class Subject extends Model
{
    /** @use HasFactory<\Database\Factories\SubjectFactory> */
    use HasFactory, HasUlids;

    protected $fillable = [
        'name',
        'code',
    ];

    public static function allowedFilters()
    {
        return [
            AllowedFilter::exact('id'),
            'name',
            'code',
        ];
    }

    public static function allowedSorts()
    {
        return ['id', 'name', 'code', 'created_at', 'updated_at'];
    }

    public static function allowedIncludes()
    {
        return [];
    }
}
