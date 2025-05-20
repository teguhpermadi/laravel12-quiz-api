<?php

namespace App\Http\Controllers;

use App\Http\Requests\StudentRequest;
use App\Http\Resources\StudentResource;
use App\Models\Student;
use Illuminate\Http\Request;
use Spatie\QueryBuilder\QueryBuilder;

class StudentController extends Controller
{
    /**
     * Menampilkan daftar semua siswa dengan filter, sorting, dan pagination
     */
    public function index(Request $request)
    {
        $students = QueryBuilder::for(Student::class)
            ->allowedFilters(Student::allowedFilters())
            ->allowedSorts(Student::allowedSorts())
            ->allowedIncludes(Student::allowedIncludes())
            ->paginate($request->input('per_page', 15))
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
    public function show(Student $student)
    {
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
}