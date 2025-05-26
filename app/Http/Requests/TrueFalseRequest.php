<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TrueFalseRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'is_true' => 'required|boolean'
        ];
    }
}