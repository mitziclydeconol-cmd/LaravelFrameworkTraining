<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CodingGoal extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'subject_id', 'title', 'description',
        'period', 'target_hours', 'target_minutes',
        'target_logs', 'start_date', 'end_date', 'is_active',
    ];

    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date'   => 'date',
            'is_active'  => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    public function getTargetTotalMinutesAttribute(): int
    {
        return ($this->target_hours * 60) + $this->target_minutes;
    }

    public function getProgressMinutesAttribute(): int
    {
        $query = CodingLog::where('user_id', $this->user_id)
            ->whereBetween('log_date', [$this->start_date, $this->end_date]);

        if ($this->subject_id) {
            $query->where('subject_id', $this->subject_id);
        }

        return $query->sum(\Illuminate\Support\Facades\DB::raw('hours * 60 + minutes'));
    }

    public function getProgressLogsAttribute(): int
    {
        $query = CodingLog::where('user_id', $this->user_id)
            ->whereBetween('log_date', [$this->start_date, $this->end_date]);

        if ($this->subject_id) {
            $query->where('subject_id', $this->subject_id);
        }

        return $query->count();
    }

    public function getTimeProgressPctAttribute(): int
    {
        if ($this->target_total_minutes === 0) return 0;
        return min(100, round($this->progress_minutes / $this->target_total_minutes * 100));
    }

    public function getLogsProgressPctAttribute(): int
    {
        if ($this->target_logs === 0) return 0;
        return min(100, round($this->progress_logs / $this->target_logs * 100));
    }

    public function getIsExpiredAttribute(): bool
    {
        return $this->end_date->isPast();
    }

    public function getIsCompletedAttribute(): bool
    {
        $timeOk = $this->target_total_minutes === 0 || $this->progress_minutes >= $this->target_total_minutes;
        $logsOk = $this->target_logs === 0 || $this->progress_logs >= $this->target_logs;
        return $timeOk && $logsOk;
    }
}
