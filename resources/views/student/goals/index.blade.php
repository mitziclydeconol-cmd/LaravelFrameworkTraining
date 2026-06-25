@extends('layouts.app')
@section('title','Coding Goals')
@section('breadcrumb')
<li class="breadcrumb-item active">Goals</li>
@endsection

@section('content')
<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h4 class="fw-bold mb-0">Coding Goals 🎯</h4>
        <p class="text-muted mb-0" style="font-size:.875rem;">Set targets and track your milestones</p>
    </div>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#newGoalModal">
        <i class="bi bi-plus-lg me-1"></i> New Goal
    </button>
</div>

{{-- Active Goals --}}
@php $active = $goals->where('is_active', true)->where('is_expired', false); @endphp
@if($active->count())
<h6 class="fw-semibold text-muted mb-3 text-uppercase" style="font-size:.72rem;letter-spacing:.06em;">Active Goals</h6>
<div class="row g-3 mb-4">
    @foreach($active as $goal)
    <div class="col-md-6 col-xl-4">
        <div class="ct-card p-4 h-100 {{ $goal->is_completed ? 'border-success' : '' }}">
            @if($goal->is_completed)
                <div class="badge bg-success-subtle text-success mb-2" style="font-size:.7rem;"><i class="bi bi-check-circle me-1"></i>Completed!</div>
            @endif
            <div class="d-flex align-items-start justify-content-between mb-2">
                <div>
                    <h6 class="fw-semibold mb-1">{{ $goal->title }}</h6>
                    @if($goal->subject)
                        <span class="badge mb-1" style="background:{{ $goal->subject->color }}22;color:{{ $goal->subject->color }};font-size:.65rem;">{{ $goal->subject->code }}</span>
                    @endif
                </div>
                <span class="badge bg-light text-muted" style="font-size:.65rem;">{{ ucfirst($goal->period) }}</span>
            </div>
            @if($goal->target_total_minutes > 0)
            <div class="mb-2">
                <div class="d-flex justify-content-between mb-1">
                    <small class="text-muted">Time Progress</small>
                    <small class="fw-medium">{{ floor($goal->progress_minutes/60) }}h {{ $goal->progress_minutes%60 }}m / {{ $goal->target_hours }}h {{ $goal->target_minutes }}m</small>
                </div>
                <div class="progress ct-progress">
                    <div class="progress-bar {{ $goal->time_progress_pct >= 100 ? 'bg-success' : '' }}" style="width:{{ $goal->time_progress_pct }}%;background:{{ $goal->time_progress_pct < 100 ? '#4F46E5' : '' }};"></div>
                </div>
                <small class="text-muted">{{ $goal->time_progress_pct }}% complete</small>
            </div>
            @endif
            @if($goal->target_logs > 0)
            <div class="mb-3">
                <div class="d-flex justify-content-between mb-1">
                    <small class="text-muted">Logs Progress</small>
                    <small class="fw-medium">{{ $goal->progress_logs }} / {{ $goal->target_logs }} logs</small>
                </div>
                <div class="progress ct-progress">
                    <div class="progress-bar {{ $goal->logs_progress_pct >= 100 ? 'bg-success' : '' }}" style="width:{{ $goal->logs_progress_pct }}%;background:{{ $goal->logs_progress_pct < 100 ? '#06B6D4' : '' }};"></div>
                </div>
            </div>
            @endif
            <div class="d-flex justify-content-between align-items-center pt-2 border-top">
                <small class="text-muted"><i class="bi bi-calendar3 me-1"></i>{{ $goal->start_date->format('M j') }} – {{ $goal->end_date->format('M j, Y') }}</small>
                <form method="POST" action="{{ route('student.goals.destroy', $goal) }}" onsubmit="return confirm('Delete this goal?')">
                    @csrf @method('DELETE')
                    <button type="submit" class="btn btn-sm btn-light text-danger"><i class="bi bi-trash"></i></button>
                </form>
            </div>
        </div>
    </div>
    @endforeach
</div>
@endif

{{-- Expired / Inactive Goals --}}
@php $past = $goals->filter(fn($g) => $g->is_expired || !$g->is_active); @endphp
@if($past->count())
<h6 class="fw-semibold text-muted mb-3 text-uppercase" style="font-size:.72rem;letter-spacing:.06em;">Past Goals</h6>
<div class="ct-card overflow-hidden">
    <table class="table ct-table mb-0">
        <thead class="bg-light"><tr><th class="ps-4">Goal</th><th>Period</th><th>Time</th><th>Logs</th><th>Result</th><th class="pe-4">Date</th></tr></thead>
        <tbody>
        @foreach($past as $goal)
        <tr>
            <td class="ps-4 fw-medium">{{ $goal->title }}</td>
            <td><span class="badge bg-secondary-subtle text-secondary">{{ ucfirst($goal->period) }}</span></td>
            <td class="text-muted" style="font-size:.82rem;">{{ floor($goal->progress_minutes/60) }}h {{ $goal->progress_minutes%60 }}m</td>
            <td class="text-muted" style="font-size:.82rem;">{{ $goal->progress_logs }}</td>
            <td><span class="badge {{ $goal->is_completed ? 'bg-success-subtle text-success' : 'bg-danger-subtle text-danger' }}">{{ $goal->is_completed ? '✓ Done' : 'Missed' }}</span></td>
            <td class="pe-4 text-muted" style="font-size:.78rem;">{{ $goal->end_date->format('M j, Y') }}</td>
        </tr>
        @endforeach
        </tbody>
    </table>
</div>
@endif

@if($goals->isEmpty())
<div class="ct-card p-5 text-center">
    <i class="bi bi-bullseye text-muted" style="font-size:3rem;"></i>
    <h5 class="mt-3 text-muted">No goals yet</h5>
    <p class="text-muted">Set your first coding goal to stay motivated!</p>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#newGoalModal">Create First Goal</button>
</div>
@endif

{{-- New Goal Modal --}}
<div class="modal fade" id="newGoalModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold"><i class="bi bi-bullseye text-primary me-2"></i>New Coding Goal</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="{{ route('student.goals.store') }}">
                @csrf
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label fw-medium">Goal Title *</label>
                            <input type="text" name="title" class="form-control" placeholder="e.g. Code 10 hours this week" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-medium">Subject</label>
                            <select name="subject_id" class="form-select">
                                <option value="">All Subjects</option>
                                @foreach($subjects as $s)
                                    <option value="{{ $s->id }}">{{ $s->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-medium">Period</label>
                            <select name="period" class="form-select">
                                <option value="daily">Daily</option>
                                <option value="weekly" selected>Weekly</option>
                                <option value="monthly">Monthly</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-medium">Target Time</label>
                            <div class="input-group">
                                <input type="number" name="target_hours" class="form-control" placeholder="0" min="0" max="999" value="0">
                                <span class="input-group-text">h</span>
                                <input type="number" name="target_minutes" class="form-control" placeholder="0" min="0" max="59" value="0">
                                <span class="input-group-text">m</span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-medium">Target Log Entries</label>
                            <input type="number" name="target_logs" class="form-control" placeholder="0" min="0" value="5">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-medium">Start Date *</label>
                            <input type="date" name="start_date" class="form-control" value="{{ now()->format('Y-m-d') }}" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-medium">End Date *</label>
                            <input type="date" name="end_date" class="form-control" value="{{ now()->addWeek()->format('Y-m-d') }}" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-medium">Description</label>
                            <textarea name="description" class="form-control" rows="2" placeholder="Optional notes..."></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary px-4"><i class="bi bi-check-lg me-1"></i>Create Goal</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
