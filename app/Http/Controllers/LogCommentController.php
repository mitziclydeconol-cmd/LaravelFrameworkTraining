<?php

namespace App\Http\Controllers;

use App\Models\CodingLog;
use App\Models\LogComment;
use Illuminate\Http\Request;

class LogCommentController extends Controller
{
    public function store(Request $request, CodingLog $log)
    {
        // Only the log owner or instructors can comment
        $user = auth()->user();
        if ($log->user_id !== $user->id && !$user->isInstructor()) {
            abort(403);
        }

        $request->validate(['body' => ['required', 'string', 'max:1000']]);

        $log->comments()->create([
            'user_id' => $user->id,
            'body'    => $request->body,
        ]);

        return back()->with('success', 'Comment posted!');
    }

    public function destroy(LogComment $comment)
    {
        $user = auth()->user();
        if ($comment->user_id !== $user->id && !$user->isInstructor()) {
            abort(403);
        }

        $comment->delete();
        return back()->with('success', 'Comment deleted.');
    }
}
