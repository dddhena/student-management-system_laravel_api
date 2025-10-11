<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Timetable extends Model
{
    protected $fillable = [
    'teacher_id',
    'class_id',
    'subject_id',
    'day',
    'start_time',
    'end_time',
    'room',
];

    public function teacher() {
  return $this->belongsTo(Teacher::class);
}

public function class() {
  return $this->belongsTo(ClassModel::class);
}

public function subject() {
  return $this->belongsTo(Subject::class);
}

}
