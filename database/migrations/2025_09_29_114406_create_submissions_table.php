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
    Schema::create('submissions', function (Blueprint $table) {
    $table->id();
    $table->unsignedBigInteger('assignment_id');
    $table->unsignedBigInteger('student_id');
    $table->string('file_url')->nullable();
    $table->timestamp('submitted_at')->nullable();
    $table->decimal('grade', 5, 2)->nullable();
    $table->timestamps();

    $table->foreign('assignment_id')->references('id')->on('assignments')->onDelete('cascade');
    $table->foreign('student_id')->references('id')->on('students')->onDelete('cascade');
});

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('submissions');
    }
};
