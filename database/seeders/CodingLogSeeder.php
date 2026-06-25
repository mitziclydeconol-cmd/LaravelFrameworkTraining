<?php

namespace Database\Seeders;

use App\Models\CodingLog;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Database\Seeder;

class CodingLogSeeder extends Seeder
{
    public function run(): void
    {
        $students = User::whereHas('role', fn ($q) => $q->where('name', 'student'))->get();
        $subjects = Subject::all();

        $languages   = ['PHP', 'JavaScript', 'Python', 'Java', 'C++', 'TypeScript', 'SQL'];
        $difficulties = ['easy', 'medium', 'hard'];

        $sampleLogs = [
            ['title' => 'Implemented Linked List from scratch', 'description' => 'Built a singly linked list with insert, delete, and traverse operations.'],
            ['title' => 'Built REST API with Laravel',          'description' => 'Created CRUD endpoints for a Todo application.'],
            ['title' => 'Solved binary search problem',         'description' => 'Implemented binary search on a sorted array with recursion.'],
            ['title' => 'Designed ER diagram for library system', 'description' => 'Created entity-relationship diagram with 8 entities.'],
            ['title' => 'Created login form with validation',   'description' => 'HTML form with JavaScript client-side validation.'],
            ['title' => 'Implemented bubble sort algorithm',    'description' => 'Coded and analyzed time complexity of bubble sort.'],
            ['title' => 'OOP polymorphism exercise',            'description' => 'Created Animal class hierarchy demonstrating polymorphism.'],
            ['title' => 'SQL joins practice',                   'description' => 'Practiced INNER, LEFT, RIGHT and FULL OUTER joins.'],
        ];

        foreach ($students as $student) {
            $assignedSubjects = $student->subjects;

            for ($i = 0; $i < 15; $i++) {
                $sample  = $sampleLogs[array_rand($sampleLogs)];
                $subject = $assignedSubjects->count() ? $assignedSubjects->random() : null;
                $daysAgo = rand(0, 60);

                CodingLog::create([
                    'user_id'              => $student->id,
                    'subject_id'           => $subject?->id,
                    'title'                => $sample['title'],
                    'description'          => $sample['description'],
                    'programming_language' => $languages[array_rand($languages)],
                    'hours'                => rand(0, 3),
                    'minutes'              => [0, 15, 30, 45][array_rand([0, 15, 30, 45])],
                    'log_date'             => now()->subDays($daysAgo)->format('Y-m-d'),
                    'difficulty'           => $difficulties[array_rand($difficulties)],
                    'code_snippet'         => "// Sample code\nfunction example() {\n    return 'Hello World';\n}",
                ]);
            }
        }
    }
}
