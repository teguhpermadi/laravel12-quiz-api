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
        Schema::create('teacher_link_tokens', function (Blueprint $table) {
            $table->ulid('id')->primary(); // Menggunakan ULID sebagai primary key
            $table->foreignUlid('teacher_id')->constrained()->onDelete('cascade'); // Foreign key ke tabel teachers
            $table->string('token')->unique(); // Token unik untuk link
            $table->timestamp('expires_at'); // Waktu kadaluarsa token
            $table->timestamp('used_at')->nullable(); // Waktu token digunakan (untuk sekali pakai)
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('teacher_link_tokens');
    }
};
