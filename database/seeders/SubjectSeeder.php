<?php

namespace Database\Seeders;

use App\Models\Subject;
use App\Models\User;
use Illuminate\Database\Seeder;

class SubjectSeeder extends Seeder
{
    public function run(): void
    {
        $instructor = User::whereHas('role', fn ($q) => $q->where('name', 'instructor'))->first();

        $subjects = [
            ['name' => 'Object-Oriented Programming',   'code' => 'OOP101',  'color' => '#4F46E5', 'description' => 'Fundamentals of OOP using Java or C++'],
            ['name' => 'Web Development',               'code' => 'WEB201',  'color' => '#0891B2', 'description' => 'HTML, CSS, JavaScript, and PHP basics'],
            ['name' => 'Data Structures & Algorithms',  'code' => 'DSA301',  'color' => '#D97706', 'description' => 'Arrays, linked lists, trees, sorting, and searching'],
            ['name' => 'Database Management',           'code' => 'DBM201',  'color' => '#16A34A', 'description' => 'SQL, ERD design, normalization'],
            ['name' => 'Mobile Development',            'code' => 'MOB401',  'color' => '#DC2626', 'description' => 'Android/iOS development basics'],
            ['name' => 'Software Engineering',          'code' => 'SE301',   'color' => '#7C3AED', 'description' => 'SDLC, design patterns, project management'],
        ];

        foreach ($subjects as $subjectData) {
            Subject::firstOrCreate(
                ['code' => $subjectData['code']],
                array_merge($subjectData, ['created_by' => $instructor->id])
            );
        }

        // Assign all students to the first 3 subjects
        $students = User::whereHas('role', fn ($q) => $q->where('name', 'student'))->get();
        $firstThreeSubjects = Subject::take(3)->get();

        foreach ($students as $student) {
            $student->subjects()->syncWithoutDetaching($firstThreeSubjects->pluck('id'));
        }
    }
}
