<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSubjectRequest;
use App\Models\Role;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Http\Request;

class SubjectController extends Controller
{
    public function index()
    {
        $subjects = Subject::withCount(['students', 'codingLogs'])
            ->with('creator')
            ->orderBy('name')
            ->paginate(10);

        return view('subjects.index', compact('subjects'));
    }

    public function create()
    {
        return view('subjects.create');
    }

    public function store(StoreSubjectRequest $request)
    {
        $subject = Subject::create(array_merge(
            $request->validated(),
            ['created_by' => auth()->id()]
        ));

        return redirect()
            ->route('subjects.show', $subject)
            ->with('success', 'Subject created successfully!');
    }

    public function show(Subject $subject)
    {
        $subject->load(['students', 'codingLogs.user', 'creator']);

        $studentRole = Role::where('name', 'student')->first();
        $allStudents = User::where('role_id', $studentRole->id)
            ->whereDoesntHave('subjects', fn ($q) => $q->where('subjects.id', $subject->id))
            ->orderBy('name')
            ->get();

        return view('subjects.show', compact('subject', 'allStudents'));
    }

    public function edit(Subject $subject)
    {
        return view('subjects.edit', compact('subject'));
    }

    public function update(StoreSubjectRequest $request, Subject $subject)
    {
        $subject->update($request->validated());

        return redirect()
            ->route('subjects.show', $subject)
            ->with('success', 'Subject updated successfully!');
    }

    public function destroy(Subject $subject)
    {
        $subject->delete();

        return redirect()
            ->route('subjects.index')
            ->with('success', 'Subject deleted.');
    }

    public function assignStudent(Request $request, Subject $subject)
    {
        $request->validate([
            'student_id' => ['required', 'exists:users,id'],
        ]);

        $subject->students()->syncWithoutDetaching([$request->student_id]);

        return back()->with('success', 'Student enrolled successfully!');
    }

    public function removeStudent(Subject $subject, User $student)
    {
        $subject->students()->detach($student->id);

        return back()->with('success', 'Student removed from subject.');
    }
}
