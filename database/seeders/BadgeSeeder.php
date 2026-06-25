<?php

namespace Database\Seeders;

use App\Models\Badge;
use Illuminate\Database\Seeder;

class BadgeSeeder extends Seeder
{
    public function run(): void
    {
        $badges = [
            // Log count badges
            ['name' => 'First Step',     'slug' => 'first-log',      'description' => 'Logged your very first coding session.',          'icon' => 'bi-star-fill',          'color' => '#F59E0B', 'type' => 'milestone',  'threshold' => 1],
            ['name' => 'Getting Started','slug' => 'logs-5',         'description' => 'Completed 5 coding sessions.',                    'icon' => 'bi-journal-check',      'color' => '#10B981', 'type' => 'logs',       'threshold' => 5],
            ['name' => 'Consistent',     'slug' => 'logs-25',        'description' => 'Logged 25 coding sessions.',                      'icon' => 'bi-trophy',             'color' => '#4F46E5', 'type' => 'logs',       'threshold' => 25],
            ['name' => 'Dedicated',      'slug' => 'logs-50',        'description' => 'Completed 50 coding sessions.',                   'icon' => 'bi-trophy-fill',        'color' => '#7C3AED', 'type' => 'logs',       'threshold' => 50],
            ['name' => 'Century Coder',  'slug' => 'logs-100',       'description' => 'Logged 100 coding sessions. Incredible!',         'icon' => 'bi-award-fill',         'color' => '#DC2626', 'type' => 'logs',       'threshold' => 100],

            // Hours badges
            ['name' => 'Hour Power',     'slug' => 'hours-10',       'description' => 'Coded for a total of 10 hours.',                  'icon' => 'bi-clock-fill',         'color' => '#0891B2', 'type' => 'hours',      'threshold' => 10],
            ['name' => 'Committed',      'slug' => 'hours-50',       'description' => 'Reached 50 total hours of coding.',               'icon' => 'bi-hourglass-split',    'color' => '#D97706', 'type' => 'hours',      'threshold' => 50],
            ['name' => 'Code Warrior',   'slug' => 'hours-100',      'description' => 'Hit the 100-hour milestone!',                     'icon' => 'bi-lightning-fill',     'color' => '#9333EA', 'type' => 'hours',      'threshold' => 100],

            // Streak badges
            ['name' => '3-Day Streak',   'slug' => 'streak-3',       'description' => 'Coded for 3 days in a row.',                      'icon' => 'bi-fire',               'color' => '#F97316', 'type' => 'streak',     'threshold' => 3],
            ['name' => 'Week Warrior',   'slug' => 'streak-7',       'description' => 'Maintained a 7-day coding streak.',               'icon' => 'bi-fire',               'color' => '#EF4444', 'type' => 'streak',     'threshold' => 7],
            ['name' => 'Unstoppable',    'slug' => 'streak-30',      'description' => '30 days straight — phenomenal dedication!',       'icon' => 'bi-rocket-takeoff-fill','color' => '#DC2626', 'type' => 'streak',     'threshold' => 30],

            // Language badges
            ['name' => 'Polyglot',       'slug' => 'languages-3',    'description' => 'Used 3 different programming languages.',         'icon' => 'bi-translate',          'color' => '#16A34A', 'type' => 'languages',  'threshold' => 3],
            ['name' => 'Multilingual',   'slug' => 'languages-5',    'description' => 'Coded in 5 different programming languages.',     'icon' => 'bi-globe',              'color' => '#0891B2', 'type' => 'languages',  'threshold' => 5],

            // Special/milestone badges
            ['name' => 'Night Owl',      'slug' => 'night-owl',      'description' => 'Logged a session after 10 PM.',                   'icon' => 'bi-moon-stars-fill',    'color' => '#4338CA', 'type' => 'milestone',  'threshold' => 1],
            ['name' => 'Early Bird',     'slug' => 'early-bird',     'description' => 'Logged a session before 7 AM.',                   'icon' => 'bi-sunrise-fill',       'color' => '#F59E0B', 'type' => 'milestone',  'threshold' => 1],
            ['name' => 'AI Explorer',    'slug' => 'ai-explorer',    'description' => 'Used AI feedback on your code.',                  'icon' => 'bi-robot',              'color' => '#7C3AED', 'type' => 'milestone',  'threshold' => 1],
            ['name' => 'Goal Crusher',   'slug' => 'goal-crusher',   'description' => 'Completed your first coding goal.',              'icon' => 'bi-bullseye',           'color' => '#16A34A', 'type' => 'milestone',  'threshold' => 1],
        ];

        foreach ($badges as $badge) {
            Badge::firstOrCreate(['slug' => $badge['slug']], $badge);
        }
    }
}
