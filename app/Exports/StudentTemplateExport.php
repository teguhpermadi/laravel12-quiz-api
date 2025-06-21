<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithMultipleSheets; // Digunakan kembali karena refactor ke 2 sheet
use Maatwebsite\Excel\Concerns\WithHeadings; // Digunakan untuk inner class sheets
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Style\Protection;
use PhpOffice\PhpSpreadsheet\Cell\DataValidation;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet; // Untuk menyembunyikan sheet
use PhpOffice\PhpSpreadsheet\Cell\Coordinate; // Untuk Coordinate::stringFromColumnIndex

class StudentTemplateExport implements WithMultipleSheets, ShouldAutoSize
{
    // --- KONSTANTA INI HARUS ADA DI SINI ---
    const TEMPLATE_CODE = 'STUDENT_IMPORT_TEMPLATE_V1.0_20250615'; // Kode unik template
    const TEMPLATE_INFO_SHEET_NAME = 'TemplateInfo'; // Nama sheet info tersembunyi
    const DATA_SHEET_NAME = 'DataSiswa'; // Nama sheet data siswa

    const SHEET_PASSWORD = 'quizmaster'; // Password proteksi sheet

    // Konstanta untuk lokasi kode/info di sheet TemplateInfo
    const TEMPLATE_CODE_CELL_PREFIX = 'B'; // Kolom tempat data info dimulai di TemplateInfo
    const TEMPLATE_INFO_START_ROW = 2; // Baris awal data info di TemplateInfo

    // Konstanta untuk lokasi header dan data di sheet DataGuru
    const DATA_HEADER_ROW = 1; // Baris tempat header data guru berada di DataGuru sheet
    const DATA_CONTENT_START_ROW = 2; // Baris tempat data guru bisa diisi pengguna di DataGuru sheet

    /**
     * @return array
     */
    public function sheets(): array
    {
        $sheets = [];

        // --- SHEET PERTAMA: TemplateInfo (Tersembunyi) ---
        $sheets[] = new class implements FromCollection, WithHeadings, WithEvents, WithTitle, ShouldAutoSize {
            public function collection()
            {
                // Data untuk sheet informasi template
                return new Collection([
                    ['template_code', StudentTemplateExport::TEMPLATE_CODE],
                    ['version', '1.0'],
                    ['created_at', now()->toDateTimeString()],
                ]);
            }

            public function headings(): array
            {
                // Header untuk sheet informasi template
                return ['Key', 'Value'];
            }

            public function title(): string
            {
                // Nama sheet informasi template
                return StudentTemplateExport::TEMPLATE_INFO_SHEET_NAME;
            }

            public function registerEvents(): array
            {
                return [
                    AfterSheet::class => function (AfterSheet $event) {
                        $sheet = $event->sheet->getDelegate();
                        // Sembunyikan sheet ini agar tidak terlihat oleh pengguna
                        $sheet->setSheetState(Worksheet::SHEETSTATE_HIDDEN);
                        // Atur lebar kolom otomatis
                        foreach (range('A', 'B') as $col) { // Asumsi 2 kolom (Key, Value)
                            $sheet->getColumnDimension($col)->setAutoSize(true);
                        }
                    },
                ];
            }
        };

        // --- SHEET KEDUA: DataSiswa (Template Data Siswa) ---
        $sheets[] = new class implements FromCollection, WithHeadings, WithEvents, WithTitle, ShouldAutoSize {
            public function collection()
            {
                // Mengembalikan koleksi kosong karena ini adalah template untuk diisi
                return new Collection([]);
            }

            public function headings(): array
            {
                // Header untuk kolom data siswa. Ini akan muncul di baris 1.
                return [
                    'Nama Siswa',           // Kolom A
                    'Jenis Kelamin (L/P)',  // Kolom B
                    // Kolom Email dihapus sesuai permintaan sebelumnya
                ];
            }

            public function title(): string
            {
                // Nama sheet data siswa utama
                return StudentTemplateExport::DATA_SHEET_NAME;
            }

            public function registerEvents(): array
            {
                return [
                    AfterSheet::class => function (AfterSheet $event) {
                        $sheet = $event->sheet->getDelegate();

                        // --- PROTEKSI SHEET ---
                        // Lindungi Sheet (Defaultnya, semua sel akan terkunci jika proteksi diaktifkan)
                        $sheet->getProtection()->setSheet(true); // Aktifkan proteksi sheet
                        $sheet->getProtection()->setPassword(StudentTemplateExport::SHEET_PASSWORD); // Set password
                        $sheet->getProtection()->setFormatCells(true);
                        $sheet->getProtection()->setFormatColumns(true);
                        $sheet->getProtection()->setFormatRows(true);
                        $sheet->getProtection()->setInsertColumns(true);
                        $sheet->getProtection()->setInsertRows(true);
                        $sheet->getProtection()->setDeleteColumns(true);
                        $sheet->getProtection()->setDeleteRows(true);

                        // --- BUKA KUNCI AREA DATA SISWA ---
                        // Data siswa bisa diisi mulai dari DATA_CONTENT_START_ROW (baris 2)
                        $lastColumnIndex = count($this->headings()); // Jumlah kolom header
                        $lastColumnLetter = Coordinate::stringFromColumnIndex($lastColumnIndex);
                        $editableDataRange = 'A' . StudentTemplateExport::DATA_CONTENT_START_ROW . ':' . $lastColumnLetter . '1000'; // Dari A2 sampai baris 1000
                        $sheet->getStyle($editableDataRange)->getProtection()->setLocked(Protection::PROTECTION_UNPROTECTED);

                        // --- VALIDASI DATA (Dropdown untuk Jenis Kelamin) ---
                        $genderColumnLetter = 'B'; // Kolom untuk 'Jenis Kelamin (L/P)'
                        $genderValidationStartRow = StudentTemplateExport::DATA_CONTENT_START_ROW; // Mulai dari baris 2
                        $genderValidationRange = $genderColumnLetter . $genderValidationStartRow . ':' . $genderColumnLetter . '1000';

                        $objValidation = $sheet->getCell($genderColumnLetter . $genderValidationStartRow)->getDataValidation();
                        $objValidation->setType(DataValidation::TYPE_LIST);
                        $objValidation->setErrorStyle(DataValidation::STYLE_INFORMATION);
                        $objValidation->setAllowBlank(false);
                        $objValidation->setShowInputMessage(true);
                        $objValidation->setShowErrorMessage(true);
                        $objValidation->setShowDropDown(true);
                        $objValidation->setErrorTitle('Input Error');
                        $objValidation->setError('Pilih dari daftar yang tersedia: L atau P.');
                        $objValidation->setPromptTitle('Jenis Kelamin');
                        $objValidation->setPrompt('Pilih L (Laki-laki) atau P (Perempuan)');
                        $objValidation->setFormula1('"L,P"');

                        // Terapkan validasi ke seluruh rentang kolom Jenis Kelamin yang bisa diedit
                        for ($row = $genderValidationStartRow; $row <= 1000; $row++) {
                            $sheet->getCell($genderColumnLetter . $row)->setDataValidation(clone $objValidation);
                        }

                        // --- PEMFORMATAN HEADER ---
                        // Gaya untuk header di baris DATA_HEADER_ROW (baris 1)
                        $headerStyleRange = 'A' . StudentTemplateExport::DATA_HEADER_ROW . ':' . $lastColumnLetter . StudentTemplateExport::DATA_HEADER_ROW;
                        $sheet->getStyle($headerStyleRange)->applyFromArray([
                            'font' => [
                                'bold' => true,
                                'color' => ['argb' => Color::COLOR_WHITE], // Teks putih
                            ],
                            'fill' => [
                                'fillType' => Fill::FILL_SOLID,
                                'rotation' => 90,
                                'startColor' => [
                                    'argb' => Color::COLOR_DARKBLUE, // Warna latar belakang biru tua
                                ],
                            ],
                            'borders' => [
                                'allBorders' => [
                                    'borderStyle' => Border::BORDER_THIN,
                                    'color' => ['argb' => Color::COLOR_BLACK],
                                ],
                            ],
                        ]);

                        // Atur lebar kolom otomatis
                        foreach (range('A', $lastColumnLetter) as $col) {
                            $sheet->getColumnDimension($col)->setAutoSize(true);
                        }
                    },
                ];
            }
        };

        return $sheets;
    }
}
