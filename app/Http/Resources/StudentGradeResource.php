<?php

namespace App\Http\Resources;

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
                return [
                    'id' => $this->academicYear->id,
                    'year' => $this->academicYear->year,
                    'semester' => $this->academicYear->semester,
                ];
            }),
            'grade' => $this->whenLoaded('grade', function() {
                return [
                    'id' => $this->grade->id,
                    'name' => $this->grade->name,
                    'level' => $this->grade->level,
                ];
            }),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at
        ];
    }
}