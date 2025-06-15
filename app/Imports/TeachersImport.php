<?php

namespace App\Imports;

use App\Models\Teacher;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Validators\Failure;
use Throwable;

class TeachersImport implements ToModel, WithHeadingRow, WithValidation, SkipsOnError, SkipsOnFailure
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        // Konversi nilai 'Jenis Kelamin' dari string ke 'L' atau 'P' jika diperlukan
        $gender = null;
        if (isset($row['jenis_kelamin'])) {
            $gender = strtolower($row['jenis_kelamin']);
            if ($gender === 'laki-laki' || $gender === 'l') {
                $gender = 'male';
            } elseif ($gender === 'perempuan' || $gender === 'p') {
                $gender = 'female';
            } else {
                // Tangani kasus jika nilai tidak valid, misalnya default ke L atau P, atau skip
                // Untuk demo ini, kita biarkan null jika tidak cocok, dan validasi akan menangkapnya
                $gender = null;
            }
        }


        // Pastikan nama kolom di Excel sesuai dengan key di array $row
        // Contoh: jika di Excel ada kolom 'nama_lengkap', gunakan $row['nama_lengkap']
        return new Teacher([
            'name' => $row['nama'], // Sesuaikan dengan nama kolom di Excel Anda (heading row)
            'gender' => $gender, // Sesuaikan dengan nama kolom di Excel
            // Tambahkan kolom lain yang relevan
        ]);
    }

    public function rules(): array
    {
        // Aturan validasi untuk setiap baris
        return [
            'nama' => ['required', 'string', 'max:255'],
            'jenis_kelamin' => ['required', 'in:L,P,laki-laki,perempuan,l,p,Laki-laki,Perempuan'], // Terima berbagai input untuk jenis kelamin
        ];
    }

    public function customValidationMessages(): array
    {
        return [
            'nama.required' => 'Kolom nama wajib diisi.',
            'jenis_kelamin.required' => 'Kolom jenis kelamin wajib diisi.',
            'jenis_kelamin.in' => 'Jenis kelamin harus Laki-laki/L atau Perempuan/P.',
        ];
    }

    public function onError(Throwable $error)
    {
        // Handle error per baris yang tidak menyebabkan kegagalan validasi
        // Misalnya, log error atau simpan ke array untuk laporan akhir
        // Anda bisa mengimplementasikan SkipsErrors jika ingin melewati baris error.
        // Untuk demo, kita abaikan, tapi Anda bisa menambahkan logging di sini.
        Log::error("Error importing teacher row: " . $error->getMessage());
    }

    public function onFailure(Failure ...$failures)
    {
        // Handle kegagalan validasi untuk setiap baris
        // $failures adalah array dari objek Maatwebsite\Excel\Validators\Failure
        foreach ($failures as $failure) {
            Log::warning("Validation failed for row " . $failure->row() . ": " . implode(", ", $failure->errors()));
            // Anda bisa menyimpan ini ke session atau variabel untuk ditampilkan ke user
        }
    }
}
