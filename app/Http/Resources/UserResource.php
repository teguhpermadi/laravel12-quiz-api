<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'email_verified_at' => $this->when(isset($this->email_verified_at), $this->email_verified_at),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'userable_id' => $this->userable_id,
            'userable_type' => $this->userable_type,
            'userable' => $this->whenLoaded('userable'), // Ini penting!
            'roles' => $this->whenLoaded('roles', function () {
                return $this->roles->pluck('name');
            }),
            'permissions' => $this->whenLoaded('permissions', function () {
                return $this->permissions->pluck('name');
            })
        ];
    }
}
