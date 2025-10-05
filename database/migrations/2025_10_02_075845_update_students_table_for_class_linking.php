<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('students', function (Blueprint $table) {
            // Remove old columns
            $table->dropColumn(['grade', 'section']);

            // Add new class_id column
            $table->unsignedBigInteger('class_id')->nullable()->after('guardian_id');
            $table->foreign('class_id')->references('id')->on('classes')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            // Drop foreign key and column
            $table->dropForeign(['class_id']);
            $table->dropColumn('class_id');

            // Restore old columns
            $table->string('grade')->nullable();
            $table->string('section')->nullable();
        });
    }
};
