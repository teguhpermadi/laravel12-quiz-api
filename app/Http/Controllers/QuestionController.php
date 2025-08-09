<?php

namespace App\Http\Controllers;

use App\Http\Requests\ComplexMultipleChoiceRequest;
use App\Http\Requests\EssayAnswerRequest;
use App\Http\Requests\MultipleChoiceRequest;
use App\Http\Requests\QuestionRequest;
use App\Http\Requests\ShortAnswerRequest;
use App\Http\Requests\TrueFalseRequest;
use App\Http\Resources\QuestionResource;
use App\Models\ComplexMultipleChoice;
use App\Models\EssayAnswer;
use App\Models\MultipleChoice;
use App\Models\Question;
use App\Models\ShortAnswer;
use App\Models\TrueFalse;
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
            ->allowedSorts(Question::allowedSorts());

        // Jika ada parameter include, cek apakah mengandung 'answerables'
        if ($request->has('include')) {
            $includes = explode(',', $request->include);
            if (in_array('answerables', $includes)) {
                // Jika ada 'answerables', load semua relasi answerable
                $query->with(['multipleChoices', 'complexMultipleChoices', 'trueFalses', 'shortAnswers', 'essayAnswers']);
            }
            // Tetap proses includes biasa
            $query->allowedIncludes(Question::allowedIncludes());
        }

        // Tambahkan relasi teacher dan literature jika ada
        $query->with(['teacher.profileLinkTokens', 'literature']);

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
    public function store(
        QuestionRequest $questionRequest,
        MultipleChoiceRequest $mcRequest,
        ComplexMultipleChoiceRequest $cmcRequest,
        TrueFalseRequest $tfRequest,
        ShortAnswerRequest $saRequest,
        EssayAnswerRequest $eaRequest,
    ) {
        $question = Question::create($questionRequest->validated());

        switch ($questionRequest->question_type) {
            case 'MULTIPLE_CHOICE':
                foreach ($mcRequest->choices as $choice) {
                    $question->answerables()->create([
                        'answerable_type' => MultipleChoice::class,
                        'answerable_id' => MultipleChoice::create($choice)->id
                    ]);
                }
                break;
            case 'COMPLEX_MULTIPLE_CHOICE':
                foreach ($cmcRequest->choices as $choice) {
                    $question->answerables()->create([
                        'answerable_type' => ComplexMultipleChoice::class,
                        'answerable_id' => ComplexMultipleChoice::create($choice)->id
                    ]);
                }
                break;
            case 'TRUE_FALSE':
                $question->answerables()->create([
                    'answerable_type' => TrueFalse::class,
                    'answerable_id' => TrueFalse::create($tfRequest->validated())->id
                ]);
                break;
            case 'SHORT_ANSWER':
                $question->answerables()->create([
                    'answerable_type' => ShortAnswer::class,
                    'answerable_id' => ShortAnswer::create($saRequest->validated())->id
                ]);
                break;
            case 'ESSAY':
                $question->answerables()->create([
                    'answerable_type' => EssayAnswer::class,
                    'answerable_id' => EssayAnswer::create($eaRequest->validated())->id
                ]);
                break;
        }

        // Load teacher dan literature jika ada
        $question->load('teacher');

        if ($question->literature_id) {
            $question->load('literature');
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
        // Load answerables berdasarkan tipe soal
        switch ($question->question_type) {
            case \App\Enums\QuestionTypeEnum::MULTIPLE_CHOICE:
                $question->load('multipleChoices');
                break;
            case \App\Enums\QuestionTypeEnum::COMPLEX_MULTIPLE_CHOICE:
                $question->load('complexMultipleChoices');
                break;
            case \App\Enums\QuestionTypeEnum::TRUE_FALSE:
                $question->load('trueFalses');
                break;
            case \App\Enums\QuestionTypeEnum::SHORT_ANSWER:
                $question->load('shortAnswers');
                break;
            case \App\Enums\QuestionTypeEnum::ESSAY:
                $question->load('essayAnswers');
                break;
        }

        // Load teacher dan literature jika ada
        $question->load('teacher');

        if ($question->literature_id) {
            $question->load('literature');
        }

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

        // Load teacher dan literature jika ada
        $question->load('teacher');

        if ($question->literature_id) {
            $question->load('literature');
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
