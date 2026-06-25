<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use App\Models\CodingLog;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PdfExportController extends Controller
{
    /**
     * Generate an HTML-based printable/PDF report.
     * Uses browser print-to-PDF (no extra package needed).
     */
    public function studentReport(User $student)
    {
        if (!$student->isStudent()) abort(404);

        $student->load(['subjects', 'codingLogs.subject', 'badges', 'selfAssessments']);

        $totalMinutes  = $student->codingLogs->sum(fn ($l) => $l->hours * 60 + $l->minutes);
        $totalLogs     = $student->codingLogs->count();
        $weeklyMinutes = $student->codingLogs()->thisWeek()->sum(DB::raw('hours * 60 + minutes'));

        $subjectProgress = $student->subjects->map(function ($subject) use ($student) {
            $logs    = $student->codingLogs->where('subject_id', $subject->id);
            $minutes = $logs->sum(fn ($l) => $l->hours * 60 + $l->minutes);
            return [
                'subject' => $subject,
                'logs'    => $logs->count(),
                'minutes' => $minutes,
                'hours'   => round($minutes / 60, 1),
            ];
        });

        $languageStats = $student->codingLogs()
            ->select('programming_language', DB::raw('COUNT(*) as count'), DB::raw('SUM(hours*60+minutes) as total_minutes'))
            ->groupBy('programming_language')
            ->orderByDesc('total_minutes')
            ->get();

        $recentLogs = $student->codingLogs()
            ->with(['subject', 'selfAssessment'])
            ->latest('log_date')
            ->take(20)
            ->get();

        $avgAssessment = $student->selfAssessments->count()
            ? round($student->selfAssessments->avg(fn ($a) => ($a->understanding + $a->confidence + $a->effort) / 3), 1)
            : null;

        return view('instructor.reports.student-pdf', compact(
            'student', 'totalMinutes', 'totalLogs', 'weeklyMinutes',
            'subjectProgress', 'languageStats', 'recentLogs', 'avgAssessment'
        ));
    }

    public function allStudentsReport()
    {
        $studentRole = Role::where('name', 'student')->first();
        $students = User::where('role_id', $studentRole->id)
            ->withCount('codingLogs')
            ->withSum('codingLogs', DB::raw('hours * 60 + minutes'))
            ->with(['subjects', 'badges'])
            ->orderBy('name')
            ->get();

        $totalLogs    = CodingLog::count();
        $totalMinutes = CodingLog::sum(DB::raw('hours * 60 + minutes'));

        return view('instructor.reports.all-students-pdf', compact(
            'students', 'totalLogs', 'totalMinutes'
        ));
    }
}
