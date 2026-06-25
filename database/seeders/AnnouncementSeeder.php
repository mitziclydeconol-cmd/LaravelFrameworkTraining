<?php

namespace Database\Seeders;

use App\Models\Announcement;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Database\Seeder;

class AnnouncementSeeder extends Seeder
{
    public function run(): void
    {
        $instructor = User::whereHas('role', fn ($q) => $q->where('name', 'instructor'))->first();
        $subject    = Subject::first();

        $announcements = [
            [
                'title'      => '🎉 Welcome to CodeTrack AI!',
                'body'       => "Welcome to the new semester! Please make sure to log your coding sessions daily. Consistent practice is the key to becoming a great developer. Don't forget to try the AI feedback feature on your code snippets!",
                'priority'   => 'important',
                'is_pinned'  => true,
                'subject_id' => null,
                'expires_at' => null,
            ],
            [
                'title'      => 'OOP Midterm Project Reminder',
                'body'       => "The OOP midterm project is due in 2 weeks. Make sure you are logging your progress in CodeTrack. Your coding logs will be reviewed as part of your project assessment. Aim for at least 5 sessions per week!",
                'priority'   => 'urgent',
                'is_pinned'  => false,
                'subject_id' => $subject?->id,
                'expires_at' => now()->addWeeks(2),
            ],
            [
                'title'      => 'New Feature: AI Study Suggestions',
                'body'       => "You can now get personalized AI-powered study suggestions based on your coding activity! Head to your dashboard and click 'Get AI Suggestions' to receive a customized study plan. This feature analyzes your recent logs, languages used, and progress to give you actionable advice.",
                'priority'   => 'normal',
                'is_pinned'  => false,
                'subject_id' => null,
                'expires_at' => null,
            ],
        ];

        foreach ($announcements as $ann) {
            Announcement::create(array_merge($ann, ['user_id' => $instructor->id]));
        }
    }
}
