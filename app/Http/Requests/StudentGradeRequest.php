<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StudentGradeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'academic_year_id' => 'required|exists:academic_years,id|string',
            'student_id' => 'required|exists:students,id|string',
            'grade_id' => 'required|exists:grades,id|string',
        ];
    }
}