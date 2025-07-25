<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class EssayAnswerResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'answer' => $this->answer,
            'correction_with_ai' => $this->correction_with_ai,
            'prompt' => $this->prompt,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}