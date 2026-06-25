<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AiFeedbackLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'coding_log_id',
        'user_id',
        'prompt_sent',
        'feedback_received',
        'model_used',
        'tokens_used',
        'status',
    ];

    public function codingLog(): BelongsTo
    {
        return $this->belongsTo(CodingLog::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
