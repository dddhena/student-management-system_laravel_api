<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Submission extends Model
{
    protected $fillable = [
        'assignment_id',
        'student_id',
        'teacher_id',
        'file_url',
        'submitted_at',
        'feedback',
        'grade',
    ];

    public function assignment(): BelongsTo
    {
        return $this->belongsTo(Assignment::class);
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }
    public function teacher()
{
    return $this->belongsTo(Teacher::class);
}

public function grade()
{
    return $this->hasOne(Grade::class);
}

}
