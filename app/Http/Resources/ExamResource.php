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
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at
        ];
    }
}