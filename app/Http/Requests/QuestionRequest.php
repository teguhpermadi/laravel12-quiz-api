<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class QuestionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'question' => 'required|string|max:1000',
            'question_type' => 'required|string',
            'time' => 'required|integer',
            'score' => 'required|integer',
            'teacher_id' => 'required|ulid|exists:teachers,id',
            'media' => 'sometimes|file|mimetypes:image/jpeg,image/png,video/mp4|max:10240', // 10MB max
        ];
    }
}