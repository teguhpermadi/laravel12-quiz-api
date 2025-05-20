<?php

namespace App\Http\Controllers;

use App\Http\Requests\GradeRequest;
use App\Http\Resources\GradeResource;
use App\Models\Grade;
use App\Models\StudentGrade;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Spatie\QueryBuilder\QueryBuilder;

class GradeController extends Controller
{
    /**
     * Menampilkan daftar semua kelas dengan filter, sorting, dan pagination
     */
    public function index(Request $request)
    {
        $academicYearId = $request->input('academic_year_id');
        
        $query = QueryBuilder::for(Grade::class)
            ->allowedFilters(Grade::allowedFilters())
            ->allowedSorts(Grade::allowedSorts())
            ->allowedIncludes(Grade::allowedIncludes());
            
        // Jika ada parameter academic_year_id, tambahkan jumlah siswa
        if ($academicYearId) {
            $query->withCount(['students as student_count' => function($query) use ($academicYearId) {
                $query->where('academic_year_id', $academicYearId);
            }]);
        }
        
        $grades = $query->paginate($request->input('per_page', 15))
            ->appends($request->query());
        
        return response()->json([
            'status' => 'success',
            'data' => GradeResource::collection($grades),
            'meta' => [
                'current_page' => $grades->currentPage(),
                'from' => $grades->firstItem(),
                'last_page' => $grades->lastPage(),
                'per_page' => $grades->perPage(),
                'to' => $grades->lastItem(),
                'total' => $grades->total(),
            ],
            'links' => [
                'first' => $grades->url(1),
                'last' => $grades->url($grades->lastPage()),
                'prev' => $grades->previousPageUrl(),
                'next' => $grades->nextPageUrl(),
            ],
        ]);
    }

    /**
     * Menyimpan kelas baru
     */
    public function store(GradeRequest $request)
    {
        $grade = Grade::create($request->validated());
        
        return response()->json([
            'status' => 'success',
            'message' => 'Grade added successfully',
            'data' => new GradeResource($grade)
        ], 201);
    }

    /**
     * Menampilkan detail kelas
     */
    public function show(Request $request, Grade $grade)
    {
        $academicYearId = $request->input('academic_year_id');
        
        // Jika ada parameter academic_year_id, load siswa berdasarkan tahun akademik
        if ($academicYearId) {
            $grade->load(['students' => function($query) use ($academicYearId) {
                $query->where('academic_year_id', $academicYearId)
                      ->with(['student', 'academicYear']);
            }]);
            
            // Hitung jumlah siswa
            $grade->student_count = $grade->students->count();
        }
        
        return response()->json([
            'status' => 'success',
            'data' => new GradeResource($grade)
        ]);
    }

    /**
     * Memperbarui data kelas
     */
    public function update(GradeRequest $request, Grade $grade)
    {
        $grade->update($request->validated());
        
        return response()->json([
            'status' => 'success',
            'message' => 'Grade data updated successfully',
            'data' => new GradeResource($grade)
        ]);
    }

    /**
     * Menghapus data kelas
     */
    public function destroy(Grade $grade)
    {
        $grade->delete();
        
        return response()->json([
            'status' => 'success',
            'message' => 'Grade data deleted successfully'
        ]);
    }
}