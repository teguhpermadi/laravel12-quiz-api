<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo; // Impor MorphTo

class ProfileLinkToken extends Model
{
    use HasFactory, HasUlids;

    protected $fillable = [
        'linkable_id',
        'linkable_type',
        'token',
        'expires_at',
        'used_at',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'used_at' => 'datetime',
    ];

    /**
     * Dapatkan model induk yang menjadi pemilik token ini (Teacher atau Student).
     */
    public function linkable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Periksa apakah token sudah kadaluarsa.
     */
    public function isExpired(): bool
    {
        return $this->expires_at->isPast();
    }

    /**
     * Periksa apakah token sudah digunakan.
     */
    public function isUsed(): bool
    {
        return !is_null($this->used_at);
    }
}
