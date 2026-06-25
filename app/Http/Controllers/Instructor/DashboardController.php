<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use App\Models\CodingLog;
use App\Models\Subject;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $studentRole = Role::where('name', 'student')->first();

        // ── Overview Stats ─────────────────────────────────────────
        $totalStudents  = User::where('role_id', $studentRole->id)->count();
        $totalSubjects  = Subject::count();
        $totalLogs      = CodingLog::count();
        $activeThisWeek = CodingLog::thisWeek()->distinct('user_id')->count('user_id');

        // ── Top Active Students This Month ─────────────────────────
        $topStudents = User::where('role_id', $studentRole->id)
            ->withCount(['codingLogs as monthly_logs' => fn ($q) => $q->thisMonth()])
            ->withSum(['codingLogs as monthly_minutes' => fn ($q) => $q->thisMonth()], DB::raw('hours * 60 + minutes'))
            ->having('monthly_logs', '>', 0)
            ->orderByDesc('monthly_logs')
            ->take(5)
            ->get();

        // ── Weekly Activity (all students) ─────────────────────────
        $weeklyData = $this->getSystemWeeklyData();

        // ── Language Distribution ──────────────────────────────────
        $languageStats = CodingLog::select('programming_language', DB::raw('COUNT(*) as count'))
            ->groupBy('programming_language')
            ->orderByDesc('count')
            ->take(8)
            ->get();

        // ── Subject Engagement ─────────────────────────────────────
        $subjectStats = Subject::withCount('codingLogs')
            ->withSum('codingLogs', DB::raw('hours * 60 + minutes'))
            ->orderByDesc('coding_logs_count')
            ->get();

        return view('instructor.dashboard', compact(
            'totalStudents',
            'totalSubjects',
            'totalLogs',
            'activeThisWeek',
            'topStudents',
            'weeklyData',
            'languageStats',
            'subjectStats'
        ));
    }

    private function getSystemWeeklyData(): array
    {
        $logs = CodingLog::whereBetween('log_date', [now()->subDays(6)->startOfDay(), now()->endOfDay()])
            ->select('log_date', DB::raw('COUNT(*) as count'), DB::raw('SUM(hours * 60 + minutes) as total_minutes'))
            ->groupBy('log_date')
            ->get()
            ->keyBy(fn ($item) => $item->log_date->format('Y-m-d'));

        $labels = $counts = $minutes = [];

        for ($i = 6; $i >= 0; $i--) {
            $date     = now()->subDays($i)->format('Y-m-d');
            $labels[] = now()->subDays($i)->format('D, M j');
            $counts[] = $logs[$date]->count ?? 0;
            $minutes[] = round(($logs[$date]->total_minutes ?? 0) / 60, 1);
        }

        return compact('labels', 'counts', 'minutes');
    }
}
