<?php

namespace App\Http\Controllers;

use App\Http\Requests\AcademicYearRequest;
use App\Http\Resources\AcademicYearResource;
use App\Models\AcademicYear;
use Illuminate\Http\Request;
use Spatie\QueryBuilder\QueryBuilder;

class AcademicYearController extends Controller
{
    /**
     * Menampilkan daftar semua tahun akademik dengan filter, sorting, dan pagination
     */
    public function index(Request $request)
    {
        $academicYears = QueryBuilder::for(AcademicYear::class)
            ->allowedFilters(AcademicYear::allowedFilters())
            ->allowedSorts(AcademicYear::allowedSorts())
            ->allowedIncludes(AcademicYear::allowedIncludes())
            ->paginate($request->input('per_page', 15))
            ->appends($request->query());
        
        return response()->json([
            'status' => 'success',
            'data' => AcademicYearResource::collection($academicYears),
            'meta' => [
                'current_page' => $academicYears->currentPage(),
                'from' => $academicYears->firstItem(),
                'last_page' => $academicYears->lastPage(),
                'per_page' => $academicYears->perPage(),
                'to' => $academicYears->lastItem(),
                'total' => $academicYears->total(),
            ],
            'links' => [
                'first' => $academicYears->url(1),
                'last' => $academicYears->url($academicYears->lastPage()),
                'prev' => $academicYears->previousPageUrl(),
                'next' => $academicYears->nextPageUrl(),
            ],
        ]);
    }

    /**
     * Menyimpan tahun akademik baru
     */
    public function store(AcademicYearRequest $request)
    {
        $academicYear = AcademicYear::create($request->validated());
        
        return response()->json([
            'status' => 'success',
            'message' => 'Academic Year added successfully',
            'data' => new AcademicYearResource($academicYear)
        ], 201);
    }

    /**
     * Menampilkan detail tahun akademik
     */
    public function show(AcademicYear $academicYear)
    {
        return response()->json([
            'status' => 'success',
            'data' => new AcademicYearResource($academicYear)
        ]);
    }

    /**
     * Memperbarui data tahun akademik
     */
    public function update(AcademicYearRequest $request, AcademicYear $academicYear)
    {
        $academicYear->update($request->validated());
        
        return response()->json([
            'status' => 'success',
            'message' => 'Academic Year data updated successfully',
            'data' => new AcademicYearResource($academicYear)
        ]);
    }

    /**
     * Menghapus data tahun akademik
     */
    public function destroy(AcademicYear $academicYear)
    {
        $academicYear->delete();
        
        return response()->json([
            'status' => 'success',
            'message' => 'Academic Year data deleted successfully'
        ]);
    }
}