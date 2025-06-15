<?php

namespace App\Exports;

use App\Models\Teacher;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class TeachersExport implements FromCollection, WithHeadings, WithMapping
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return Teacher::all();
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        // Header untuk kolom Excel
        return [
            'ID',
            'Nama',
            'Jenis Kelamin',
        ];
    }

    /**
     * @var Teacher $teacher
     */
    public function map($teacher): array
    {
        // Sesuaikan mapping data dengan kolom yang diinginkan
        return [
            $teacher->id,
            $teacher->name,
            $teacher->gender == 'male' ? 'Laki-laki' : 'Perempuan',
        ];
    }
}
