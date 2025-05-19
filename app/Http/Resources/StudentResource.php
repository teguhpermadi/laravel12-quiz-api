<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StudentResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'gender' => $this->gender,
            'nisn' => $this->nisn,
            'nis' => $this->nis,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at
        ];
    }
}