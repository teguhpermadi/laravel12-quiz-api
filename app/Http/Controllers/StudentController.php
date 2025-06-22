<?php

namespace App\Http\Controllers;

use App\Events\NewStudentAdded;
use App\Events\StudentCreated;
use App\Events\StudentDeleted;
use App\Events\StudentUpdated;
use App\Exports\StudentsExport;
use App\Exports\StudentTemplateExport;
use App\Http\Requests\StudentRequest;
use App\Http\Resources\StudentResource;
use App\Imports\StudentImport;
use App\Imports\StudentTemplateValidation;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use Spatie\QueryBuilder\QueryBuilder;
use Illuminate\Validation\ValidationException;

class StudentController extends Controller
{
    /**
     * Menampilkan daftar semua siswa dengan filter, sorting, dan pagination
     */
    public function index(Request $request)
    {
        $academicYearId = $request->input('academic_year_id');
        
        $query = QueryBuilder::for(Student::class)
            ->allowedFilters(Student::allowedFilters())
            ->allowedSorts(Student::allowedSorts())
            ->allowedIncludes(Student::allowedIncludes());
            
        // Jika ada parameter academic_year_id, load grades berdasarkan tahun akademik
        if ($academicYearId) {
            $query->with(['grades' => function($query) use ($academicYearId) {
                $query->where('academic_year_id', $academicYearId)
                      ->with(['grade', 'academicYear']);
            }]);
        }
        
        $students = $query->paginate($request->input('per_page', 15))
            ->appends($request->query());
        
        return response()->json([
            'status' => 'success',
            'data' => StudentResource::collection($students),
            'meta' => [
                'current_page' => $students->currentPage(),
                'from' => $students->firstItem(),
                'last_page' => $students->lastPage(),
                'per_page' => $students->perPage(),
                'to' => $students->lastItem(),
                'total' => $students->total(),
            ],
            'links' => [
                'first' => $students->url(1),
                'last' => $students->url($students->lastPage()),
                'prev' => $students->previousPageUrl(),
                'next' => $students->nextPageUrl(),
            ],
        ]);
    }

    /**
     * Menyimpan siswa baru
     */
    public function store(StudentRequest $request)
    {
        $student = Student::create($request->validated());

        return response()->json([
            'status' => 'success',
            'message' => 'Student added successfully',
            'data' => new StudentResource($student)
        ], 201);
    }

    /**
     * Menampilkan detail siswa
     */
    public function show(Request $request, Student $student)
    {
        $academicYearId = $request->input('academic_year_id');
        
        // Jika ada parameter academic_year_id, load grades berdasarkan tahun akademik
        if ($academicYearId) {
            $student->load(['grades' => function($query) use ($academicYearId) {
                $query->where('academic_year_id', $academicYearId)
                      ->with(['grade', 'academicYear']);
            }]);
        }
        
        return response()->json([
            'status' => 'success',
            'data' => new StudentResource($student)
        ]);
    }

    /**
     * Memperbarui data siswa
     */
    public function update(StudentRequest $request, Student $student)
    {
        $student->update($request->validated());
        
        return response()->json([
            'status' => 'success',
            'message' => 'Student data updated successfully',
            'data' => new StudentResource($student)
        ]);
    }

    /**
     * Menghapus data siswa
     */
    public function destroy(Student $student)
    {
        $student->delete();
        
        return response()->json([
            'status' => 'success',
            'message' => 'Student data deleted successfully'
        ]);
    }

    
    /**
     * Menghapus data siswa dalam jumlah banyak
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function bulkDelete(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'string|exists:students,id', // Validasi setiap ID
        ]);

        Student::whereIn('id', $request->ids)->delete();

        return response()->json(['message' => 'Students deleted successfully'], 200);
    }

    /**
     * Download an Excel file containing all students data.
     *
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse|\Illuminate\Http\JsonResponse
     */
    public function export()
    {
        try {
            return Excel::download(new StudentsExport, 'students.xlsx');
        } catch (\Exception $e) {
            return response()->json(['message' => 'Gagal mengekspor data siswa: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Download a blank Excel template for teacher import.
     *
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse|\Illuminate\Http\JsonResponse
     */
    public function downloadTemplate()
    {
        try {
            return Excel::download(new StudentTemplateExport, 'student_import_template.xlsx');
        } catch (\Exception $e) {
            // Log the error for debugging
            Log::error('Failed to download student import template: ' . $e->getMessage());
            return response()->json(['message' => 'Gagal mengunduh template: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Mengimpor data siswa dari file Excel.
     * Memisahkan validasi template dan impor data.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function import(Request $request)
    {
        // Validasi dasar untuk file yang diunggah
        $request->validate([
            'file' => ['required', 'file', 'mimes:xls,xlsx', 'max:10240'], // Max 10MB
        ]);

        try {
            // --- FASE 1: VALIDASI TEMPLATE ---
            $templateValidator = new StudentTemplateValidation();

            // PENTING: Panggil import untuk StudentTemplateValidation.
            // Karena StudentTemplateValidation mengimplementasikan WithMultipleSheets,
            // ia akan secara otomatis memproses semua sheets yang didefinisikannya (TemplateInfo & DataSiswa).
            Excel::import($templateValidator, $request->file('file')); // Tidak perlu selected_sheets jika WithMultipleSheets
            
            // Periksa hasil validasi template
            if (!$templateValidator->isValid()) {
                // Jika template tidak valid, kembalikan pesan error template
                // HANYA mengandalkan getValidationMessage()
                return response()->json([
                    'message' => $templateValidator->getValidationMessage() ?: 'Impor gagal: File bukan template resmi atau ada masalah validasi.',
                    // Array 'errors' dihilangkan karena StudentTemplateValidation tidak lagi mengumpulkan kegagalan detail
                ], 422);
            }

            // --- FASE 2: IMPOR DATA SISWA (HANYA JIKA TEMPLATE VALID) ---
            $dataImporter = new StudentImport();

            // PENTING: Panggil import untuk StudentImport.
            // Karena StudentImport mengimplementasikan WithTitle, ia akan secara otomatis
            // mencari dan memproses sheet DataSiswa (sesuai dengan title() methodnya).
            Excel::import($dataImporter, $request->file('file'), null, \Maatwebsite\Excel\Excel::XLSX, [
                'selected_sheets' => [StudentTemplateExport::DATA_SHEET_NAME], // Pastikan hanya DataSiswa sheet yang diimpor
                'heading_row' => 1, // Heading untuk DataSiswa ada di baris 1
            ]);

            // Jika semua berhasil
            return response()->json([
                'message' => 'Data siswa berhasil diimpor! Total ' . $dataImporter->getImportedRowCount() . ' baris data berhasil.',
                'imported_count' => $dataImporter->getImportedRowCount(),
            ], 200);

        } catch (ValidationException $e) {
            // Tangani error validasi dari $request->validate() di awal (misal: file bukan excel, ukuran)
            return response()->json([
                'message' => $e->getMessage() ?: 'Kesalahan validasi file yang diunggah.',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            // Tangani semua error lain yang tidak spesifik (misal: masalah baca file, koneksi DB)
            Log::error('Error processing Excel file: ' . $e->getMessage(), ['exception' => $e, 'file' => $request->file('file')->getClientOriginalName()]);
            return response()->json(['message' => 'Gagal memproses file Excel: ' . $e->getMessage()], 500);
        }
    }
}