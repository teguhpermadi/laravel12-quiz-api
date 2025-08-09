<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TeacherResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        // Cari token aktif yang belum digunakan dan belum kadaluarsa
        // Relasi profileLinkTokens harus sudah di-eager load dengan constraint
        $activeToken = $this->whenLoaded('profileLinkTokens', function () {
            return $this->profileLinkTokens->first(function ($token) {
                return is_null($token->used_at) && $token->expires_at->isFuture();
            });
        });

        return [
            'id' => $this->id,
            'name' => $this->name,
            'gender' => $this->gender,
            'nip' => $this->nip,
            'subject_count' => $this->whenNotNull($this->subject_count),
            'subjects' => SubjectResource::collection($this->whenLoaded('subjects')),
            'user' => new UserResource($this->whenLoaded('user')),
            'is_linked_to_user' => $this->user()->exists(), // Status apakah sudah tertaut user
            'active_link_token' => $this->when($activeToken, function () use ($activeToken) {
                return [
                    'token' => $activeToken->token,
                    'expires_at' => $activeToken->expires_at->toDateTimeString(),
                    // Anda bisa tambahkan linking_url di sini jika ingin, tapi frontend bisa membuatnya
                ];
            }),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}