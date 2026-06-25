<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use App\Models\Subject;
use Illuminate\Http\Request;

class AnnouncementController extends Controller
{
    public function index()
    {
        $announcements = Announcement::with(['instructor', 'subject'])
            ->latest()
            ->paginate(15);

        $subjects = Subject::orderBy('name')->get();

        return view('instructor.announcements.index', compact('announcements', 'subjects'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title'      => ['required', 'string', 'max:200'],
            'body'       => ['required', 'string', 'max:5000'],
            'subject_id' => ['nullable', 'exists:subjects,id'],
            'priority'   => ['required', 'in:normal,important,urgent'],
            'is_pinned'  => ['boolean'],
            'expires_at' => ['nullable', 'date', 'after:now'],
        ]);

        $data['user_id']   = auth()->id();
        $data['is_pinned'] = $request->boolean('is_pinned');

        Announcement::create($data);

        return back()->with('success', 'Announcement posted successfully!');
    }

    public function destroy(Announcement $announcement)
    {
        $announcement->delete();
        return back()->with('success', 'Announcement deleted.');
    }

    public function togglePin(Announcement $announcement)
    {
        $announcement->update(['is_pinned' => !$announcement->is_pinned]);
        return back()->with('success', $announcement->is_pinned ? 'Pinned!' : 'Unpinned.');
    }
}
