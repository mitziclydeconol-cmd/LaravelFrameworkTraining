<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\CodingLog;
use Illuminate\Support\Facades\DB;

class HeatmapController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        // Build full year of activity data
        $logs = CodingLog::forUser($user->id)
            ->where('log_date', '>=', now()->subYear())
            ->select('log_date', DB::raw('COUNT(*) as count'), DB::raw('SUM(hours * 60 + minutes) as total_minutes'))
            ->groupBy('log_date')
            ->get()
            ->keyBy(fn ($item) => $item->log_date->format('Y-m-d'));

        // Build 52-week grid
        $weeks = [];
        $start = now()->subWeeks(51)->startOfWeek();

        for ($w = 0; $w < 52; $w++) {
            $week = [];
            for ($d = 0; $d < 7; $d++) {
                $date    = $start->copy()->addDays($w * 7 + $d);
                $dateStr = $date->format('Y-m-d');
                $entry   = $logs[$dateStr] ?? null;
                $mins    = $entry?->total_minutes ?? 0;

                $week[] = [
                    'date'    => $dateStr,
                    'label'   => $date->format('M j, Y'),
                    'count'   => $entry?->count ?? 0,
                    'minutes' => $mins,
                    'level'   => match (true) {
                        $mins >= 120 => 4,
                        $mins >= 60  => 3,
                        $mins >= 30  => 2,
                        $mins > 0   => 1,
                        default      => 0,
                    },
                ];
            }
            $weeks[] = $week;
        }

        // Month labels for the heatmap x-axis
        $monthLabels = [];
        for ($w = 0; $w < 52; $w++) {
            $date = $start->copy()->addWeeks($w);
            if ($w === 0 || $date->day <= 7) {
                $monthLabels[$w] = $date->format('M');
            } else {
                $monthLabels[$w] = '';
            }
        }

        $totalDaysActive = collect($weeks)->flatten(1)->filter(fn ($d) => $d['count'] > 0)->count();
        $longestStreak   = $this->calculateLongestStreak($user->id);
        $currentStreak   = $this->calculateCurrentStreak($user->id);

        return view('student.heatmap', compact(
            'weeks', 'monthLabels', 'totalDaysActive', 'longestStreak', 'currentStreak'
        ));
    }

    private function calculateCurrentStreak(int $userId): int
    {
        $dates = CodingLog::forUser($userId)
            ->select('log_date')->distinct()->orderByDesc('log_date')->pluck('log_date');

        if ($dates->isEmpty()) return 0;
        $streak = 0;
        $current = now()->startOfDay();

        foreach ($dates as $date) {
            if ($date->diffInDays($current) <= 1) { $streak++; $current = $date; }
            else break;
        }
        return $streak;
    }

    private function calculateLongestStreak(int $userId): int
    {
        $dates = CodingLog::forUser($userId)
            ->select('log_date')->distinct()->orderBy('log_date')->pluck('log_date');

        if ($dates->isEmpty()) return 0;
        $longest = 1; $current = 1;

        foreach ($dates->skip(1)->values() as $i => $date) {
            if ($date->diffInDays($dates[$i]) === 1) {
                $current++;
                $longest = max($longest, $current);
            } else {
                $current = 1;
            }
        }
        return $longest;
    }
}
