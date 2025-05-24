<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MultipleChoice extends Model
{
    /** @use HasFactory<\Database\Factories\MultipleChoiceFactory> */
    use HasFactory, HasUlids;

    protected $fillable = [
        'question_id',
        'choice',
        'is_correct',
    ];
    
    public function question()
    {
        return $this->belongsTo(Question::class);
    }
}
