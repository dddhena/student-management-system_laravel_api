<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ClassModel extends Model
{
    protected $table = 'classes'; // Explicitly set table name

    protected $fillable = [
        'name',
        'teacher_id',
    ];

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(Teacher::class);
    }

  // ClassModel.php
public function students()
{
    return $this->hasMany(Student::class, 'class_id'); // explicitly define foreign key
}


    public function subjects(): HasMany
    {
        return $this->hasMany(Subject::class);
    }
  

}
