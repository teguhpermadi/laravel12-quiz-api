<?php

namespace App\Http\Controllers;

use App\Http\Requests\SubjectRequest;
use App\Http\Resources\SubjectResource;
use App\Models\Subject;
use Illuminate\Http\Request;
use Spatie\QueryBuilder\QueryBuilder;

class SubjectController extends Controller
{
    /**
     * Menampilkan daftar semua mata pelajaran dengan filter, sorting, dan pagination
     */
    public function index(Request $request)
    {
        $subjects = QueryBuilder::for(Subject::class)
            ->allowedFilters(Subject::allowedFilters())
            ->allowedSorts(Subject::allowedSorts())
            ->allowedIncludes(Subject::allowedIncludes())
            ->paginate($request->input('per_page', 15))
            ->appends($request->query());
        
        return response()->json([
            'status' => 'success',
            'data' => SubjectResource::collection($subjects),
            'meta' => [
                'current_page' => $subjects->currentPage(),
                'from' => $subjects->firstItem(),
                'last_page' => $subjects->lastPage(),
                'per_page' => $subjects->perPage(),
                'to' => $subjects->lastItem(),
                'total' => $subjects->total(),
            ],
            'links' => [
                'first' => $subjects->url(1),
                'last' => $subjects->url($subjects->lastPage()),
                'prev' => $subjects->previousPageUrl(),
                'next' => $subjects->nextPageUrl(),
            ],
        ]);
    }

    /**
     * Menyimpan mata pelajaran baru
     */
    public function store(SubjectRequest $request)
    {
        $subject = Subject::create($request->validated());
        
        return response()->json([
            'status' => 'success',
            'message' => 'Subject added successfully',
            'data' => new SubjectResource($subject)
        ], 201);
    }

    /**
     * Menampilkan detail mata pelajaran
     */
    public function show(Subject $subject)
    {
        return response()->json([
            'status' => 'success',
            'data' => new SubjectResource($subject)
        ]);
    }

    /**
     * Memperbarui data mata pelajaran
     */
    public function update(SubjectRequest $request, Subject $subject)
    {
        $subject->update($request->validated());
        
        return response()->json([
            'status' => 'success',
            'message' => 'Subject data updated successfully',
            'data' => new SubjectResource($subject)
        ]);
    }

    /**
     * Menghapus data mata pelajaran
     */
    public function destroy(Subject $subject)
    {
        $subject->delete();
        
        return response()->json([
            'status' => 'success',
            'message' => 'Subject data deleted successfully'
        ]);
    }
}