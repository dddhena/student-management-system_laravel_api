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
    Schema::create('fees', function (Blueprint $table) {
    $table->id();
    $table->unsignedBigInteger('student_id');
    $table->string('type'); // tuition, library, lab, etc.
    $table->decimal('amount', 8, 2);
    $table->date('due_date')->nullable();
    $table->boolean('is_paid')->default(false);
    $table->timestamps();

    $table->foreign('student_id')->references('id')->on('students')->onDelete('cascade');
});

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fees');
    }
};
