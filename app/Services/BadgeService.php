<?php

namespace App\Services;

use App\Models\Badge;
use App\Models\CodingLog;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class BadgeService
{
    /**
     * Check and award all applicable badges to a user.
     * Call this after every new coding log is saved.
     */
    public function checkAndAward(User $user): array
    {
        $newBadges = [];
        $allBadges = Badge::all();
        $earnedIds = $user->badges->pluck('id')->toArray();

        foreach ($allBadges as $badge) {
            if (in_array($badge->id, $earnedIds)) continue;

            if ($this->qualifies($user, $badge)) {
                $user->badges()->attach($badge->id, ['earned_at' => now()]);
                $newBadges[] = $badge;
            }
        }

        return $newBadges;
    }

    private function qualifies(User $user, Badge $badge): bool
    {
        return match ($badge->type) {
            'logs'      => $this->checkLogs($user, $badge->threshold),
            'hours'     => $this->checkHours($user, $badge->threshold),
            'streak'    => $this->checkStreak($user, $badge->threshold),
            'languages' => $this->checkLanguages($user, $badge->threshold),
            'milestone' => $this->checkMilestone($user, $badge->slug),
            default     => false,
        };
    }

    private function checkLogs(User $user, int $threshold): bool
    {
        return $user->codingLogs()->count() >= $threshold;
    }

    private function checkHours(User $user, int $threshold): bool
    {
        $totalMins = $user->codingLogs()->sum(DB::raw('hours * 60 + minutes'));
        return ($totalMins / 60) >= $threshold;
    }

    private function checkStreak(User $user, int $threshold): bool
    {
        $dates = CodingLog::forUser($user->id)
            ->select('log_date')->distinct()
            ->orderByDesc('log_date')->pluck('log_date');

        if ($dates->isEmpty()) return false;

        $streak  = 1;
        $current = $dates->first();

        foreach ($dates->skip(1) as $date) {
            if ($current->diffInDays($date) === 1) {
                $streak++;
                $current = $date;
                if ($streak >= $threshold) return true;
            } else {
                break;
            }
        }

        return $streak >= $threshold;
    }

    private function checkLanguages(User $user, int $threshold): bool
    {
        return $user->codingLogs()
            ->distinct('programming_language')
            ->count('programming_language') >= $threshold;
    }

    private function checkMilestone(User $user, string $slug): bool
    {
        return match ($slug) {
            'first-log'        => $user->codingLogs()->count() >= 1,
            'night-owl'        => $user->codingLogs()->whereTime('created_at', '>=', '22:00')->exists(),
            'early-bird'       => $user->codingLogs()->whereTime('created_at', '<=', '07:00')->exists(),
            'ai-explorer'      => $user->aiFeedbackLogs()->count() >= 1,
            'goal-crusher'     => $user->codingGoals()->where('is_active', true)->get()
                                       ->filter(fn ($g) => $g->is_completed)->count() >= 1,
            default            => false,
        };
    }
}
