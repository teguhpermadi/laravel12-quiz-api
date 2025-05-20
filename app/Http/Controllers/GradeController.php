<?php

namespace App\Http\Controllers;

use App\Http\Requests\GradeRequest;
use App\Http\Resources\GradeResource;
use App\Models\Grade;
use App\Traits\ApiQueryBuilder;
use Illuminate\Http\Request;

class GradeController extends Controller
{
    use ApiQueryBuilder;
    
    /**
     * Menampilkan daftar semua kelas dengan filter, sorting, dan pagination
     */
    public function index(Request $request)
    {
        $query = Grade::query();
        $grades = $this->applyQueryBuilder($request, $query);
        
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
    public function show(Grade $grade)
    {
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