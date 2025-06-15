<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithHeadings;

class TeacherTemplateExport implements WithHeadings
{
    /**
     * Define the headings for the Excel template.
     * These should match the expected columns in your TeacherImport class.
     *
     * @return array
     */
    public function headings(): array
    {
        // Sesuaikan dengan nama kolom yang Anda harapkan di file Excel untuk impor.
        // Ini harus cocok dengan kunci yang Anda gunakan di TeachersImport (misalnya, 'nama', 'jenis_kelamin', 'email').
        return [
            'Nama',
            'Jenis Kelamin', // Contoh: Laki-laki/L atau Perempuan/P
            // Tambahkan semua kolom lain yang relevan yang ingin Anda masukkan dalam template
            // Contoh: 'Nomor Telepon', 'Alamat', dll.
        ];
    }
}
