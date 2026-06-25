<?php

namespace App\Http\Controllers;

use App\Models\CodingLog;
use App\Models\Role;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LeaderboardController extends Controller
{
    public function index(Request $request)
    {
        $period     = $request->get('period', 'monthly');
        $subjectId  = $request->get('subject_id');
        $studentRole = Role::where('name', 'student')->first();

        [$startDate, $endDate] = $this->getPeriodDates($period);

        $query = User::where('role_id', $studentRole->id)
            ->withCount(['codingLogs as period_logs' => fn ($q) =>
                $q->whereBetween('log_date', [$startDate, $endDate])
                  ->when($subjectId, fn ($q) => $q->where('subject_id', $subjectId))
            ])
            ->withSum(['codingLogs as period_minutes' => fn ($q) =>
                $q->whereBetween('log_date', [$startDate, $endDate])
                  ->when($subjectId, fn ($q) => $q->where('subject_id', $subjectId))
            ], DB::raw('hours * 60 + minutes'))
            ->having('period_logs', '>', 0)
            ->orderByDesc('period_minutes')
            ->orderByDesc('period_logs');

        $leaderboard = $query->get()->values();
        $subjects    = Subject::orderBy('name')->get();

        // Current user rank
        $currentUserId = auth()->id();
        $myRank = $leaderboard->search(fn ($u) => $u->id === $currentUserId);
        $myRank = $myRank !== false ? $myRank + 1 : null;
        $me     = $leaderboard->firstWhere('id', $currentUserId);

        return view('leaderboard', compact(
            'leaderboard', 'subjects', 'period',
            'subjectId', 'myRank', 'me', 'startDate', 'endDate'
        ));
    }

    private function getPeriodDates(string $period): array
    {
        return match ($period) {
            'weekly'  => [now()->startOfWeek(), now()->endOfWeek()],
            'monthly' => [now()->startOfMonth(), now()->endOfMonth()],
            'alltime' => [now()->subYears(10), now()],
            default   => [now()->startOfMonth(), now()->endOfMonth()],
        };
    }
}
