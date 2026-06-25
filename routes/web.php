<?php

use App\Http\Controllers\Instructor;
use App\Http\Controllers\Student;
use App\Http\Controllers\SubjectController;
use App\Http\Controllers\LeaderboardController;
use App\Http\Controllers\LogCommentController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => redirect()->route('login'));

require __DIR__ . '/auth.php';

Route::middleware(['auth', 'verified'])->group(function () {

    Route::get('/dashboard', function () {
        return auth()->user()->isInstructor()
            ? redirect()->route('instructor.dashboard')
            : redirect()->route('student.dashboard');
    })->name('dashboard');

    // Leaderboard — visible to all authenticated users
    Route::get('/leaderboard', [LeaderboardController::class, 'index'])->name('leaderboard');

    // Comments — students on own logs, instructors on any
    Route::post('/logs/{log}/comments', [LogCommentController::class, 'store'])->name('logs.comments.store');
    Route::delete('/comments/{comment}', [LogCommentController::class, 'destroy'])->name('logs.comments.destroy');

    /*
    |----------------------------------------------------------------------
    | Student Routes
    |----------------------------------------------------------------------
    */
    Route::middleware('role:student')->prefix('student')->name('student.')->group(function () {

        Route::get('/dashboard', [Student\DashboardController::class, 'index'])->name('dashboard');

        // Coding Logs CRUD
        Route::resource('logs', Student\CodingLogController::class);
        Route::post('/logs/{log}/ai-feedback', [Student\CodingLogController::class, 'getAiFeedback'])->name('logs.ai-feedback');

        // Self Assessment
        Route::post('/logs/{log}/assessment', [Student\SelfAssessmentController::class, 'store'])->name('logs.assessment.store');

        // Goals
        Route::get('/goals', [Student\GoalController::class, 'index'])->name('goals.index');
        Route::post('/goals', [Student\GoalController::class, 'store'])->name('goals.store');
        Route::patch('/goals/{goal}', [Student\GoalController::class, 'update'])->name('goals.update');
        Route::delete('/goals/{goal}', [Student\GoalController::class, 'destroy'])->name('goals.destroy');

        // Activity Heatmap
        Route::get('/heatmap', [Student\HeatmapController::class, 'index'])->name('heatmap');

        // Badges
        Route::get('/badges', [Student\BadgeController::class, 'index'])->name('badges');

        // AI Study Suggestions
        Route::get('/suggestions', [Student\StudySuggestionController::class, 'index'])->name('suggestions.index');
        Route::post('/suggestions/generate', [Student\StudySuggestionController::class, 'generate'])->name('suggestions.generate');
    });

    /*
    |----------------------------------------------------------------------
    | Instructor Routes
    |----------------------------------------------------------------------
    */
    Route::middleware('role:instructor')->prefix('instructor')->name('instructor.')->group(function () {

        Route::get('/dashboard', [Instructor\DashboardController::class, 'index'])->name('dashboard');

        // Students
        Route::get('/students', [Instructor\StudentController::class, 'index'])->name('students.index');
        Route::get('/students/{student}', [Instructor\StudentController::class, 'show'])->name('students.show');

        // CSV Exports
        Route::get('/export/all', [Instructor\StudentController::class, 'exportCsv'])->name('export.all');
        Route::get('/students/{student}/export', [Instructor\StudentController::class, 'exportCsv'])->name('students.export');

        // PDF Reports
        Route::get('/students/{student}/pdf', [Instructor\PdfExportController::class, 'studentReport'])->name('students.pdf');
        Route::get('/reports/all-pdf', [Instructor\PdfExportController::class, 'allStudentsReport'])->name('reports.all-pdf');

        // Announcements
        Route::get('/announcements', [Instructor\AnnouncementController::class, 'index'])->name('announcements.index');
        Route::post('/announcements', [Instructor\AnnouncementController::class, 'store'])->name('announcements.store');
        Route::delete('/announcements/{announcement}', [Instructor\AnnouncementController::class, 'destroy'])->name('announcements.destroy');
        Route::patch('/announcements/{announcement}/pin', [Instructor\AnnouncementController::class, 'togglePin'])->name('announcements.pin');
    });

    /*
    |----------------------------------------------------------------------
    | Subjects (Instructor only)
    |----------------------------------------------------------------------
    */
    Route::middleware('role:instructor')->group(function () {
        Route::resource('subjects', SubjectController::class);
        Route::post('/subjects/{subject}/students', [SubjectController::class, 'assignStudent'])->name('subjects.assign-student');
        Route::delete('/subjects/{subject}/students/{student}', [SubjectController::class, 'removeStudent'])->name('subjects.remove-student');
    });
});
