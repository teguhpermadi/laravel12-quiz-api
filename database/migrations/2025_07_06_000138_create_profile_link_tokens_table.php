<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('profile_link_tokens', function (Blueprint $table) {
            $table->ulid('id')->primary(); // Menggunakan ULID sebagai primary key
            $table->string('linkable_id', 26); // ID unik untuk link, bisa berupa ULID atau UUID
            $table->string('linkable_type'); // Tipe link, misalnya 'student', 'teacher', atau 'profile'
            $table->string('token')->unique(); // Token unik untuk link
            $table->timestamp('expires_at'); // Waktu kadaluarsa token
            $table->timestamp('used_at')->nullable(); // Waktu token digunakan (untuk sekali pakai)
            $table->timestamps();

            $table->index(['linkable_id', 'linkable_type']); // Index untuk performa
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('profile_link_tokens');
    }
};
