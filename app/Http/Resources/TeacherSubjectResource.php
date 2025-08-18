<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TeacherSubjectResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'teacher_id' => $this->teacher_id,
            'subject_id' => $this->subject_id,
            'grade_id' => $this->grade_id,
            'teacher' => $this->whenLoaded('teacher', function() {
                return new TeacherResource($this->teacher);
            }),
            'subject' => $this->whenLoaded('subject', function() {
                return new SubjectResource($this->subject);
            }),
            'grade' => $this->whenLoaded('grade', function() {
                return new GradeResource($this->grade);
            }),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at
        ];
    }
}