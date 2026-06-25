@extends('layouts.app')

@section('title', 'Edit Log')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('student.logs.index') }}" class="text-decoration-none">Coding Logs</a></li>
    <li class="breadcrumb-item"><a href="{{ route('student.logs.show', $log) }}" class="text-decoration-none">{{ Str::limit($log->title, 30) }}</a></li>
    <li class="breadcrumb-item active">Edit</li>
@endsection

@section('content')
<div class="row justify-content-center">
    <div class="col-xl-9">
        <div class="d-flex align-items-center justify-content-between mb-4">
            <div>
                <h4 class="fw-bold mb-0">Edit Coding Log</h4>
                <p class="text-muted mb-0" style="font-size:.875rem;">Update your session details</p>
            </div>
            <a href="{{ route('student.logs.show', $log) }}" class="btn btn-light">
                <i class="bi bi-arrow-left me-1"></i> Back
            </a>
        </div>

        <form method="POST" action="{{ route('student.logs.update', $log) }}">
            @csrf @method('PUT')

            <div class="ct-card p-4 mb-3">
                <h6 class="fw-semibold mb-3 pb-2 border-bottom">
                    <i class="bi bi-info-circle me-2 text-primary"></i>Session Details
                </h6>
                <div class="row g-3">
                    <div class="col-12">
                        <label class="form-label fw-medium">Title <span class="text-danger">*</span></label>
                        <input type="text" name="title" class="form-control @error('title') is-invalid @enderror"
                               value="{{ old('title', $log->title) }}" required>
                        @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-medium">Subject</label>
                        <select name="subject_id" class="form-select @error('subject_id') is-invalid @enderror">
                            <option value="">— No subject —</option>
                            @foreach($subjects as $subject)
                                <option value="{{ $subject->id }}" {{ old('subject_id', $log->subject_id) == $subject->id ? 'selected' : '' }}>
                                    {{ $subject->name }} ({{ $subject->code }})
                                </option>
                            @endforeach
                        </select>
                        @error('subject_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-medium">Programming Language <span class="text-danger">*</span></label>
                        <select name="programming_language" class="form-select @error('programming_language') is-invalid @enderror" required>
                            @foreach($languages as $lang)
                                <option value="{{ $lang }}" {{ old('programming_language', $log->programming_language) == $lang ? 'selected' : '' }}>
                                    {{ $lang }}
                                </option>
                            @endforeach
                        </select>
                        @error('programming_language')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fw-medium">Date <span class="text-danger">*</span></label>
                        <input type="date" name="log_date" class="form-control"
                               value="{{ old('log_date', $log->log_date->format('Y-m-d')) }}"
                               max="{{ now()->format('Y-m-d') }}" required>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fw-medium">Time Spent</label>
                        <div class="input-group">
                            <input type="number" name="hours" class="form-control" placeholder="0"
                                   min="0" max="23" value="{{ old('hours', $log->hours) }}">
                            <span class="input-group-text">h</span>
                            <input type="number" name="minutes" class="form-control" placeholder="0"
                                   min="0" max="59" value="{{ old('minutes', $log->minutes) }}">
                            <span class="input-group-text">m</span>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fw-medium">Difficulty</label>
                        <select name="difficulty" class="form-select" required>
                            <option value="easy"   {{ old('difficulty', $log->difficulty) == 'easy'   ? 'selected' : '' }}>🟢 Easy</option>
                            <option value="medium" {{ old('difficulty', $log->difficulty) == 'medium' ? 'selected' : '' }}>🟡 Medium</option>
                            <option value="hard"   {{ old('difficulty', $log->difficulty) == 'hard'   ? 'selected' : '' }}>🔴 Hard</option>
                        </select>
                    </div>

                    <div class="col-12">
                        <label class="form-label fw-medium">Description</label>
                        <textarea name="description" class="form-control" rows="3">{{ old('description', $log->description) }}</textarea>
                    </div>
                </div>
            </div>

            <div class="ct-card p-4 mb-4">
                <h6 class="fw-semibold mb-3"><i class="bi bi-code-square text-primary me-2"></i>Code Snippet</h6>
                <textarea name="code_snippet" class="form-control font-mono" rows="12"
                          style="background:#0F172A;color:#E2E8F0;border-radius:10px;font-size:.82rem;line-height:1.6;">{{ old('code_snippet', $log->code_snippet) }}</textarea>
            </div>

            <div class="d-flex gap-2 justify-content-end">
                <a href="{{ route('student.logs.show', $log) }}" class="btn btn-light px-4">Cancel</a>
                <button type="submit" class="btn btn-primary px-4">
                    <i class="bi bi-check-lg me-1"></i> Update Log
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
