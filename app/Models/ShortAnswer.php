<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShortAnswer extends Model
{
    /** @use HasFactory<\Database\Factories\ShortAnswerFactory> */
    use HasFactory, HasUlids;

    protected $fillable = [
        'answer',
        'correction_with_ai',
        'prompt',
    ];
}
