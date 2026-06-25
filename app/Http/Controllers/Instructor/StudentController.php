<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use App\Models\CodingLog;
use App\Models\Role;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\StreamedResponse;

class StudentController extends Controller
{
    public function index(Request $request)
    {
        $studentRole = Role::where('name', 'student')->first();

        $query = User::where('role_id', $studentRole->id)
            ->withCount('codingLogs')
            ->withSum('codingLogs', DB::raw('hours * 60 + minutes'))
            ->with('subjects');

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%')
                  ->orWhere('student_id', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->filled('subject_id')) {
            $query->whereHas('subjects', fn ($q) => $q->where('subjects.id', $request->subject_id));
        }

        $students = $query->latest()->paginate(15)->withQueryString();
        $subjects = Subject::orderBy('name')->get();

        return view('instructor.students.index', compact('students', 'subjects'));
    }

    public function show(User $student)
    {
        $this->ensureIsStudent($student);
        $student->load(['subjects', 'codingLogs.subject']);

        // ── Stats ──────────────────────────────────────────────────
        $totalMinutes  = $student->getTotalMinutesAttribute();
        $totalLogs     = $student->codingLogs->count();
        $weeklyMinutes = $student->codingLogs()->thisWeek()->sum(DB::raw('hours * 60 + minutes'));

        // ── Per Subject Progress ───────────────────────────────────
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

        // ── Recent Logs ────────────────────────────────────────────
        $recentLogs = $student->codingLogs()
            ->with('subject')
            ->latest('log_date')
            ->take(10)
            ->get();

        // ── Language Stats ─────────────────────────────────────────
        $languageStats = $student->codingLogs()
            ->select('programming_language', DB::raw('COUNT(*) as count'))
            ->groupBy('programming_language')
            ->orderByDesc('count')
            ->get();

        return view('instructor.students.show', compact(
            'student',
            'totalMinutes',
            'totalLogs',
            'weeklyMinutes',
            'subjectProgress',
            'recentLogs',
            'languageStats'
        ));
    }

    public function exportCsv(User $student = null): StreamedResponse
    {
        $headers = [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => 'attachment; filename="codetrack-report-' . now()->format('Y-m-d') . '.csv"',
        ];

        $query = CodingLog::with(['user', 'subject'])
            ->orderBy('log_date', 'desc');

        if ($student) {
            $this->ensureIsStudent($student);
            $query->where('user_id', $student->id);
        }

        $callback = function () use ($query) {
            $handle = fopen('php://output', 'w');

            // CSV Headers
            fputcsv($handle, [
                'Student Name', 'Student ID', 'Email',
                'Date', 'Title', 'Subject', 'Language',
                'Hours', 'Minutes', 'Total Minutes', 'Difficulty',
                'Description',
            ]);

            $query->chunk(200, function ($logs) use ($handle) {
                foreach ($logs as $log) {
                    fputcsv($handle, [
                        $log->user->name,
                        $log->user->student_id ?? 'N/A',
                        $log->user->email,
                        $log->log_date->format('Y-m-d'),
                        $log->title,
                        $log->subject?->name ?? 'N/A',
                        $log->programming_language,
                        $log->hours,
                        $log->minutes,
                        $log->hours * 60 + $log->minutes,
                        $log->difficulty,
                        $log->description ?? '',
                    ]);
                }
            });

            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }

    private function ensureIsStudent(User $user): void
    {
        if (! $user->isStudent()) {
            abort(404);
        }
    }
}
