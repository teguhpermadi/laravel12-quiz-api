<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GradeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'level' => 'required|integer|min:1',
            'academic_year_id' => 'required|exists:academic_years,id|string',
        ];
    }
}