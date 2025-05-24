<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ExamResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'teacher_id' => $this->teacher_id,
            'teacher' => $this->whenLoaded('teacher', new TeacherResource($this->teacher)),
            'subject_id' => $this->subject_id,
            'subject' => $this->whenLoaded('subject', new SubjectResource($this->subject)),
            'grade_id' => $this->grade_id,
            'grade' => $this->whenLoaded('grade'),
            'questions_count' => $this->when(isset($this->questions_count), $this->questions_count),
            'questions' => $this->whenLoaded('questions', function () {
                return QuestionResource::collection(
                    $this->questions->map(function($question) {
                        $question->order = $question->pivot->order;
                        return $question;
                    })->sortBy('order')
                );
            }),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at
        ];
    }
}