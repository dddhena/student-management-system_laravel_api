<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Grade extends Model
{
    protected $fillable = [
        'student_id',
        'teacher_id',
        'subject_id',
        'exam_type',
        'score',
    ];

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }
    public function teacher(): BelongsTo
    {
        return $this->belongsTo(Teacher::class);
    }
    public function submission()
{
    return $this->belongsTo(Submission::class);
}

}

