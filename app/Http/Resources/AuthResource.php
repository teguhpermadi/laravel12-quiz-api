<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AuthResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            // Tambahkan atribut lain yang ingin Anda sertakan (misal: created_at)
            'roles' => $this->whenLoaded('roles', function () {
                return $this->roles->pluck('name');
            }),
            'permissions' => $this->whenLoaded('permissions', function () {
                // Pastikan Anda memuat relasi 'permissions' di controller jika ingin ini muncul
                return $this->getAllPermissions()->pluck('name');
            }),
        ];
    }
}