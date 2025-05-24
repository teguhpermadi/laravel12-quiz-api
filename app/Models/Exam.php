<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;

class Exam extends Model
{
    use HasFactory, HasUlids;
    
    protected $fillable = [
        'title',
        'teacher_id',
        'subject_id',
        'grade_id',
    ];

    public function teacher()
    {
        return $this->belongsTo(Teacher::class);
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    public function grade()
    {
        return $this->belongsTo(Grade::class);
    }

    public function questions()
    {
        return $this->belongsToMany(Question::class, 'exam_question')
            ->withPivot('order')
            ->orderByPivot('order');
    }

    public static function allowedIncludes(): array
    {
        return [
            'teacher',
            'subject',
            'grade',
            'questions' // Add questions to allowed includes
        ];
    }
    
    public static function allowedFilters(): array
    {
        return [
            'title',
            'teacher_id',
            'subject_id',
            'grade_id'
        ];
    }
    
    public static function allowedSorts(): array
    {
        return [
            'title',
            'created_at',
            'updated_at'
        ];
    }
}
