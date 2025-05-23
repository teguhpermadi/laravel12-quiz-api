<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Literature extends Model implements HasMedia
{
    use HasFactory, HasUlids, InteractsWithMedia;
    
    protected $fillable = [
        'title',
        'content',
    ];

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('literature_media')
            ->singleFile()
            ->acceptsMimeTypes(['application/pdf', 'image/jpeg', 'image/png']);
    }

    public function questions()
    {
        return $this->hasMany(Question::class);
    }

    public static function allowedFilters(): array
    {
        return [
            'title',
            'content',
        ];
    }

    public static function allowedSorts(): array
    {
        return [
            'title',
            'created_at',
            'updated_at',
        ];
    }

    public static function allowedIncludes(): array
    {
        return [
            'questions',
        ];
    }
}