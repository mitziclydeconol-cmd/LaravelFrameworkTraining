@extends('layouts.app')
@section('title','AI Study Suggestions')
@section('breadcrumb')<li class="breadcrumb-item active">AI Suggestions</li>@endsection

@section('content')
<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h4 class="fw-bold mb-0">AI Study Suggestions 🤖</h4>
        <p class="text-muted mb-0" style="font-size:.875rem;">Personalized study plans powered by Claude AI</p>
    </div>
    <form method="POST" action="{{ route('student.suggestions.generate') }}">
        @csrf
        <button type="submit" class="btn btn-primary d-flex align-items-center gap-2">
            <i class="bi bi-stars"></i> Generate New Plan
        </button>
    </form>
</div>

@if($latest)
<div class="ct-card p-4 mb-4" style="border-left:4px solid #4F46E5;">
    <div class="d-flex align-items-center gap-2 mb-3">
        <span style="background:linear-gradient(135deg,#4F46E5,#7C3AED);-webkit-background-clip:text;-webkit-text-fill-color:transparent;font-weight:700;font-size:1rem;">
            <i class="bi bi-stars"></i> Latest AI Study Plan
        </span>
        <span class="badge bg-primary-subtle text-primary" style="font-size:.65rem;">Claude AI</span>
        <span class="text-muted ms-auto" style="font-size:.75rem;">{{ $latest->generated_at->diffForHumans() }}</span>
    </div>
    <div class="ai-feedback-box" style="white-space:pre-wrap;font-size:.875rem;line-height:1.75;">{{ $latest->suggestion }}</div>
</div>

@if($suggestions->count() > 1)
<h6 class="fw-semibold text-muted text-uppercase mb-3" style="font-size:.72rem;letter-spacing:.06em;">Previous Plans</h6>
<div class="ct-card overflow-hidden">
    @foreach($suggestions->skip(1) as $s)
    <div class="p-4 {{ !$loop->last ? 'border-bottom' : '' }}">
        <div class="d-flex align-items-center justify-content-between mb-2">
            <span class="fw-medium" style="font-size:.82rem;">Plan from {{ $s->generated_at->format('M j, Y g:i A') }}</span>
            <small class="text-muted">{{ $s->tokens_used }} tokens</small>
        </div>
        <div class="text-muted" style="font-size:.8rem;line-height:1.6;white-space:pre-wrap;max-height:200px;overflow:hidden;position:relative;">
            {{ Str::limit($s->suggestion, 400) }}
            <div style="position:absolute;bottom:0;left:0;right:0;height:60px;background:linear-gradient(transparent,#fff);"></div>
        </div>
    </div>
    @endforeach
</div>
@endif

@else
<div class="ct-card p-5 text-center">
    <div style="font-size:3.5rem;">🤖</div>
    <h5 class="mt-3 fw-bold">Get Your Personalized Study Plan</h5>
    <p class="text-muted">Claude AI will analyze your coding activity, languages, subjects, and progress to create a custom study plan just for you.</p>
    <div class="row justify-content-center g-3 my-3">
        <div class="col-md-3">
            <div class="p-3 rounded-3" style="background:#EEF2FF;">
                <i class="bi bi-graph-up text-primary fs-4 d-block mb-1"></i>
                <div style="font-size:.78rem;font-weight:500;">Analyzes your progress</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="p-3 rounded-3" style="background:#F0FDF4;">
                <i class="bi bi-lightbulb text-success fs-4 d-block mb-1"></i>
                <div style="font-size:.78rem;font-weight:500;">Identifies weak areas</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="p-3 rounded-3" style="background:#FFF7ED;">
                <i class="bi bi-list-check text-warning fs-4 d-block mb-1"></i>
                <div style="font-size:.78rem;font-weight:500;">Gives actionable tasks</div>
            </div>
        </div>
    </div>
    <form method="POST" action="{{ route('student.suggestions.generate') }}">
        @csrf
        <button type="submit" class="btn btn-primary btn-lg px-5">
            <i class="bi bi-stars me-2"></i>Generate My Study Plan
        </button>
    </form>
</div>
@endif
@endsection
