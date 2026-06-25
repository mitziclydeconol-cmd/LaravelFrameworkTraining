<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $studentRole    = Role::where('name', 'student')->first();
        $instructorRole = Role::where('name', 'instructor')->first();

        // Create default instructor
        User::firstOrCreate(
            ['email' => 'instructor@codetrack.dev'],
            [
                'name'     => 'Prof. Maria Santos',
                'password' => Hash::make('password'),
                'role_id'  => $instructorRole->id,
            ]
        );

        // Create demo students
        $students = [
            ['name' => 'Juan Dela Cruz',  'email' => 'juan@student.dev',  'student_id' => 'IT-2024-001'],
            ['name' => 'Ana Reyes',       'email' => 'ana@student.dev',   'student_id' => 'IT-2024-002'],
            ['name' => 'Carlos Mendoza',  'email' => 'carlos@student.dev','student_id' => 'IT-2024-003'],
        ];

        foreach ($students as $student) {
            User::firstOrCreate(
                ['email' => $student['email']],
                array_merge($student, [
                    'password' => Hash::make('password'),
                    'role_id'  => $studentRole->id,
                ])
            );
        }
    }
}
