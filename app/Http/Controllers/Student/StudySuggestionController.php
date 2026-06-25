<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Services\StudySuggestionService;

class StudySuggestionController extends Controller
{
    public function __construct(private StudySuggestionService $service) {}

    public function index()
    {
        $user        = auth()->user();
        $suggestions = $user->studySuggestions()->latest()->take(5)->get();
        $latest      = $suggestions->first();

        return view('student.suggestions', compact('suggestions', 'latest'));
    }

    public function generate()
    {
        $result = $this->service->generate(auth()->user());

        if ($result['success']) {
            return redirect()->route('student.suggestions.index')
                ->with('success', 'AI study plan generated! 🤖');
        }

        return back()->with('error', $result['message']);
    }
}
