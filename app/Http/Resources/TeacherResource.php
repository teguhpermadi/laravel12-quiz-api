<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TeacherResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'gender' => $this->gender,
            'nip' => $this->nip,
            'subject_count' => $this->when(isset($this->subject_count), $this->subject_count),
            'subjects' => $this->when($this->relationLoaded('subjects'), function() {
                return TeacherSubjectResource::collection($this->subjects);
            }),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at
        ];
    }
}