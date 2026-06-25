<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CodingLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'subject_id',
        'title',
        'description',
        'programming_language',
        'hours',
        'minutes',
        'log_date',
        'code_snippet',
        'difficulty',
    ];

    protected function casts(): array
    {
        return [
            'log_date' => 'date',
            'hours'    => 'integer',
            'minutes'  => 'integer',
        ];
    }

    // ── Relationships ──────────────────────────────────────────────

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    public function aiFeedbackLogs(): HasMany
    {
        return $this->hasMany(AiFeedbackLog::class);
    }

    // ── Accessors ──────────────────────────────────────────────────

    public function getLatestFeedbackAttribute(): ?AiFeedbackLog
    {
        return $this->aiFeedbackLogs()->latest()->first();
    }

    public function getDurationAttribute(): string
    {
        $parts = [];
        if ($this->hours > 0)   $parts[] = "{$this->hours}h";
        if ($this->minutes > 0) $parts[] = "{$this->minutes}m";
        return implode(' ', $parts) ?: '0m';
    }

    public function getTotalMinutesAttribute(): int
    {
        return ($this->hours * 60) + $this->minutes;
    }

    // ── Scopes ─────────────────────────────────────────────────────

    public function scopeForUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeThisWeek($query)
    {
        return $query->whereBetween('log_date', [now()->startOfWeek(), now()->endOfWeek()]);
    }

    public function scopeThisMonth($query)
    {
        return $query->whereMonth('log_date', now()->month)
                     ->whereYear('log_date', now()->year);
    }
}
