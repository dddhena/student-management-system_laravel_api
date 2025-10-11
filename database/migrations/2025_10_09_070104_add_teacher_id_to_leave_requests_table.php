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
Schema::table('leave_requests', function (Blueprint $table) {
    $table->unsignedBigInteger('subject_id')->nullable()->after('student_id');
    $table->unsignedBigInteger('teacher_id')->nullable()->after('subject_id');
    $table->foreign('subject_id')->references('id')->on('subjects')->onDelete('set null');
    $table->foreign('teacher_id')->references('id')->on('teachers')->onDelete('set null');
});

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('leave_requests', function (Blueprint $table) {
            //
        });
    }
};
