<?php

namespace App\Http\Controllers;

use App\Http\Requests\QuestionRequest;
use App\Http\Resources\QuestionResource;
use App\Models\Question;
use Illuminate\Http\Request;
use Spatie\QueryBuilder\QueryBuilder;

class QuestionController extends Controller
{
    /**
     * Menampilkan daftar semua pertanyaan dengan filter, sorting, dan pagination
     */
    public function index(Request $request)
    {
        $query = QueryBuilder::for(Question::class)
            ->allowedFilters(Question::allowedFilters())
            ->allowedSorts(Question::allowedSorts())
            ->allowedIncludes(Question::allowedIncludes());
            
        $questions = $query->paginate($request->input('per_page', 15))
            ->appends($request->query());
        
        return response()->json([
            'status' => 'success',
            'data' => QuestionResource::collection($questions),
            'meta' => [
                'current_page' => $questions->currentPage(),
                'from' => $questions->firstItem(),
                'last_page' => $questions->lastPage(),
                'per_page' => $questions->perPage(),
                'to' => $questions->lastItem(),
                'total' => $questions->total(),
            ],
            'links' => [
                'first' => $questions->url(1),
                'last' => $questions->url($questions->lastPage()),
                'prev' => $questions->previousPageUrl(),
                'next' => $questions->nextPageUrl(),
            ],
        ]);
    }

    /**
     * Menyimpan pertanyaan baru
     */
    public function store(QuestionRequest $request)
    {
        $question = Question::create($request->validated());
        
        if ($request->hasFile('media')) {
            $question->addMediaFromRequest('media')
                ->toMediaCollection('question_media');
        }
        
        return response()->json([
            'status' => 'success',
            'message' => 'Question added successfully',
            'data' => new QuestionResource($question)
        ], 201);
    }

    /**
     * Menampilkan detail pertanyaan
     */
    public function show(Question $question)
    {
        return response()->json([
            'status' => 'success',
            'data' => new QuestionResource($question)
        ]);
    }

    /**
     * Memperbarui data pertanyaan
     */
    public function update(QuestionRequest $request, Question $question)
    {
        $question->update($request->validated());
        
        if ($request->hasFile('media')) {
            $question->clearMediaCollection('question_media');
            $question->addMediaFromRequest('media')
                ->toMediaCollection('question_media');
        }
        
        return response()->json([
            'status' => 'success',
            'message' => 'Question updated successfully',
            'data' => new QuestionResource($question)
        ]);
    }

    /**
     * Menghapus data pertanyaan
     */
    public function destroy(Question $question)
    {
        $question->delete();
        
        return response()->json([
            'status' => 'success',
            'message' => 'Question deleted successfully'
        ]);
    }
}