<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name', 'email', 'password', 'role_id', 'student_id', 'avatar',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
        ];
    }

    // ── Relationships ──────────────────────────────────────────────
    public function role(): BelongsTo { return $this->belongsTo(Role::class); }
    public function subjects(): BelongsToMany { return $this->belongsToMany(Subject::class)->withTimestamps(); }
    public function codingLogs(): HasMany { return $this->hasMany(CodingLog::class); }
    public function aiFeedbackLogs(): HasMany { return $this->hasMany(AiFeedbackLog::class); }
    public function codingGoals(): HasMany { return $this->hasMany(CodingGoal::class); }
    public function studySuggestions(): HasMany { return $this->hasMany(StudySuggestion::class); }
    public function logComments(): HasMany { return $this->hasMany(LogComment::class); }
    public function selfAssessments(): HasMany { return $this->hasMany(SelfAssessment::class); }
    public function announcements(): HasMany { return $this->hasMany(Announcement::class); }

    public function badges(): BelongsToMany
    {
        return $this->belongsToMany(Badge::class, 'user_badges')
                    ->withPivot('earned_at')
                    ->withTimestamps();
    }

    // ── Role Helpers ───────────────────────────────────────────────
    public function isStudent(): bool { return $this->role->name === Role::STUDENT; }
    public function isInstructor(): bool { return $this->role->name === Role::INSTRUCTOR; }

    // ── Computed Accessors ─────────────────────────────────────────
    public function getTotalMinutesAttribute(): int
    {
        return $this->codingLogs->sum(fn ($l) => $l->hours * 60 + $l->minutes);
    }

    public function getInitialsAttribute(): string
    {
        return strtoupper(substr($this->name, 0, 2));
    }
}
