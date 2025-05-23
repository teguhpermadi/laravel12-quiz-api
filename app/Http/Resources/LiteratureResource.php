<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LiteratureResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $media = $this->getFirstMedia('literature_media');
        
        return [
            'id' => $this->id,
            'title' => $this->title,
            'content' => $this->content,
            'source' => $this->source,
            'author' => $this->author,
            'media' => $media ? [
                'url' => $media->getUrl(),
                'mime_type' => $media->mime_type,
                'size' => $media->size,
                'file_name' => $media->file_name
            ] : null,
            'questions_count' => $this->when(isset($this->questions_count), $this->questions_count),
            'questions' => QuestionResource::collection($this->whenLoaded('questions')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}