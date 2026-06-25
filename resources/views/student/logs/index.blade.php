@extends('layouts.app')

@section('title', 'My Coding Logs')

@section('breadcrumb')
    <li class="breadcrumb-item active">Coding Logs</li>
@endsection

@section('content')
<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h4 class="fw-bold mb-0">My Coding Logs</h4>
        <p class="text-muted mb-0" style="font-size:.875rem;">Track and manage your coding sessions</p>
    </div>
    <a href="{{ route('student.logs.create') }}" class="btn btn-primary d-flex align-items-center gap-2">
        <i class="bi bi-plus-lg"></i> New Log
    </a>
</div>

{{-- Filters --}}
<div class="ct-card p-3 mb-4">
    <form method="GET" class="row g-2 align-items-end">
        <div class="col-md-4">
            <input type="text" name="search" class="form-control form-control-sm"
                   placeholder="Search logs..." value="{{ request('search') }}">
        </div>
        <div class="col-md-3">
            <select name="subject_id" class="form-select form-select-sm">
                <option value="">All Subjects</option>
                @foreach($subjects as $subject)
                    <option value="{{ $subject->id }}" {{ request('subject_id') == $subject->id ? 'selected' : '' }}>
                        {{ $subject->name }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="col-md-3">
            <select name="language" class="form-select form-select-sm">
                <option value="">All Languages</option>
                @foreach($languages as $lang)
                    <option value="{{ $lang }}" {{ request('language') == $lang ? 'selected' : '' }}>{{ $lang }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2 d-flex gap-2">
            <button type="submit" class="btn btn-primary btn-sm flex-fill">Filter</button>
            <a href="{{ route('student.logs.index') }}" class="btn btn-light btn-sm">Reset</a>
        </div>
    </form>
</div>

{{-- Logs Table --}}
<div class="ct-card overflow-hidden">
    @if($logs->isNotEmpty())
        <div class="table-responsive">
            <table class="table ct-table mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-4">Title</th>
                        <th>Subject</th>
                        <th>Language</th>
                        <th>Duration</th>
                        <th>Difficulty</th>
                        <th>Date</th>
                        <th class="pe-4">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($logs as $log)
                        <tr>
                            <td class="ps-4">
                                <a href="{{ route('student.logs.show', $log) }}" class="text-decoration-none fw-medium text-dark">
                                    {{ $log->title }}
                                </a>
                                @if($log->code_snippet)
                                    <i class="bi bi-code-slash ms-1 text-muted" title="Has code snippet"></i>
                                @endif
                                @if($log->aiFeedbackLogs->count())
                                    <i class="bi bi-stars ms-1 text-warning" title="Has AI feedback"></i>
                                @endif
                            </td>
                            <td>
                                @if($log->subject)
                                    <span class="badge" style="background:{{ $log->subject->color }}22;color:{{ $log->subject->color }};">
                                        {{ $log->subject->code }}
                                    </span>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td>
                                <span class="lang-badge" style="background:#EEF2FF;color:#4F46E5;">
                                    {{ $log->programming_language }}
                                </span>
                            </td>
                            <td class="text-muted" style="font-size:.875rem;font-family:'JetBrains Mono',monospace;">
                                {{ $log->duration }}
                            </td>
                            <td>
                                <span class="badge diff-{{ $log->difficulty }}">{{ ucfirst($log->difficulty) }}</span>
                            </td>
                            <td class="text-muted" style="font-size:.82rem;">
                                {{ $log->log_date->format('M j, Y') }}
                            </td>
                            <td class="pe-4">
                                <div class="d-flex gap-1">
                                    <a href="{{ route('student.logs.show', $log) }}" class="btn btn-sm btn-light" title="View">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="{{ route('student.logs.edit', $log) }}" class="btn btn-sm btn-light" title="Edit">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <form method="POST" action="{{ route('student.logs.destroy', $log) }}"
                                          onsubmit="return confirm('Delete this log?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-light text-danger" title="Delete">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="p-3 border-top d-flex justify-content-between align-items-center">
            <small class="text-muted">Showing {{ $logs->firstItem() }}–{{ $logs->lastItem() }} of {{ $logs->total() }} logs</small>
            {{ $logs->links() }}
        </div>
    @else
        <div class="text-center py-5">
            <i class="bi bi-journal-x text-muted" style="font-size:3rem;"></i>
            <h5 class="mt-3 text-muted">No logs found</h5>
            <p class="text-muted">{{ request()->hasAny(['search','subject_id','language']) ? 'Try adjusting your filters.' : 'Start by logging your first coding session!' }}</p>
            <a href="{{ route('student.logs.create') }}" class="btn btn-primary">
                <i class="bi bi-plus me-1"></i> Create First Log
            </a>
        </div>
    @endif
</div>
@endsection
