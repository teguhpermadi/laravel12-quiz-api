<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class GradeResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'level' => $this->level,
            'student_count' => $this->when(isset($this->student_count), $this->student_count),
            'subject_count' => $this->when(isset($this->subjects_count), $this->subjects_count),
            'academic_year' => $this->whenLoaded('academicYear', function() {
                return new AcademicYearResource($this->academicYear);
            }),
            'students' => $this->when($this->relationLoaded('students'), function() {
                return StudentGradeResource::collection($this->students);
            }),
            'subjects' => $this->when($this->relationLoaded('subjects'), function() {
                return TeacherSubjectResource::collection($this->subjects);
            }),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at
        ];
    }
}