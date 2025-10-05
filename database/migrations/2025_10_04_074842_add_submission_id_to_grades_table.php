<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('grades', function (Blueprint $table) {
            $table->unsignedBigInteger('submission_id')->nullable()->after('subject_id');

            $table->foreign('submission_id')
                  ->references('id')
                  ->on('submissions')
                  ->onDelete('cascade');
        });
    }

    public function down(): void {
        Schema::table('grades', function (Blueprint $table) {
            $table->dropForeign(['submission_id']);
            $table->dropColumn('submission_id');
        });
    }
};
