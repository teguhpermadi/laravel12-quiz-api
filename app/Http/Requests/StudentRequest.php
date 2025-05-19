<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StudentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'gender' => 'required|string|in:male,female',
            'nisn' => 'required|string|max:20|unique:students,nisn,' . $this->student,
            'nis' => 'required|string|max:20|unique:students,nis,' . $this->student,
        ];
    }
}