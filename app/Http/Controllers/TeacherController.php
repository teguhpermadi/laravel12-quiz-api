<?php

namespace App\Http\Controllers;

use App\Http\Requests\TeacherRequest;
use App\Http\Resources\TeacherResource;
use App\Models\Teacher;
use Illuminate\Http\Request;
use Spatie\QueryBuilder\QueryBuilder;

class TeacherController extends Controller
{
    /**
     * Menampilkan daftar semua guru dengan filter, sorting, dan pagination
     */
    public function index(Request $request)
    {
        $teachers = QueryBuilder::for(Teacher::class)
            ->allowedFilters(Teacher::allowedFilters())
            ->allowedSorts(Teacher::allowedSorts())
            ->allowedIncludes(Teacher::allowedIncludes())
            ->paginate($request->input('per_page', 15))
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
        
        return response()->json([
            'status' => 'success',
            'message' => 'Teacher added successfully',
            'data' => new TeacherResource($teacher)
        ], 201);
    }

    /**
     * Menampilkan detail guru
     */
    public function show(Teacher $teacher)
    {
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
}