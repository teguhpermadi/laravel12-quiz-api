<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StudentUpdateRequest extends FormRequest
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
        // Dapatkan ID siswa dari route parameter
        // Jika Anda menggunakan route model binding (misal: public function update(StudentUpdateRequest $request, Student $student))
        // maka ID siswa bisa didapatkan dari $this->route('student')->id
        $studentId = $this->route('student')->id; // Asumsi route model binding {student}

        return [
            // 'name' dan 'gender' bisa 'sometimes' karena tidak semua kolom harus diupdate
            'name' => ['sometimes', 'string', 'min:3', 'max:255'],
            'gender' => ['sometimes', 'in:male,female'],
            'nisn' => [
                'sometimes', // Hanya validasi jika NISN disertakan dalam request
                'string',
                'regex:/^\d{10}$/',
                Rule::unique('students', 'nisn')->ignore($studentId, 'id'), // Abaikan ID siswa ini
            ],
            'nis' => [
                'sometimes', // Hanya validasi jika NIS disertakan dalam request
                'string',
                'regex:/^\d{8}$/',
                Rule::unique('students', 'nis')->ignore($studentId, 'id'),   // Abaikan ID siswa ini
            ],
            // Tambahkan aturan untuk kolom lain yang bisa diupdate
        ];
    }

    public function messages(): array
    {
        return [
            'name.min' => 'Nama siswa minimal 3 karakter.',
            'gender.in' => 'Jenis kelamin tidak valid.',
            'nisn.regex' => 'Format NISN tidak valid. Harus 10 digit angka.',
            'nisn.unique' => 'NISN ini sudah digunakan oleh siswa lain.',
            'nis.regex' => 'Format NIS tidak valid. Harus 8 digit angka.',
            'nis.unique' => 'NIS ini sudah digunakan oleh siswa lain.',
        ];
    }
}
