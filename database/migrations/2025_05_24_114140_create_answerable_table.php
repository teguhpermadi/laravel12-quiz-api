<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('answerables', function (Blueprint $table) {
            $table->foreignUlid('question_id')->constrained()->cascadeOnDelete();
            $table->ulid('answerable_id');
            $table->string('answerable_type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('answerable');
    }
};
