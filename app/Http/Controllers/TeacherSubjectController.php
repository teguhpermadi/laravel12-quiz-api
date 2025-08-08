<?php

namespace App\Http\Controllers;

use App\Http\Requests\TeacherSubjectRequest;
use App\Http\Resources\TeacherSubjectResource;
use App\Models\TeacherSubject;
use Illuminate\Http\Request;
use Spatie\QueryBuilder\QueryBuilder;

class TeacherSubjectController extends Controller
{
    /**
     * Menampilkan daftar semua mata pelajaran guru dengan filter, sorting, dan pagination
     */
    public function index(Request $request)
    {
        $teacherSubjects = QueryBuilder::for(TeacherSubject::class)
            ->allowedFilters(TeacherSubject::allowedFilters())
            ->allowedSorts(TeacherSubject::allowedSorts())
            ->allowedIncludes(TeacherSubject::allowedIncludes())
            ->with(['academicYear', 'teacher.profileLinkTokens', 'subject', 'grade'])
            ->paginate($request->input('per_page', 15))
            ->appends($request->query());
        
        return response()->json([
            'status' => 'success',
            'data' => TeacherSubjectResource::collection($teacherSubjects),
            'meta' => [
                'current_page' => $teacherSubjects->currentPage(),
                'from' => $teacherSubjects->firstItem(),
                'last_page' => $teacherSubjects->lastPage(),
                'per_page' => $teacherSubjects->perPage(),
                'to' => $teacherSubjects->lastItem(),
                'total' => $teacherSubjects->total(),
            ],
            'links' => [
                'first' => $teacherSubjects->url(1),
                'last' => $teacherSubjects->url($teacherSubjects->lastPage()),
                'prev' => $teacherSubjects->previousPageUrl(),
                'next' => $teacherSubjects->nextPageUrl(),
            ],
        ]);
    }

    /**
     * Menyimpan mata pelajaran guru baru
     */
    public function store(TeacherSubjectRequest $request)
    {
        $teacherSubject = TeacherSubject::create($request->validated());

        // Pastikan relasi 'teacher' dan 'profileLinkTokens' dimuat
        $teacherSubject->load(['teacher.profileLinkTokens']);
        
        return response()->json([
            'status' => 'success',
            'message' => 'Teacher Subject added successfully',
            'data' => new TeacherSubjectResource($teacherSubject)
        ], 201);
    }

    /**
     * Menampilkan detail mata pelajaran guru
     */
    public function show(TeacherSubject $teacherSubject)
    {
        // Pastikan relasi 'teacher' dan 'profileLinkTokens' dimuat
        $teacherSubject->load(['teacher.profileLinkTokens']);

        return response()->json([
            'status' => 'success',
            'data' => new TeacherSubjectResource($teacherSubject)
        ]);
    }

    /**
     * Memperbarui data mata pelajaran guru
     */
    public function update(TeacherSubjectRequest $request, TeacherSubject $teacherSubject)
    {
        $teacherSubject->update($request->validated());
        
        // Pastikan relasi 'teacher' dan 'profileLinkTokens' dimuat
        $teacherSubject->load(['teacher.profileLinkTokens']);

        return response()->json([
            'status' => 'success',
            'message' => 'Teacher Subject data updated successfully',
            'data' => new TeacherSubjectResource($teacherSubject)
        ]);
    }

    /**
     * Menghapus data mata pelajaran guru
     */
    public function destroy(TeacherSubject $teacherSubject)
    {
        $teacherSubject->delete();
        
        return response()->json([
            'status' => 'success',
            'message' => 'Teacher Subject data deleted successfully'
        ]);
    }
}