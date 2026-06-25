<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SelfAssessment extends Model
{
    use HasFactory;

    protected $fillable = [
        'coding_log_id', 'user_id',
        'understanding', 'confidence', 'effort',
        'reflection', 'next_steps',
    ];

    protected function casts(): array
    {
        return [
            'understanding' => 'integer',
            'confidence'    => 'integer',
            'effort'        => 'integer',
        ];
    }

    public function codingLog(): BelongsTo
    {
        return $this->belongsTo(CodingLog::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getAverageScoreAttribute(): float
    {
        return round(($this->understanding + $this->confidence + $this->effort) / 3, 1);
    }

    public function getRatingLabelAttribute(): string
    {
        return match (true) {
            $this->average_score >= 4.5 => 'Excellent',
            $this->average_score >= 3.5 => 'Good',
            $this->average_score >= 2.5 => 'Fair',
            default                     => 'Needs Work',
        };
    }
}
