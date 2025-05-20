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
            'academic_year_id' => $this->academic_year_id,
            'teacher_id' => $this->teacher_id,
            'subject_id' => $this->subject_id,
            'academic_year' => $this->whenLoaded('academicYear', function() {
                return new AcademicYearResource($this->academicYear);
            }),
            'teacher' => $this->whenLoaded('teacher', function() {
                return new TeacherResource($this->teacher);
            }),
            'subject' => $this->whenLoaded('subject', function() {
                return new SubjectResource($this->subject);
            }),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at
        ];
    }
}