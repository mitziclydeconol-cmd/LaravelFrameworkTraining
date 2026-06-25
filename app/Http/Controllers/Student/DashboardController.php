<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\CodingLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user()->load(['subjects', 'codingLogs']);

        // ── Summary Stats ──────────────────────────────────────────
        $totalMinutes  = $user->getTotalMinutesAttribute();
        $totalLogs     = $user->codingLogs->count();
        $weeklyMinutes = $user->codingLogs()->thisWeek()->sum(DB::raw('hours * 60 + minutes'));
        $currentStreak = $this->calculateStreak($user->id);

        // ── Progress per Subject ───────────────────────────────────
        $subjectProgress = $user->subjects->map(function ($subject) use ($user) {
            $logs    = $user->codingLogs->where('subject_id', $subject->id);
            $minutes = $logs->sum(fn ($l) => $l->hours * 60 + $l->minutes);
            return [
                'subject'  => $subject,
                'logs'     => $logs->count(),
                'minutes'  => $minutes,
                'hours'    => round($minutes / 60, 1),
            ];
        })->sortByDesc('minutes');

        // ── Weekly Activity Chart Data (last 7 days) ───────────────
        $weeklyData = $this->getWeeklyActivityData($user->id);

        // ── Language Distribution ──────────────────────────────────
        $languageStats = $user->codingLogs()
            ->select('programming_language', DB::raw('COUNT(*) as count'), DB::raw('SUM(hours * 60 + minutes) as total_minutes'))
            ->groupBy('programming_language')
            ->orderByDesc('total_minutes')
            ->take(6)
            ->get();

        // ── Recent Logs ────────────────────────────────────────────
        $recentLogs = $user->codingLogs()
            ->with('subject')
            ->latest('log_date')
            ->take(5)
            ->get();

        return view('student.dashboard', compact(
            'user',
            'totalMinutes',
            'totalLogs',
            'weeklyMinutes',
            'currentStreak',
            'subjectProgress',
            'weeklyData',
            'languageStats',
            'recentLogs'
        ));
    }

    private function calculateStreak(int $userId): int
    {
        $dates = CodingLog::forUser($userId)
            ->select('log_date')
            ->distinct()
            ->orderByDesc('log_date')
            ->pluck('log_date');

        if ($dates->isEmpty()) return 0;

        $streak  = 0;
        $current = now()->startOfDay();

        foreach ($dates as $date) {
            if ($date->diffInDays($current) <= 1) {
                $streak++;
                $current = $date;
            } else {
                break;
            }
        }

        return $streak;
    }

    private function getWeeklyActivityData(int $userId): array
    {
        $logs = CodingLog::forUser($userId)
            ->whereBetween('log_date', [now()->subDays(6)->startOfDay(), now()->endOfDay()])
            ->select('log_date', DB::raw('SUM(hours * 60 + minutes) as total_minutes'))
            ->groupBy('log_date')
            ->pluck('total_minutes', 'log_date');

        $labels = [];
        $data   = [];

        for ($i = 6; $i >= 0; $i--) {
            $date     = now()->subDays($i)->format('Y-m-d');
            $labels[] = now()->subDays($i)->format('D, M j');
            $data[]   = round(($logs[$date] ?? 0) / 60, 2);
        }

        return ['labels' => $labels, 'data' => $data];
    }
}
