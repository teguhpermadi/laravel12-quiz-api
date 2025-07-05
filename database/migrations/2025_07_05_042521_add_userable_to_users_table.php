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
        Schema::table('users', function (Blueprint $table) {
            $table->string('userable_id', 26)->nullable();   // Tipe string dengan panjang 26 untuk ULID
            $table->string('userable_type')->nullable(); // Tipe string untuk nama kelas
            $table->index(['userable_id', 'userable_type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['userable_id', 'userable_type']); // Drop index
            $table->dropColumn(['userable_id', 'userable_type']); // Drop kolom
        });
    }
};
