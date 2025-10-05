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
    Schema::create('grades', function (Blueprint $table) {
    $table->id();
    $table->unsignedBigInteger('student_id');
    $table->unsignedBigInteger('subject_id');
    $table->string('exam_type'); // quiz, midterm, final
    $table->decimal('score', 5, 2)->nullable();
    $table->timestamps();

    $table->foreign('student_id')->references('id')->on('students')->onDelete('cascade');
    $table->foreign('subject_id')->references('id')->on('subjects')->onDelete('cascade');
});

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('grades');
    }
};
