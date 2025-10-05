<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Teacher extends Model
{
    protected $fillable = [
        'user_id',
        'full_name',
        'subject',
        'phone',
        'email',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function classes(): HasMany
    {
        return $this->hasMany(ClassModel::class, 'teacher_id');
    }

    public function subjects(): HasMany
    {
        return $this->hasMany(Subject::class);
    }

    public function grades(): HasMany
    {
        return $this->hasMany(Grade::class);
    }
}
