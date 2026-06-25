<?php

use App\Http\Controllers\Instructor;
use App\Http\Controllers\Student;
use App\Http\Controllers\SubjectController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/
Route::get('/', fn () => redirect()->route('login'));

/*
|--------------------------------------------------------------------------
| Auth Routes (Laravel Breeze)
|--------------------------------------------------------------------------
*/
require __DIR__ . '/auth.php';

/*
|--------------------------------------------------------------------------
| Authenticated Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'verified'])->group(function () {

    // Redirect dashboard based on role
    Route::get('/dashboard', function () {
        return auth()->user()->isInstructor()
            ? redirect()->route('instructor.dashboard')
            : redirect()->route('student.dashboard');
    })->name('dashboard');

    /*
    |----------------------------------------------------------------------
    | Student Routes
    |----------------------------------------------------------------------
    */
    Route::middleware('role:student')
        ->prefix('student')
        ->name('student.')
        ->group(function () {

            Route::get('/dashboard', [Student\DashboardController::class, 'index'])
                ->name('dashboard');

            // Coding Logs CRUD
            Route::resource('logs', Student\CodingLogController::class);

            // AI Feedback
            Route::post('/logs/{log}/ai-feedback', [Student\CodingLogController::class, 'getAiFeedback'])
                ->name('logs.ai-feedback');
        });

    /*
    |----------------------------------------------------------------------
    | Instructor Routes
    |----------------------------------------------------------------------
    */
    Route::middleware('role:instructor')
        ->prefix('instructor')
        ->name('instructor.')
        ->group(function () {

            Route::get('/dashboard', [Instructor\DashboardController::class, 'index'])
                ->name('dashboard');

            // Students
            Route::get('/students', [Instructor\StudentController::class, 'index'])
                ->name('students.index');
            Route::get('/students/{student}', [Instructor\StudentController::class, 'show'])
                ->name('students.show');

            // Exports
            Route::get('/export/all', [Instructor\StudentController::class, 'exportCsv'])
                ->name('export.all');
            Route::get('/students/{student}/export', [Instructor\StudentController::class, 'exportCsv'])
                ->name('students.export');
        });

    /*
    |----------------------------------------------------------------------
    | Subject Routes (Instructor only for write ops)
    |----------------------------------------------------------------------
    */
    Route::middleware('role:instructor')
        ->group(function () {
            Route::resource('subjects', SubjectController::class);
            Route::post('/subjects/{subject}/students', [SubjectController::class, 'assignStudent'])
                ->name('subjects.assign-student');
            Route::delete('/subjects/{subject}/students/{student}', [SubjectController::class, 'removeStudent'])
                ->name('subjects.remove-student');
        });
});
