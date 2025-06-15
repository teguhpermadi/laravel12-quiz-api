<?php

namespace App\Http\Controllers;

use App\Events\NewTeacherAdded;
use App\Exports\TeachersExport;
use App\Exports\TeacherTemplateExport;
use App\Http\Requests\TeacherRequest;
use App\Http\Resources\TeacherResource;
use App\Imports\TeachersImport;
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
        NewTeacherAdded::dispatch($teacher);

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

        return response()->json([
            'status' => 'success',
            'message' => 'Teacher data deleted successfully'
        ]);
    }

    // bulk delete
    public function bulkDelete(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'string|exists:teachers,id', // Validasi setiap ID
        ]);

        Teacher::whereIn('id', $request->ids)->delete();

        return response()->json(['message' => 'Teachers deleted successfully'], 200);
    }

    public function export()
    {
        try {
            return Excel::download(new TeachersExport, 'teachers.xlsx');
        } catch (\Exception $e) {
            return response()->json(['message' => 'Gagal mengekspor data guru: ' . $e->getMessage()], 500);
        }
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xls,xlsx|max:2048', // Batasi tipe file dan ukuran
        ]);

        try {
            Excel::import(new TeachersImport, $request->file('file'));
            return response()->json(['message' => 'Data guru berhasil diimpor.']);
        } catch (ValidationException $e) {
            // Tangani error validasi dari Maatwebsite/Excel
            $failures = $e->failures();
            // Format error untuk dikembalikan sebagai response`
            $errors = [];
            
            foreach ($failures as $failure) {
                $errors[] = [
                    'row' => $failure->row(),
                    'attribute' => $failure->attribute(),
                    'errors' => $failure->errors(),
                    'values' => $failure->values(),
                ];
            }
            return response()->json(['message' => 'Impor gagal karena masalah validasi.', 'errors' => $errors], 422);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Gagal mengimpor data guru: ' . $e->getMessage()], 500);
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
}
