<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StudentCreateRequest extends FormRequest
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
        return [
            'name' => ['required', 'string', 'min:3', 'max:255'],
            'gender' => ['required', 'in:male,female'], // 'L'/'P' atau 'male'/'female'
            'nisn' => ['required', 'string', 'regex:/^\d{10}$/', 'unique:students,nisn'], // NISN wajib dan unik
            'nis' => ['required', 'string', 'regex:/^\d{8}$/', 'unique:students,nis'],   // NIS wajib dan unik
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Nama siswa wajib diisi.',
            'name.min' => 'Nama siswa minimal 3 karakter.',
            'gender.required' => 'Jenis kelamin wajib diisi.',
            'gender.in' => 'Jenis kelamin tidak valid.',
            'nisn.required' => 'NISN wajib diisi.',
            'nisn.regex' => 'Format NISN tidak valid. Harus 10 digit angka.',
            'nisn.unique' => 'NISN ini sudah digunakan oleh siswa lain.',
            'nis.required' => 'NIS wajib diisi.',
            'nis.regex' => 'Format NIS tidak valid. Harus 8 digit angka.',
            'nis.unique' => 'NIS ini sudah digunakan oleh siswa lain.',
        ];
    }
}
