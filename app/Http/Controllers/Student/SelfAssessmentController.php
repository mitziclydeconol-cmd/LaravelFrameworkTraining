<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\CodingLog;
use App\Models\SelfAssessment;
use Illuminate\Http\Request;

class SelfAssessmentController extends Controller
{
    public function store(Request $request, CodingLog $log)
    {
        if ($log->user_id !== auth()->id()) abort(403);

        $data = $request->validate([
            'understanding' => ['required', 'integer', 'min:1', 'max:5'],
            'confidence'    => ['required', 'integer', 'min:1', 'max:5'],
            'effort'        => ['required', 'integer', 'min:1', 'max:5'],
            'reflection'    => ['nullable', 'string', 'max:1000'],
            'next_steps'    => ['nullable', 'string', 'max:500'],
        ]);

        SelfAssessment::updateOrCreate(
            ['coding_log_id' => $log->id, 'user_id' => auth()->id()],
            $data
        );

        return back()->with('success', 'Self-assessment saved! Great self-reflection! 🌟');
    }
}
