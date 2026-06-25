<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\CodingGoal;
use Illuminate\Http\Request;

class GoalController extends Controller
{
    public function index()
    {
        $goals   = auth()->user()->codingGoals()->with('subject')->latest()->get();
        $subjects = auth()->user()->subjects;
        return view('student.goals.index', compact('goals', 'subjects'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title'          => ['required', 'string', 'max:150'],
            'description'    => ['nullable', 'string', 'max:500'],
            'subject_id'     => ['nullable', 'exists:subjects,id'],
            'period'         => ['required', 'in:daily,weekly,monthly'],
            'target_hours'   => ['required', 'integer', 'min:0', 'max:999'],
            'target_minutes' => ['required', 'integer', 'min:0', 'max:59'],
            'target_logs'    => ['required', 'integer', 'min:0'],
            'start_date'     => ['required', 'date'],
            'end_date'       => ['required', 'date', 'after_or_equal:start_date'],
        ]);

        auth()->user()->codingGoals()->create($data);

        return back()->with('success', 'Goal created! Keep focused and crush it! 🎯');
    }

    public function update(Request $request, CodingGoal $goal)
    {
        $this->authorizeGoal($goal);

        $goal->update($request->validate([
            'is_active' => ['required', 'boolean'],
        ]));

        return back()->with('success', 'Goal updated.');
    }

    public function destroy(CodingGoal $goal)
    {
        $this->authorizeGoal($goal);
        $goal->delete();
        return back()->with('success', 'Goal removed.');
    }

    private function authorizeGoal(CodingGoal $goal): void
    {
        if ($goal->user_id !== auth()->id()) abort(403);
    }
}
