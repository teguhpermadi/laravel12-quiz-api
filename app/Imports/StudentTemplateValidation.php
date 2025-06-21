<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithLimit;
use Maatwebsite\Excel\Concerns\WithTitle;
use App\Exports\StudentTemplateExport;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\ToCollection;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use Maatwebsite\Excel\Concerns\WithEvents;

class StudentTemplateValidation implements WithMultipleSheets
{
    public $isValid = false;
    public $validationMessage = '';

    public $templateCodeValidated = false;
    public $templateCodeMatches = false;
    public $dataHeadersValidated = false;
    public $dataHeadersMatch = false;

    /**
     * Mendefinisikan sheet-sheet yang akan divalidasi dan logikanya.
     * @return array
     */
    public function sheets(): array
    {
        $sheets = [];

        // --- SHEET 1: TemplateInfo (Validasi Kode Rahasia) ---
        $sheets[] = new class($this) implements ToCollection, WithHeadingRow, WithLimit, WithTitle
        {
            private $parent;

            public function __construct(StudentTemplateValidation $parent)
            {
                $this->parent = $parent;
            }

            public function collection(Collection $collection)
            {
                $this->parent->templateCodeValidated = true;
                // Log::info('TemplateValidation (TemplateInfoSheet): Collection diterima:', $collection->toArray());

                $expectedTemplateCode = StudentTemplateExport::TEMPLATE_CODE;
                $currentTemplateCode = null;

                if ($collection->isNotEmpty()) {
                    $templateCodeRow = $collection->firstWhere('key', 'template_code');

                    if ($templateCodeRow && isset($templateCodeRow['value'])) {
                        $currentTemplateCode = $templateCodeRow['value'];
                        if ($currentTemplateCode === $expectedTemplateCode) {
                            $this->parent->templateCodeMatches = true;
                            Log::info('TemplateValidation (TemplateInfoSheet): Kode template COCOK.');
                        } else {
                            $this->parent->validationMessage = 'Kode identifikasi template tidak sesuai. Silakan unduh template terbaru.';
                            Log::warning('TemplateValidation (TemplateInfoSheet): Kode template TIDAK COCOK.');
                        }
                    } else {
                        $this->parent->validationMessage = 'Tidak dapat menemukan kode identifikasi template dalam sheet informasi.';
                        Log::warning('TemplateValidation (TemplateInfoSheet): Baris "template_code" tidak ditemukan atau kolom "value" tidak ada.');
                    }
                } else {
                    $this->parent->validationMessage = 'Sheet informasi template kosong atau tidak valid.';
                    Log::warning('TemplateValidation (TemplateInfoSheet): Koleksi kosong.');
                }
            }

            public function limit(): int { return 5; }
            public function headingRow(): int { return 1; }
            public function title(): string { return StudentTemplateExport::TEMPLATE_INFO_SHEET_NAME; }
        };

        // --- SHEET 2: DataSiswa (Validasi Header Kolom) ---
        $sheets[] = new class($this) implements WithEvents, WithTitle, WithHeadingRow
        {
            private $parent;

            public function __construct(StudentTemplateValidation $parent)
            {
                $this->parent = $parent;
            }
            
            public function registerEvents(): array
            {
                return [
                    \Maatwebsite\Excel\Events\BeforeSheet::class => function (\Maatwebsite\Excel\Events\BeforeSheet $event) {
                        $sheet = $event->sheet->getDelegate();
                        $this->parent->dataHeadersValidated = true;

                        $expectedHeaders = [
                            'Nama Siswa',
                            'Jenis Kelamin (L/P)',
                        ];

                        $lastColIndexExpected = count($expectedHeaders); 
                        $lastColLetterExpected = Coordinate::stringFromColumnIndex($lastColIndexExpected);

                        $actualHeadersInSheet = $sheet->rangeToArray('A1:' . $lastColLetterExpected . '1', null, true, false, false);
                        
                        // Log::info('TemplateValidation (DataSiswaSheet): Header yang dibaca dari A1:', $actualHeadersInSheet);

                        if (!empty($actualHeadersInSheet) && !empty($actualHeadersInSheet[0])) {
                            $foundHeaders = array_values($actualHeadersInSheet[0]);
                            
                            if ($foundHeaders === $expectedHeaders) {
                                $this->parent->dataHeadersMatch = true;
                                Log::info('TemplateValidation (DataSiswaSheet): Header data COCOK.');
                            } else {
                                $this->parent->validationMessage = 'Struktur header sheet DataSiswa tidak sesuai dengan template resmi.';
                                Log::warning('TemplateValidation (DataSiswaSheet): Header data TIDAK COCOK.', ['found' => $foundHeaders, 'expected' => $expectedHeaders]);
                            }
                        } else {
                            $this->parent->validationMessage = 'Sheet DataSiswa kosong atau tidak memiliki header yang valid.';
                            Log::warning('TemplateValidation (DataSiswaSheet): Sheet DataSiswa kosong atau header tidak ditemukan.');
                        }
                    },
                ];
            }

            public function collection(Collection $collection)
            {
                Log::info('TemplateValidation (DataSiswaSheet): Data baris diterima (setelah header validasi):', $collection->toArray());
            }

            public function headingRow(): int { return 1; }
            public function title(): string { return StudentTemplateExport::DATA_SHEET_NAME; }
        };

        return $sheets;
    }

    /**
     * @return bool
     */
    public function isValid(): bool
    {
        return $this->templateCodeValidated && $this->templateCodeMatches &&
               $this->dataHeadersValidated && $this->dataHeadersMatch;
    }

    /**
     * @return string
     */
    public function getValidationMessage(): string
    {
        return $this->validationMessage;
    }
}
