<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Subject extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'code', 'description', 'color', 'created_by'];

    // ── Relationships ──────────────────────────────────────────────

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function students(): BelongsToMany
    {
        return $this->belongsToMany(User::class)->withTimestamps();
    }

    public function codingLogs(): HasMany
    {
        return $this->hasMany(CodingLog::class);
    }

    // ── Helpers ────────────────────────────────────────────────────

    public function getTotalHoursForUser(int $userId): float
    {
        return $this->codingLogs
            ->where('user_id', $userId)
            ->sum(fn ($log) => $log->hours + $log->minutes / 60);
    }
}
