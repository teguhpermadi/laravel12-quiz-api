<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LiteratureRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'source' => 'required|string|max:255',
            'author' => 'required|string|max:255',
            'media' => 'sometimes|file|mimetypes:application/pdf,image/jpeg,image/png|max:10240', // 10MB max
        ];
    }
}