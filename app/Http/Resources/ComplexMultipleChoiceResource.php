<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ComplexMultipleChoiceResource extends JsonResource
{
    public function toArray($request)
    {
        $media = $this->getFirstMedia('answer_media');
        
        return [
            'id' => $this->id,
            'choice' => $this->choice,
            'is_correct' => $this->is_correct,
            'media' => $media ? [
                'url' => $media->getUrl(),
                'mime_type' => $media->mime_type,
                'size' => $media->size,
                'file_name' => $media->file_name
            ] : null,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}