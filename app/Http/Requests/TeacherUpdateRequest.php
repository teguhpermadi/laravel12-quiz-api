<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class TeacherUpdateRequest extends FormRequest
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
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $teacherId = $this->route('teacher')->id; // Asumsi route model binding {teacher}
        
        return [
            'name' => ['sometimes', 'string', 'min:3', 'max:255'],
            'gender' => ['sometimes', 'in:male,female'], // Menggunakan 'male' dan 'female'
            'nip' => [
                'sometimes',
                'string',
                Rule::unique('teachers', 'nip')->ignore($teacherId, 'id'),
            ],
        ];
    }
    
    public function messages(): array
    {
        return [
            'name.min' => 'Nama guru minimal 3 karakter.',
            'gender.in' => 'Jenis kelamin tidak valid. Harus "male" atau "female".',
            'nip.unique' => 'NIP ini sudah digunakan oleh guru lain.', // Jika ada NIP
        ];
    }
}
