<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EssayAnswer extends Model
{
    /** @use HasFactory<\Database\Factories\EssayAnswerFactory> */
    use HasFactory, HasUlids;

    protected $fillable = [
        'answer',
        'correction_with_ai',
        'prompt',
    ];

    public function questions()
    {
        return $this->morphToMany(Question::class, 'answerable', 'answerables');
    }
}
