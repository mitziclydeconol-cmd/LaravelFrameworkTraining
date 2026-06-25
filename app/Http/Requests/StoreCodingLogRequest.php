<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCodingLogRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->isStudent();
    }

    public function rules(): array
    {
        return [
            'title'                => ['required', 'string', 'max:255'],
            'description'          => ['nullable', 'string', 'max:2000'],
            'programming_language' => ['required', 'string', 'max:50'],
            'hours'                => ['required', 'integer', 'min:0', 'max:23'],
            'minutes'              => ['required', 'integer', 'min:0', 'max:59'],
            'log_date'             => ['required', 'date', 'before_or_equal:today'],
            'subject_id'           => ['nullable', 'exists:subjects,id'],
            'code_snippet'         => ['nullable', 'string'],
            'difficulty'           => ['required', 'in:easy,medium,hard'],
        ];
    }

    public function messages(): array
    {
        return [
            'log_date.before_or_equal' => 'The log date cannot be in the future.',
            'hours.max'                => 'Hours cannot exceed 23 per log entry.',
            'minutes.max'              => 'Minutes cannot exceed 59.',
        ];
    }
}
