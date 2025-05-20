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
    ];

    protected $casts = [
        'question_type' => QuestionTypeEnum::class,
        'time' => TimeEnum::class,
        'score' => ScoreEnum::class,
    ];

    public function teacher()
    {
        return $this->belongsTo(Teacher::class);
    }
}
