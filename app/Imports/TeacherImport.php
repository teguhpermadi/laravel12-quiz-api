<?php

namespace App\Imports;

use App\Models\Teacher;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation; // Dipertahankan untuk validasi jenis kelamin
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithTitle; // Dipertahankan untuk mengikat ke nama sheet DataGuru
use Maatwebsite\Excel\Concerns\SkipsOnFailure; // Dipertahankan untuk mengumpulkan kegagalan validasi data
use Maatwebsite\Excel\Concerns\SkipsFailures; // Trait untuk SkipsOnFailure
use Maatwebsite\Excel\Validators\Failure;
use Illuminate\Support\Facades\Log;
use App\Exports\TeacherTemplateExport; // Untuk mengakses nama sheet DATA_SHEET_NAME
use Illuminate\Support\Str; // Import the Str facade for ulid()

class TeacherImport implements ToModel, WithHeadingRow, WithValidation, SkipsOnFailure, WithChunkReading, WithBatchInserts
{
    use SkipsFailures;

    public $importedRowCount = 0; // Menghitung baris yang berhasil diimpor (dibuat model)

    /**
     * Mendefinisikan lokasi header.
     * @return int
     */
    public function headingRow(): int
    {
        return 1; // Header ada di baris 1 di sheet DataGuru
    }

    /**
     * Mengatur judul sheet.
     * @return string
     */
    public function title(): string
    {
        return TeacherTemplateExport::DATA_SHEET_NAME; // Nama sheet yang spesifik
    }

    /**
     * Memetakan setiap baris ke model Teacher.
     * @param array $row
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        $gender = $this->mapGender($row['jenis_kelamin_lp'] ?? '');

        Log::info('TeacherImport: Membuat model Teacher untuk baris:', $row);
        // Create the Teacher instance. HasUlids will generate an ID,
        // but we'll explicitly ensure it's set before returning for batch inserts.
        $teacher = new Teacher([
            'name' => $row['nama_guru'] ?? null,
            'gender' => $gender,
        ]);

        // IMPORTANT: Manually ensure the ULID is set on the model.
        // This is a common workaround for HasUlids with Maatwebsite/Excel batch inserts,
        // as the ID might not be available in the collected attributes if only generated
        // during the creating model event which occurs later in the batch process.
        if (empty($teacher->id)) {
            $teacher->id = (string) Str::ulid(); // Generate and assign ULID
        }

        $this->importedRowCount++;

        return $teacher; // Return the model instance
    }

    /**
     * Fungsi pembantu untuk memetakan input jenis kelamin menjadi 'L' atau 'P'.
     * @param string $genderInput
     * @return string
     */
    private function mapGender(string $genderInput): string
    {
        $genderInput = strtolower(trim($genderInput));
        if (in_array($genderInput, ['l', 'laki-laki', 'L'])) {
            return 'male';
        } elseif (in_array($genderInput, ['p', 'perempuan', 'P'])) {
            return 'female';
        }
        return '';
    }

    /**
     * Mendefinisikan aturan validasi untuk setiap baris.
     * @return array
     */
    public function rules(): array
    {
        return [
            'nama_guru' => ['required', 'min:3', 'string', 'max:255'],
            'jenis_kelamin_lp' => ['required', 'string', 'in:L,P'],
        ];
    }

    /**
     * Mendefinisikan pesan validasi kustom.
     * @return array
     */
    public function customValidationMessages()
    {
        return [
            'nama_guru.required' => 'Kolom "Nama Guru" wajib diisi.', // Dihapus
            'jenis_kelamin_lp.required' => 'Kolom "Jenis Kelamin (L/P)" wajib diisi.',
            'jenis_kelamin_lp.in' => 'Kolom "Jenis Kelamin (L/P)" harus diisi L (Laki-laki) atau P (Perempuan).',
        ];
    }

    public function chunkSize(): int
    {
        return 1000;
    }

    public function batchSize(): int
    {
        return 1000;
    }

    /**
     * Mengembalikan jumlah baris yang berhasil diimpor.
     * @return int
     */
    public function getImportedRowCount(): int
    {
        return $this->importedRowCount;
    }
}
