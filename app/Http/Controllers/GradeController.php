<?php

namespace App\Http\Controllers;

use App\Http\Requests\GradeRequest;
use App\Http\Resources\GradeResource;
use App\Models\AcademicYear;
use App\Models\Grade;
use App\Models\StudentGrade;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Spatie\QueryBuilder\AllowedFilter;
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
            ->allowedFilters(AllowedFilter::exact('academic_year_id')->default($academicYearId))
            ->allowedSorts(Grade::allowedSorts())
            ->allowedIncludes(Grade::allowedIncludes())
            ->with(['academicYear'])
            ->withCount(['students as student_count' => function ($query) use ($academicYearId) {
                $query->where('academic_year_id', $academicYearId);
            }])
            ->withCount(['subjects as subjects_count' => function ($query) use ($academicYearId) {
                $query->where('academic_year_id', $academicYearId);
            }]);

        if ($request->input('per_page') === 'all') {
            // Ambil semua data tanpa paginasi
            $grades = $query->get();
            
            return response()->json([
                'status' => 'success',
                'data' => GradeResource::collection($grades),
            ]);

        } else {
            // Lanjutkan dengan paginasi (default behavior)
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
    }

    /**
     * Menyimpan kelas baru
     */
    public function store(GradeRequest $request)
    {
        $validatedData = $request->validated();

        $grade = Grade::create([
            'name' => $validatedData['name'],
            'level' => $validatedData['level'],
            'academic_year_id' => $validatedData['academic_year_id'],
        ]);

        // 2. Gunakan sync() atau attach() untuk menyimpan relasi
        // sync() akan memastikan relasi yang ada di array 'student_ids' tersimpan,
        // dan menghapus relasi lain yang tidak ada di array tersebut.
        // Jika Anda hanya ingin menambahkan relasi baru, gunakan attach().
        $grade->students()->sync($validatedData['student_ids']);

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

        // grade with students
        $grade->load('students', 'academicYear', 'subjects.subject', 'subjects.teacher.profileLinkTokens');
        // grade with load student count
        $grade->loadCount('students as student_count', 'subjects as subjects_count');

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
        $validatedData = $request->validated();

        $grade->update([
            'name' => $validatedData['name'],
            'level' => $validatedData['level'],
            'academic_year_id' => $validatedData['academic_year_id'],
        ]);

        // Update relasi siswa
        // sync() akan memastikan relasi yang ada di array 'student_ids' tersimpan,
        // dan menghapus relasi lain yang tidak ada di array tersebut.
        // Jika Anda hanya ingin menambahkan relasi baru, gunakan attach().
        $grade->students()->sync($validatedData['student_ids']);

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
