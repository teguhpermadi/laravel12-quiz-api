<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AcademicYearRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'year' => 'required|string|max:10',
            'semester' => 'required|string|in:odd,even',
            'teacher_id' => 'required|exists:teachers,id',
        ];
    }
}