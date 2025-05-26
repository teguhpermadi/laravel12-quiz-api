<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ComplexMultipleChoiceRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'choices' => 'required|array|min:1',
            'choices.*.choice' => 'required|string',
            'choices.*.is_correct' => 'required|boolean',
            'choices.*.explanation' => 'nullable|string',
            'choices.*.media' => 'nullable|file|mimes:jpg,jpeg,png,gif,mp4,avi,mov|max:10240'
        ];
    }
}