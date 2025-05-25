<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Answerable extends Model
{
    use HasFactory, HasUlids;
    
    protected $fillable = [
        'question_id',
        'answerable_id',
        'answerable_type',
    ];

    public function question()
    {
        return $this->belongsTo(Question::class);
    }

    public function answerable()
    {
        return $this->morphTo();
    }
}
