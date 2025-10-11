<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LeaveRequest extends Model
{
    protected $fillable = [
        'student_id',
        'subject_id',
        'teacher_id',
        'start_date',
        'end_date',
        'reason',
        'status',
    ];

   public function subject() {
    return $this->belongsTo(Subject::class);
}

public function teacher() {
    return $this->belongsTo(Teacher::class);
}

public function student() {
    return $this->belongsTo(Student::class);
}

}
