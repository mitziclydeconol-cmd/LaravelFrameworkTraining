@extends('layouts.app')

@section('title', 'New Coding Log')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('student.logs.index') }}" class="text-decoration-none">Coding Logs</a></li>
    <li class="breadcrumb-item active">New Log</li>
@endsection

@section('content')
<div class="row justify-content-center">
    <div class="col-xl-9">
        <div class="d-flex align-items-center justify-content-between mb-4">
            <div>
                <h4 class="fw-bold mb-0">Log a Coding Session</h4>
                <p class="text-muted mb-0" style="font-size:.875rem;">Record what you worked on today</p>
            </div>
            <a href="{{ route('student.logs.index') }}" class="btn btn-light">
                <i class="bi bi-arrow-left me-1"></i> Back
            </a>
        </div>

        <form method="POST" action="{{ route('student.logs.store') }}">
            @csrf

            <div class="ct-card p-4 mb-3">
                <h6 class="fw-semibold mb-3 pb-2 border-bottom">
                    <i class="bi bi-info-circle me-2 text-primary"></i>Session Details
                </h6>
                <div class="row g-3">
                    <div class="col-12">
                        <label class="form-label fw-medium">Title <span class="text-danger">*</span></label>
                        <input type="text" name="title" class="form-control @error('title') is-invalid @enderror"
                               placeholder="e.g. Implemented binary search algorithm"
                               value="{{ old('title') }}" required>
                        @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-medium">Subject</label>
                        <select name="subject_id" class="form-select @error('subject_id') is-invalid @enderror">
                            <option value="">— No subject —</option>
                            @foreach($subjects as $subject)
                                <option value="{{ $subject->id }}" {{ old('subject_id') == $subject->id ? 'selected' : '' }}>
                                    {{ $subject->name }} ({{ $subject->code }})
                                </option>
                            @endforeach
                        </select>
                        @error('subject_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-medium">Programming Language <span class="text-danger">*</span></label>
                        <select name="programming_language" class="form-select @error('programming_language') is-invalid @enderror" required>
                            <option value="">— Select language —</option>
                            @foreach($languages as $lang)
                                <option value="{{ $lang }}" {{ old('programming_language') == $lang ? 'selected' : '' }}>
                                    {{ $lang }}
                                </option>
                            @endforeach
                        </select>
                        @error('programming_language')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fw-medium">Date <span class="text-danger">*</span></label>
                        <input type="date" name="log_date" class="form-control @error('log_date') is-invalid @enderror"
                               value="{{ old('log_date', now()->format('Y-m-d')) }}"
                               max="{{ now()->format('Y-m-d') }}" required>
                        @error('log_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fw-medium">Time Spent</label>
                        <div class="input-group">
                            <input type="number" name="hours" class="form-control @error('hours') is-invalid @enderror"
                                   placeholder="0" min="0" max="23" value="{{ old('hours', 0) }}">
                            <span class="input-group-text">h</span>
                            <input type="number" name="minutes" class="form-control @error('minutes') is-invalid @enderror"
                                   placeholder="0" min="0" max="59" value="{{ old('minutes', 30) }}">
                            <span class="input-group-text">m</span>
                        </div>
                        @error('hours')<div class="text-danger" style="font-size:.82rem;">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fw-medium">Difficulty <span class="text-danger">*</span></label>
                        <select name="difficulty" class="form-select @error('difficulty') is-invalid @enderror" required>
                            <option value="easy"   {{ old('difficulty') == 'easy'   ? 'selected' : '' }}>🟢 Easy</option>
                            <option value="medium" {{ old('difficulty', 'medium') == 'medium' ? 'selected' : '' }}>🟡 Medium</option>
                            <option value="hard"   {{ old('difficulty') == 'hard'   ? 'selected' : '' }}>🔴 Hard</option>
                        </select>
                        @error('difficulty')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-12">
                        <label class="form-label fw-medium">Description</label>
                        <textarea name="description" class="form-control @error('description') is-invalid @enderror"
                                  rows="3" placeholder="What did you work on? What did you learn?">{{ old('description') }}</textarea>
                        @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
            </div>

            <div class="ct-card p-4 mb-4">
                <h6 class="fw-semibold mb-1 d-flex align-items-center gap-2">
                    <i class="bi bi-code-square text-primary"></i> Code Snippet
                    <span class="badge bg-secondary-subtle text-secondary" style="font-size:.65rem;">Optional</span>
                </h6>
                <p class="text-muted mb-3" style="font-size:.8rem;">Paste your code here — you can later get AI feedback on it!</p>
                <textarea name="code_snippet"
                          class="form-control font-mono @error('code_snippet') is-invalid @enderror"
                          rows="12"
                          placeholder="// Paste your code here..."
                          style="background:#0F172A;color:#E2E8F0;border-radius:10px;font-size:.82rem;line-height:1.6;">{{ old('code_snippet') }}</textarea>
                @error('code_snippet')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="d-flex gap-2 justify-content-end">
                <a href="{{ route('student.logs.index') }}" class="btn btn-light px-4">Cancel</a>
                <button type="submit" class="btn btn-primary px-4">
                    <i class="bi bi-check-lg me-1"></i> Save Log
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
