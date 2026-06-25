<?php

namespace App\Services;

use App\Models\StudySuggestion;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class StudySuggestionService
{
    private string $apiUrl   = 'https://api.anthropic.com/v1/messages';
    private string $model    = 'claude-sonnet-4-6';

    public function generate(User $user): array
    {
        $user->load(['codingLogs' => fn ($q) => $q->latest('log_date')->take(20), 'subjects', 'badges']);

        $prompt = $this->buildPrompt($user);

        try {
            $response = Http::withHeaders([
                'Content-Type'      => 'application/json',
                'anthropic-version' => '2023-06-01',
            ])->post($this->apiUrl, [
                'model'      => $this->model,
                'max_tokens' => 1200,
                'messages'   => [['role' => 'user', 'content' => $prompt]],
            ]);

            if ($response->successful()) {
                $data       = $response->json();
                $suggestion = $data['content'][0]['text'] ?? '';
                $tokens     = $data['usage']['output_tokens'] ?? 0;

                StudySuggestion::create([
                    'user_id'      => $user->id,
                    'suggestion'   => $suggestion,
                    'model_used'   => $this->model,
                    'tokens_used'  => $tokens,
                    'generated_at' => now(),
                ]);

                return ['success' => true, 'suggestion' => $suggestion];
            }

            return ['success' => false, 'message' => 'AI service request failed.'];

        } catch (\Exception $e) {
            Log::error('StudySuggestionService: ' . $e->getMessage());
            return ['success' => false, 'message' => 'AI service temporarily unavailable.'];
        }
    }

    private function buildPrompt(User $user): string
    {
        $totalLogs  = $user->codingLogs->count();
        $totalMins  = $user->codingLogs->sum(fn ($l) => $l->hours * 60 + $l->minutes);
        $totalHours = round($totalMins / 60, 1);
        $subjects   = $user->subjects->pluck('name')->join(', ') ?: 'None enrolled';
        $badges     = $user->badges->pluck('name')->join(', ') ?: 'None yet';

        $recentActivity = $user->codingLogs->take(10)->map(function ($log) {
            return "- [{$log->log_date->format('M j')}] {$log->title} ({$log->programming_language}, {$log->duration}, difficulty: {$log->difficulty})";
        })->join("\n");

        $languageBreakdown = $user->codingLogs
            ->groupBy('programming_language')
            ->map(fn ($logs, $lang) => "$lang: {$logs->count()} logs")
            ->values()->join(', ');

        return <<<PROMPT
You are a personalized coding mentor for an IT student. Analyze their activity and provide tailored study suggestions.

**Student Profile:**
- Name: {$user->name}
- Enrolled Subjects: {$subjects}
- Total Coding Sessions: {$totalLogs}
- Total Coding Time: {$totalHours} hours
- Badges Earned: {$badges}
- Languages Used: {$languageBreakdown}

**Recent Activity (last 10 sessions):**
{$recentActivity}

Based on this data, provide a personalized study plan with:

1. **Strengths** – What they're doing well (2-3 points)
2. **Focus Areas** – Skills or topics they should improve (2-3 points)
3. **This Week's Goals** – 3 specific, actionable tasks they can complete
4. **Recommended Resources** – 2-3 specific topics to study or practice
5. **Motivational Note** – A short encouraging message personalized to their progress

Keep the tone supportive, specific, and practical for an IT student. Format clearly with headers.
PROMPT;
    }
}
