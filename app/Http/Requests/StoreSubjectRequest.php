<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreSubjectRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->isInstructor();
    }

    public function rules(): array
    {
        $subjectId = $this->route('subject')?->id;

        return [
            'name'        => ['required', 'string', 'max:100'],
            'code'        => ['required', 'string', 'max:20', 'unique:subjects,code,' . $subjectId],
            'description' => ['nullable', 'string', 'max:500'],
            'color'       => ['required', 'regex:/^#[0-9A-Fa-f]{6}$/'],
        ];
    }
}
