<?php

namespace App\Models;

use App\Enums\QuestionTypeEnum;
use App\Enums\TimeEnum;
use App\Enums\ScoreEnum;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Question extends Model implements HasMedia
{
    use HasFactory, HasUlids, InteractsWithMedia;
    
    protected $fillable = [
        'question',
        'question_type', 
        'teacher_id',
        'time',
        'score',
        'literature_id', // Menambahkan literature_id ke fillable
    ];

    protected $casts = [
        'question_type' => QuestionTypeEnum::class,
        'time' => TimeEnum::class,
        'score' => ScoreEnum::class,
    ];

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('question_media')
            ->singleFile()
            ->acceptsMimeTypes(['image/jpeg', 'image/png', 'video/mp4']);
    }

    public function teacher()
    {
        return $this->belongsTo(Teacher::class);
    }

    // Menambahkan relasi dengan Literature
    public function literature()
    {
        return $this->belongsTo(Literature::class);
    }

    public static function allowedFilters(): array
    {
        return [
            'question',
            'question_type',
            'time',
            'score',
            'teacher_id',
            'literature_id', // Menambahkan literature_id ke allowed filters
        ];
    }

    public static function allowedSorts(): array
    {
        return [
            'question',
            'question_type',
            'time',
            'score',
            'teacher_id',
            'literature_id', // Menambahkan literature_id ke allowed sorts
            'created_at',
            'updated_at',
        ];
    }

    public static function allowedIncludes(): array
    {
        return [
            'teacher',
            'literature', // Menambahkan literature ke allowed includes
        ];
    }

    public function answer()
    {
        return $this->morphTo('answerable');
    }
}
