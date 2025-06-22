<?php

namespace App\Http\Controllers;

use App\Events\TeacherCreated;
use App\Events\TeacherDeleted;
use App\Events\TeacherUpdated;
use App\Exports\TeachersExport;
use App\Exports\TeacherTemplateExport;
use App\Http\Requests\TeacherRequest;
use App\Http\Resources\TeacherResource;
use App\Imports\TeacherImport;
use App\Imports\TeacherTemplateValidation;
use App\Models\Teacher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use Spatie\QueryBuilder\QueryBuilder;
use Illuminate\Validation\ValidationException;

class TeacherController extends Controller
{
    /**
     * Menampilkan daftar semua guru dengan filter, sorting, dan pagination
     */
    public function index(Request $request)
    {
        $academicYearId = $request->input('academic_year_id');

        $query = QueryBuilder::for(Teacher::class)
            ->allowedFilters(Teacher::allowedFilters())
            ->allowedSorts(Teacher::allowedSorts())
            ->allowedIncludes(Teacher::allowedIncludes());

        // Jika ada parameter academic_year_id, load subjects berdasarkan tahun akademik
        if ($academicYearId) {
            $query->withCount(['subjects as subject_count' => function ($query) use ($academicYearId) {
                $query->where('academic_year_id', $academicYearId);
            }]);

            $query->with(['subjects' => function ($query) use ($academicYearId) {
                $query->where('academic_year_id', $academicYearId)
                    ->with(['subject', 'academicYear']);
            }]);
        } else {
            // Jika tidak ada parameter academic_year_id, hitung semua subject
            $query->withCount('subjects as subject_count');
        }

        $teachers = $query->paginate($request->input('per_page', 15))
            ->appends($request->query());

        return response()->json([
            'status' => 'success',
            'data' => TeacherResource::collection($teachers),
            'meta' => [
                'current_page' => $teachers->currentPage(),
                'from' => $teachers->firstItem(),
                'last_page' => $teachers->lastPage(),
                'per_page' => $teachers->perPage(),
                'to' => $teachers->lastItem(),
                'total' => $teachers->total(),
            ],
            'links' => [
                'first' => $teachers->url(1),
                'last' => $teachers->url($teachers->lastPage()),
                'prev' => $teachers->previousPageUrl(),
                'next' => $teachers->nextPageUrl(),
            ],
        ]);
    }

    /**
     * Menyimpan guru baru
     */
    public function store(TeacherRequest $request)
    {
        $teacher = Teacher::create($request->validated());

        // Broadcast event after teacher is created
        TeacherCreated::dispatch($teacher);

        return response()->json([
            'status' => 'success',
            'message' => 'Teacher added successfully',
            'data' => new TeacherResource($teacher)
        ], 201);
    }

    /**
     * Menampilkan detail guru
     */
    public function show(Request $request, Teacher $teacher)
    {
        $academicYearId = $request->input('academic_year_id');

        // Jika ada parameter academic_year_id, load subjects berdasarkan tahun akademik
        if ($academicYearId) {
            $teacher->loadCount(['subjects as subject_count' => function ($query) use ($academicYearId) {
                $query->where('academic_year_id', $academicYearId);
            }]);

            $teacher->load(['subjects' => function ($query) use ($academicYearId) {
                $query->where('academic_year_id', $academicYearId)
                    ->with(['subject', 'academicYear']);
            }]);
        } else {
            // Jika tidak ada parameter academic_year_id, hitung semua subject
            $teacher->loadCount('subjects as subject_count');
        }

        return response()->json([
            'status' => 'success',
            'data' => new TeacherResource($teacher)
        ]);
    }

    /**
     * Memperbarui data guru
     */
    public function update(TeacherRequest $request, Teacher $teacher)
    {
        $teacher->update($request->validated());

        // Broadcast event after teacher is updated
        TeacherUpdated::dispatch($teacher);

        return response()->json([
            'status' => 'success',
            'message' => 'Teacher data updated successfully',
            'data' => new TeacherResource($teacher)
        ]);
    }

    /**
     * Menghapus data guru
     */
    public function destroy(Teacher $teacher)
    {
        $teacher->delete();

        // Broadcast event after teacher is deleted
        TeacherDeleted::dispatch($teacher);

        return response()->json([
            'status' => 'success',
            'message' => 'Teacher data deleted successfully'
        ]);
    }

    
    /**
     * Menghapus data guru dalam jumlah banyak
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function bulkDelete(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'string|exists:teachers,id', // Validasi setiap ID
        ]);

        Teacher::whereIn('id', $request->ids)->delete();

        return response()->json(['message' => 'Teachers deleted successfully'], 200);
    }

    /**
     * Download an Excel file containing all teachers data.
     *
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse|\Illuminate\Http\JsonResponse
     */
    public function export()
    {
        try {
            return Excel::download(new TeachersExport, 'teachers.xlsx');
        } catch (\Exception $e) {
            return response()->json(['message' => 'Gagal mengekspor data guru: ' . $e->getMessage()], 500);
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
            return Excel::download(new TeacherTemplateExport, 'teacher_import_template.xlsx');
        } catch (\Exception $e) {
            // Log the error for debugging
            Log::error('Failed to download teacher import template: ' . $e->getMessage());
            return response()->json(['message' => 'Gagal mengunduh template: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Mengimpor data guru dari file Excel.
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
            $templateValidator = new TeacherTemplateValidation();
            
            // PENTING: Panggil import untuk TeacherTemplateValidation.
            // Karena TeacherTemplateValidation mengimplementasikan WithMultipleSheets,
            // ia akan secara otomatis memproses semua sheets yang didefinisikannya (TemplateInfo & DataGuru).
            Excel::import($templateValidator, $request->file('file')); // Tidak perlu selected_sheets jika WithMultipleSheets
            
            // Periksa hasil validasi template
            if (!$templateValidator->isValid()) {
                // Jika template tidak valid, kembalikan pesan error template
                // HANYA mengandalkan getValidationMessage()
                return response()->json([
                    'message' => $templateValidator->getValidationMessage() ?: 'Impor gagal: File bukan template resmi atau ada masalah validasi.',
                    // Array 'errors' dihilangkan karena TeacherTemplateValidation tidak lagi mengumpulkan kegagalan detail
                ], 422);
            }

            // --- FASE 2: IMPOR DATA GURU (HANYA JIKA TEMPLATE VALID) ---
            $dataImporter = new TeacherImport();

            // PENTING: Panggil import untuk TeacherImport.
            // Karena TeacherImport mengimplementasikan WithTitle, ia akan secara otomatis
            // mencari dan memproses sheet DataGuru (sesuai dengan title() methodnya).
            Excel::import($dataImporter, $request->file('file'), null, \Maatwebsite\Excel\Excel::XLSX, [
                'selected_sheets' => [TeacherTemplateExport::DATA_SHEET_NAME], // Pastikan hanya DataGuru sheet yang diimpor
                'heading_row' => 1, // Heading untuk DataGuru ada di baris 1
            ]);

            // Jika semua berhasil
            return response()->json([
                'message' => 'Data guru berhasil diimpor! Total ' . $dataImporter->getImportedRowCount() . ' baris data berhasil.',
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
