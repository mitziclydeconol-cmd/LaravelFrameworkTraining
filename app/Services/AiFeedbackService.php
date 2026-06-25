<?php

namespace App\Services;

use App\Models\AiFeedbackLog;
use App\Models\CodingLog;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AiFeedbackService
{
    private string $apiUrl   = 'https://api.anthropic.com/v1/messages';
    private string $model    = 'claude-sonnet-4-6';
    private int    $maxTokens = 1024;

    /**
     * Generate AI feedback for a coding log entry.
     */
    public function generateFeedback(CodingLog $codingLog): array
    {
        if (empty($codingLog->code_snippet)) {
            return [
                'success' => false,
                'message' => 'No code snippet provided for feedback.',
            ];
        }

        $prompt = $this->buildPrompt($codingLog);

        // Log the request
        $feedbackLog = AiFeedbackLog::create([
            'coding_log_id' => $codingLog->id,
            'user_id'       => $codingLog->user_id,
            'prompt_sent'   => $prompt,
            'model_used'    => $this->model,
            'status'        => 'pending',
        ]);

        try {
            $response = Http::withHeaders([
                'Content-Type'      => 'application/json',
                'anthropic-version' => '2023-06-01',
            ])->post($this->apiUrl, [
                'model'      => $this->model,
                'max_tokens' => $this->maxTokens,
                'messages'   => [
                    [
                        'role'    => 'user',
                        'content' => $prompt,
                    ],
                ],
            ]);

            if ($response->successful()) {
                $data     = $response->json();
                $feedback = $data['content'][0]['text'] ?? 'No feedback generated.';
                $tokens   = $data['usage']['output_tokens'] ?? 0;

                $feedbackLog->update([
                    'feedback_received' => $feedback,
                    'tokens_used'       => $tokens,
                    'status'            => 'success',
                ]);

                return [
                    'success'  => true,
                    'feedback' => $feedback,
                    'log_id'   => $feedbackLog->id,
                ];
            }

            $error = $response->json('error.message', 'API request failed.');
            $feedbackLog->update(['status' => 'failed', 'feedback_received' => $error]);

            return ['success' => false, 'message' => $error];

        } catch (\Exception $e) {
            Log::error('AI Feedback Service error: ' . $e->getMessage());
            $feedbackLog->update(['status' => 'failed', 'feedback_received' => $e->getMessage()]);

            return ['success' => false, 'message' => 'AI service is temporarily unavailable.'];
        }
    }

    private function buildPrompt(CodingLog $codingLog): string
    {
        $subject    = $codingLog->subject?->name ?? 'General Programming';
        $difficulty = ucfirst($codingLog->difficulty);

        return <<<PROMPT
You are a helpful coding mentor reviewing a student's code submission.

**Activity Details:**
- Title: {$codingLog->title}
- Subject: {$subject}
- Programming Language: {$codingLog->programming_language}
- Difficulty Level: {$difficulty}
- Description: {$codingLog->description}

**Code Snippet:**
```{$codingLog->programming_language}
{$codingLog->code_snippet}
```

Please provide constructive feedback covering:
1. **Code Quality** - Readability, naming conventions, and structure
2. **Correctness** - Potential bugs or logical issues
3. **Best Practices** - Language-specific conventions and patterns
4. **Suggestions** - Specific improvements the student can make
5. **Encouragement** - A positive closing note for the student

Keep the feedback educational, supportive, and appropriate for an IT student. Be concise but thorough.
PROMPT;
    }
}
