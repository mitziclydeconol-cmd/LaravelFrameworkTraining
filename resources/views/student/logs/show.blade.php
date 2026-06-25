@extends('layouts.app')

@section('title', $log->title)

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('student.logs.index') }}" class="text-decoration-none">Coding Logs</a></li>
    <li class="breadcrumb-item active">{{ Str::limit($log->title, 40) }}</li>
@endsection

@section('content')
<div class="row justify-content-center">
    <div class="col-xl-9">

        {{-- Header --}}
        <div class="ct-card p-4 mb-4">
            <div class="d-flex align-items-start justify-content-between flex-wrap gap-3">
                <div>
                    <div class="d-flex align-items-center gap-2 mb-2 flex-wrap">
                        <span class="lang-badge" style="background:#EEF2FF;color:#4F46E5;">{{ $log->programming_language }}</span>
                        @if($log->subject)
                            <span class="badge" style="background:{{ $log->subject->color }}22;color:{{ $log->subject->color }};">
                                <i class="bi bi-book me-1"></i>{{ $log->subject->name }}
                            </span>
                        @endif
                        <span class="badge diff-{{ $log->difficulty }}">{{ ucfirst($log->difficulty) }}</span>
                    </div>
                    <h4 class="fw-bold mb-1">{{ $log->title }}</h4>
                    <div class="d-flex gap-3 text-muted flex-wrap" style="font-size:.82rem;">
                        <span><i class="bi bi-calendar3 me-1"></i>{{ $log->log_date->format('F j, Y') }}</span>
                        <span><i class="bi bi-clock me-1"></i>{{ $log->duration }}</span>
                        <span><i class="bi bi-pencil me-1"></i>{{ $log->created_at->diffForHumans() }}</span>
                    </div>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('student.logs.edit', $log) }}" class="btn btn-outline-primary btn-sm">
                        <i class="bi bi-pencil me-1"></i>Edit
                    </a>
                    <form method="POST" action="{{ route('student.logs.destroy', $log) }}"
                          onsubmit="return confirm('Delete this log?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn btn-outline-danger btn-sm">
                            <i class="bi bi-trash me-1"></i>Delete
                        </button>
                    </form>
                </div>
            </div>
        </div>

        {{-- Description --}}
        @if($log->description)
        <div class="ct-card p-4 mb-4">
            <h6 class="fw-semibold mb-2"><i class="bi bi-file-text me-2 text-primary"></i>Description</h6>
            <p class="mb-0 text-secondary" style="line-height:1.7;">{{ $log->description }}</p>
        </div>
        @endif

        {{-- Code Snippet + AI Feedback --}}
        @if($log->code_snippet)
        <div class="ct-card p-4 mb-4">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <h6 class="fw-semibold mb-0"><i class="bi bi-code-slash me-2 text-primary"></i>Code Snippet</h6>
                <form method="POST" action="{{ route('student.logs.ai-feedback', $log) }}">
                    @csrf
                    <button type="submit" class="btn btn-sm d-flex align-items-center gap-2"
                            style="background:linear-gradient(135deg,#4F46E5,#7C3AED);color:#fff;border:none;">
                        <i class="bi bi-stars"></i> Get AI Feedback
                    </button>
                </form>
            </div>
            <div class="code-block">{{ $log->code_snippet }}</div>
        </div>

        {{-- AI Feedback Result --}}
        @if(session('ai_feedback'))
        <div class="ct-card p-4 mb-4">
            <h6 class="fw-semibold mb-3 d-flex align-items-center gap-2">
                <span style="background:linear-gradient(135deg,#4F46E5,#7C3AED);-webkit-background-clip:text;-webkit-text-fill-color:transparent;">
                    <i class="bi bi-stars"></i> AI Code Review
                </span>
                <span class="badge bg-primary-subtle text-primary" style="font-size:.65rem;">Powered by Claude</span>
            </h6>
            <div class="ai-feedback-box" style="white-space:pre-wrap;font-size:.875rem;line-height:1.75;">{{ session('ai_feedback') }}</div>
        </div>
        @endif

        {{-- Past AI Feedback --}}
        @if($log->aiFeedbackLogs->count())
        <div class="ct-card p-4 mb-4">
            <h6 class="fw-semibold mb-3"><i class="bi bi-clock-history me-2 text-muted"></i>Previous AI Reviews</h6>
            @foreach($log->aiFeedbackLogs as $feedback)
            <div class="border rounded-3 p-3 mb-3">
                <div class="d-flex justify-content-between mb-2">
                    <span class="badge bg-{{ $feedback->status === 'success' ? 'success' : 'danger' }}-subtle text-{{ $feedback->status === 'success' ? 'success' : 'danger' }}">
                        {{ ucfirst($feedback->status) }}
                    </span>
                    <small class="text-muted">{{ $feedback->created_at->diffForHumans() }}</small>
                </div>
                @if($feedback->feedback_received)
                <div class="ai-feedback-box" style="font-size:.8rem;max-height:300px;overflow-y:auto;white-space:pre-wrap;">{{ $feedback->feedback_received }}</div>
                @endif
            </div>
            @endforeach
        </div>
        @endif

        @else
        <div class="ct-card p-4 mb-4 text-center">
            <i class="bi bi-code-slash text-muted" style="font-size:2rem;"></i>
            <p class="text-muted mt-2 mb-2">No code snippet attached.</p>
            <a href="{{ route('student.logs.edit', $log) }}" class="btn btn-sm btn-outline-primary">Add Code Snippet</a>
        </div>
        @endif

        {{-- Self Assessment --}}
        <x-self-assessment :log="$log" />

        {{-- Comments --}}
        <x-log-comments :log="$log" />

        <div class="d-flex gap-2">
            <a href="{{ route('student.logs.index') }}" class="btn btn-light"><i class="bi bi-arrow-left me-1"></i>Back to Logs</a>
            <a href="{{ route('student.logs.create') }}" class="btn btn-primary"><i class="bi bi-plus me-1"></i>New Log</a>
        </div>
    </div>
</div>
@endsection
