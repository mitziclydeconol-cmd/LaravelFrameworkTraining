<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Announcement extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'subject_id', 'title', 'body',
        'priority', 'is_pinned', 'expires_at',
    ];

    protected function casts(): array
    {
        return [
            'is_pinned'  => 'boolean',
            'expires_at' => 'datetime',
        ];
    }

    public function instructor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    public function getIsExpiredAttribute(): bool
    {
        return $this->expires_at && $this->expires_at->isPast();
    }

    public function getPriorityColorAttribute(): string
    {
        return match ($this->priority) {
            'urgent'    => '#DC2626',
            'important' => '#D97706',
            default     => '#4F46E5',
        };
    }

    public function getPriorityIconAttribute(): string
    {
        return match ($this->priority) {
            'urgent'    => 'bi-exclamation-triangle-fill',
            'important' => 'bi-exclamation-circle-fill',
            default     => 'bi-megaphone-fill',
        };
    }

    public function scopeActive($query)
    {
        return $query->where(function ($q) {
            $q->whereNull('expires_at')->orWhere('expires_at', '>', now());
        });
    }

    public function scopeForStudent($query, User $student)
    {
        $subjectIds = $student->subjects->pluck('id');
        return $query->where(function ($q) use ($subjectIds) {
            $q->whereNull('subject_id')
              ->orWhereIn('subject_id', $subjectIds);
        });
    }
}
