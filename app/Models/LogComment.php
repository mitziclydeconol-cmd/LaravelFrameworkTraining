<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LogComment extends Model
{
    use HasFactory;

    protected $fillable = ['coding_log_id', 'user_id', 'body'];

    public function codingLog(): BelongsTo
    {
        return $this->belongsTo(CodingLog::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
