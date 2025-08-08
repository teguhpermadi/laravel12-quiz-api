<?php

namespace App\Http\Controllers;

use App\Http\Requests\StudentGradeRequest;
use App\Http\Resources\StudentGradeResource;
use App\Models\StudentGrade;
use Illuminate\Http\Request;
use Spatie\QueryBuilder\QueryBuilder;

class StudentGradeController extends Controller
{
    /**
     * Menampilkan daftar semua kelas siswa dengan filter, sorting, dan pagination
     */
    public function index(Request $request)
    {
        $studentGrades = QueryBuilder::for(StudentGrade::class)
            ->allowedFilters(StudentGrade::allowedFilters())
            ->allowedSorts(StudentGrade::allowedSorts())
            ->allowedIncludes(StudentGrade::allowedIncludes())
            ->with([
                'academicYear',
                'student',
                'grade',
            ])
            ->paginate($request->input('per_page', 15))
            ->appends($request->query());
        
        return response()->json([
            'status' => 'success',
            'data' => StudentGradeResource::collection($studentGrades),
            'meta' => [
                'current_page' => $studentGrades->currentPage(),
                'from' => $studentGrades->firstItem(),
                'last_page' => $studentGrades->lastPage(),
                'per_page' => $studentGrades->perPage(),
                'to' => $studentGrades->lastItem(),
                'total' => $studentGrades->total(),
            ],
            'links' => [
                'first' => $studentGrades->url(1),
                'last' => $studentGrades->url($studentGrades->lastPage()),
                'prev' => $studentGrades->previousPageUrl(),
                'next' => $studentGrades->nextPageUrl(),
            ],
        ]);
    }

    /**
     * Menyimpan kelas siswa baru
     */
    public function store(StudentGradeRequest $request)
    {
        $studentGrade = StudentGrade::create($request->validated());
        
        // Pastikan relasi 'academicYear', 'student', dan 'grade' dimuat
        $studentGrade->load(['academicYear', 'student', 'grade']);
        
        return response()->json([
            'status' => 'success',
            'message' => 'Student Grade added successfully',
            'data' => new StudentGradeResource($studentGrade)
        ], 201);
    }

    /**
     * Menampilkan detail kelas siswa
     */
    public function show(StudentGrade $studentGrade)
    {
        // Pastikan relasi 'academicYear', 'student', dan 'grade' dimuat
        $studentGrade->load(['academicYear', 'student', 'grade']);

        return response()->json([
            'status' => 'success',
            'data' => new StudentGradeResource($studentGrade)
        ]);
    }

    /**
     * Memperbarui data kelas siswa
     */
    public function update(StudentGradeRequest $request, StudentGrade $studentGrade)
    {
        $studentGrade->update($request->validated());

        // Pastikan relasi 'academicYear', 'student', dan 'grade' dimuat
        $studentGrade->load(['academicYear', 'student', 'grade']);
        
        return response()->json([
            'status' => 'success',
            'message' => 'Student Grade data updated successfully',
            'data' => new StudentGradeResource($studentGrade)
        ]);
    }

    /**
     * Menghapus data kelas siswa
     */
    public function destroy(StudentGrade $studentGrade)
    {
        $studentGrade->delete();
        
        return response()->json([
            'status' => 'success',
            'message' => 'Student Grade data deleted successfully'
        ]);
    }
}