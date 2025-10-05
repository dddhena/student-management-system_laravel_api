<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Attendance extends Model
{
    protected $table = 'attendance';

    protected $fillable = [
        'student_id',
        'date',
        'status', // present, absent, late, etc.
        'subject_id',
        'teacher_id',
    ];

   
    public function student() {
    return $this->belongsTo(Student::class);
}

public function subject() {
    return $this->belongsTo(Subject::class);
}

public function teacher() {
    return $this->belongsTo(Teacher::class);
}

}
