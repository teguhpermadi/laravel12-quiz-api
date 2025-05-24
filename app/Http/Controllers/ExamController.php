<?php

namespace App\Http\Controllers;

use App\Http\Requests\ExamRequest;
use App\Http\Resources\ExamResource;
use App\Models\Exam;
use Illuminate\Http\Request;
use Spatie\QueryBuilder\QueryBuilder;

class ExamController extends Controller
{
    public function index(Request $request)
    {
        $query = QueryBuilder::for(Exam::class)
        ->allowedFilters(Exam::allowedFilters())
            ->allowedSorts(Exam::allowedSorts())
            ->allowedIncludes(Exam::allowedIncludes());
            
        $exams = $query->paginate($request->input('per_page', 15))
            ->appends($request->query());
        
        return response()->json([
            'status' => 'success',
            'data' => ExamResource::collection($exams),
            'meta' => $this->buildPaginationMeta($exams)
        ]);
    }

    public function store(ExamRequest $request)
    {
        $exam = Exam::create($request->validated());
        $exam->load(['teacher', 'subject', 'grade']);
        
        return response()->json([
            'status' => 'success',
            'message' => 'Exam created successfully',
            'data' => new ExamResource($exam)
        ], 201);
    }

    public function show(Exam $exam)
    {
        $exam->load(['teacher', 'subject', 'grade']);
        
        return response()->json([
            'status' => 'success',
            'data' => new ExamResource($exam)
        ]);
    }

    public function update(ExamRequest $request, Exam $exam)
    {
        $exam->update($request->validated());
        $exam->load(['teacher', 'subject', 'grade']);
        
        return response()->json([
            'status' => 'success',
            'message' => 'Exam updated successfully',
            'data' => new ExamResource($exam)
        ]);
    }

    public function destroy(Exam $exam)
    {
        $exam->delete();
        
        return response()->json([
            'status' => 'success',
            'message' => 'Exam deleted successfully'
        ]);
    }

    private function buildPaginationMeta($paginator)
    {
        return [
            'current_page' => $paginator->currentPage(),
            'from' => $paginator->firstItem(),
            'last_page' => $paginator->lastPage(),
            'per_page' => $paginator->perPage(),
            'to' => $paginator->lastItem(),
            'total' => $paginator->total(),
        ];
    }
}