<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTeacherIdToAttendancesTable extends Migration
{
    public function up()
    {
        Schema::table('attendance', function (Blueprint $table) {
            $table->foreignId('teacher_id')
                  ->after('subject_id')
                  ->constrained()
                  ->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::table('attendances', function (Blueprint $table) {
            $table->dropForeign(['teacher_id']);
            $table->dropColumn('teacher_id');
        });
    }
}
