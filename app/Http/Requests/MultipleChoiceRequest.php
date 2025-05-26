<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MultipleChoiceRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'choices' => 'required|array|min:2',
            'choices.*.choice' => 'required|string',
            'choices.*.is_correct' => 'required|boolean'
        ];
    }
}