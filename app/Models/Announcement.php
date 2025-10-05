<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Announcement extends Model
{
    protected $fillable = [
        'admin_id',
        'title',
        'message',
        'date',
    ];

    public function administrator(): BelongsTo
    {
        return $this->belongsTo(Administrator::class, 'admin_id');
    }
}
