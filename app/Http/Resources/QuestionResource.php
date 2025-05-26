<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class QuestionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $media = $this->getFirstMedia('question_media');
        
        $response = [
            'id' => $this->id,
            'question' => $this->question,
            'question_type' => $this->question_type->value,
            'question_type_description' => $this->question_type->description(),
            'order' => $this->when(isset($this->order), $this->order),
            'time' => $this->time->value,
            'time_description' => $this->time->description(),
            'score' => $this->score->value,
            'score_description' => $this->score->description(),
            'teacher_id' => $this->teacher_id,
            'teacher' => $this->whenLoaded('teacher', function () {
                return new TeacherResource($this->teacher);
            }),
            'literature_id' => $this->literature_id,
            'literature' => $this->whenLoaded('literature', function () {
                return new LiteratureResource($this->literature);
            }),
            'media' => $media ? [
                'url' => $media->getUrl(),
                'mime_type' => $media->mime_type,
                'size' => $media->size,
                'file_name' => $media->file_name
            ] : null,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];

        // Tambahkan jawaban sesuai tipe soal
        switch ($this->question_type) {
            case \App\Enums\QuestionTypeEnum::MULTIPLE_CHOICE:
                $response['answers'] = $this->whenLoaded('multipleChoices', function () {
                    return MultipleChoiceResource::collection($this->multipleChoices);
                });
                break;
            case \App\Enums\QuestionTypeEnum::COMPLEX_MULTIPLE_CHOICE:
                $response['answers'] = $this->whenLoaded('complexMultipleChoices', function () {
                    return ComplexMultipleChoiceResource::collection($this->complexMultipleChoices);
                });
                break;
            case \App\Enums\QuestionTypeEnum::TRUE_FALSE:
                $response['answers'] = $this->whenLoaded('trueFalses', function () {
                    return TrueFalseResource::collection($this->trueFalses);
                });
                break;
            case \App\Enums\QuestionTypeEnum::SHORT_ANSWER:
                $response['answers'] = $this->whenLoaded('shortAnswers', function () {
                    return ShortAnswerResource::collection($this->shortAnswers);
                });
                break;
            case \App\Enums\QuestionTypeEnum::ESSAY:
                $response['answers'] = $this->whenLoaded('essayAnswers', function () {
                    return EssayAnswerResource::collection($this->essayAnswers);
                });
                break;
        }

        return $response;
    }
}