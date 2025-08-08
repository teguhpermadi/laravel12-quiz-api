<?php

namespace App\Http\Resources;

use App\Models\Grade;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StudentGradeResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'academic_year_id' => $this->academic_year_id,
            'student_id' => $this->student_id,
            'grade_id' => $this->grade_id,
            'academic_year' => $this->whenLoaded('academicYear', function() {
                return AcademicYearResource::make($this->academicYear);
            }),
            'student' => $this->whenLoaded('student', function() {
                return StudentResource::make($this->student);
            }),
            'grade' => $this->whenLoaded('grade', function() {
                return GradeResource::make($this->grade);
            }),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at
        ];
    }
}