<?php

namespace App\Exports;

use App\Models\Student;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class StudentsExport implements FromCollection, WithHeadings, WithMapping
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return Student::all();
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
     * @var Student $student
     */
    public function map($student): array
    {
        // Sesuaikan mapping data dengan kolom yang diinginkan
        return [
            $student->id,
            $student->name,
            $student->gender == 'male' ? 'Laki-laki' : 'Perempuan',
        ];
    }
}
