<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class QuestionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'question' => $this->question,
            'question_type' => $this->question_type->value,
            'question_type_description' => $this->question_type->description(),
            'time' => $this->time->value,
            'time_description' => $this->time->description(),
            'score' => $this->score->value,
            'score_description' => $this->score->description(),
            'teacher_id' => $this->teacher_id,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}