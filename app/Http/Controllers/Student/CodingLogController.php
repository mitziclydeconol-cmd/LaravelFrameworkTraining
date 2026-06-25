<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCodingLogRequest;
use App\Models\CodingLog;
use App\Models\Subject;
use App\Services\AiFeedbackService;
use Illuminate\Http\Request;

class CodingLogController extends Controller
{
    public function __construct(private AiFeedbackService $aiService) {}

    public function index(Request $request)
    {
        $user  = auth()->user();
        $query = $user->codingLogs()->with('subject')->latest('log_date');

        // Filters
        if ($request->filled('subject_id')) {
            $query->where('subject_id', $request->subject_id);
        }
        if ($request->filled('language')) {
            $query->where('programming_language', $request->language);
        }
        if ($request->filled('search')) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }

        $logs      = $query->paginate(10)->withQueryString();
        $subjects  = $user->subjects;
        $languages = $user->codingLogs()->distinct()->pluck('programming_language');

        return view('student.logs.index', compact('logs', 'subjects', 'languages'));
    }

    public function create()
    {
        $subjects  = auth()->user()->subjects;
        $languages = $this->getLanguages();

        return view('student.logs.create', compact('subjects', 'languages'));
    }

    public function store(StoreCodingLogRequest $request)
    {
        $log = auth()->user()->codingLogs()->create($request->validated());

        return redirect()
            ->route('student.logs.show', $log)
            ->with('success', 'Coding log created successfully! Keep up the great work! 🎉');
    }

    public function show(CodingLog $log)
    {
        $this->authorizeLog($log);
        $log->load(['subject', 'aiFeedbackLogs' => fn ($q) => $q->latest()->take(3)]);

        return view('student.logs.show', compact('log'));
    }

    public function edit(CodingLog $log)
    {
        $this->authorizeLog($log);
        $subjects  = auth()->user()->subjects;
        $languages = $this->getLanguages();

        return view('student.logs.edit', compact('log', 'subjects', 'languages'));
    }

    public function update(StoreCodingLogRequest $request, CodingLog $log)
    {
        $this->authorizeLog($log);
        $log->update($request->validated());

        return redirect()
            ->route('student.logs.show', $log)
            ->with('success', 'Coding log updated successfully!');
    }

    public function destroy(CodingLog $log)
    {
        $this->authorizeLog($log);
        $log->delete();

        return redirect()
            ->route('student.logs.index')
            ->with('success', 'Coding log deleted.');
    }

    public function getAiFeedback(CodingLog $log)
    {
        $this->authorizeLog($log);

        $result = $this->aiService->generateFeedback($log);

        if ($result['success']) {
            return back()->with('ai_feedback', $result['feedback']);
        }

        return back()->with('error', $result['message']);
    }

    private function authorizeLog(CodingLog $log): void
    {
        if ($log->user_id !== auth()->id()) {
            abort(403, 'You can only access your own logs.');
        }
    }

    private function getLanguages(): array
    {
        return [
            'PHP', 'JavaScript', 'TypeScript', 'Python', 'Java', 'C', 'C++',
            'C#', 'Ruby', 'Go', 'Rust', 'Swift', 'Kotlin', 'SQL', 'HTML/CSS',
            'Bash', 'R', 'MATLAB', 'Other',
        ];
    }
}
