<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class TrueFalse extends Model implements HasMedia
{
    /** @use HasFactory<\Database\Factories\TrueFalseFactory> */
    use HasFactory, HasUlids, InteractsWithMedia;

    protected $fillable = [
        'choice',
        'is_correct',
    ];

    public function questions()
    {
        return $this->morphToMany(Question::class, 'answerable', 'answerables');
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('answer_media')
            ->acceptsMimeTypes([
                'image/jpeg',
                'image/png',
                'image/gif',
                'audio/mpeg',
                'audio/ogg',
                'audio/wav'
            ])
            ->singleFile();
    }
}
