@extends('layouts.app')

@section('title', 'Subjects')

@section('breadcrumb')
    <li class="breadcrumb-item active">Subjects</li>
@endsection

@section('content')
<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h4 class="fw-bold mb-0">Subjects</h4>
        <p class="text-muted mb-0" style="font-size:.875rem;">Manage curriculum subjects and enrollments</p>
    </div>
    <a href="{{ route('subjects.create') }}" class="btn btn-primary d-flex align-items-center gap-2">
        <i class="bi bi-plus-lg"></i> New Subject
    </a>
</div>

<div class="row g-3">
    @forelse($subjects as $subject)
        <div class="col-md-6 col-xl-4">
            <div class="ct-card p-4 h-100" style="border-top: 4px solid {{ $subject->color }};">
                <div class="d-flex align-items-start justify-content-between mb-3">
                    <div>
                        <span class="badge mb-2" style="background:{{ $subject->color }}22;color:{{ $subject->color }};">
                            {{ $subject->code }}
                        </span>
                        <h6 class="fw-semibold mb-1">{{ $subject->name }}</h6>
                        <p class="text-muted mb-0" style="font-size:.78rem;">{{ Str::limit($subject->description, 80) ?? 'No description.' }}</p>
                    </div>
                </div>

                <div class="d-flex gap-3 my-3 py-2 border-top border-bottom">
                    <div class="text-center flex-fill">
                        <div class="fw-bold">{{ $subject->students_count }}</div>
                        <div class="text-muted" style="font-size:.72rem;">Students</div>
                    </div>
                    <div class="text-center flex-fill">
                        <div class="fw-bold">{{ $subject->coding_logs_count }}</div>
                        <div class="text-muted" style="font-size:.72rem;">Log Entries</div>
                    </div>
                    <div class="text-center flex-fill">
                        <div class="fw-bold" style="font-size:.75rem;color:#64748B;">by</div>
                        <div class="text-muted" style="font-size:.72rem;">{{ $subject->creator->name }}</div>
                    </div>
                </div>

                <div class="d-flex gap-2">
                    <a href="{{ route('subjects.show', $subject) }}" class="btn btn-sm btn-primary flex-fill">
                        <i class="bi bi-eye me-1"></i>View
                    </a>
                    <a href="{{ route('subjects.edit', $subject) }}" class="btn btn-sm btn-light">
                        <i class="bi bi-pencil"></i>
                    </a>
                    <form method="POST" action="{{ route('subjects.destroy', $subject) }}"
                          onsubmit="return confirm('Delete this subject? This will remove all associations.')">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-light text-danger">
                            <i class="bi bi-trash"></i>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    @empty
        <div class="col-12">
            <div class="ct-card p-5 text-center">
                <i class="bi bi-book text-muted" style="font-size:3rem;"></i>
                <h5 class="mt-3 text-muted">No subjects yet</h5>
                <p class="text-muted">Create your first subject to get started.</p>
                <a href="{{ route('subjects.create') }}" class="btn btn-primary">
                    <i class="bi bi-plus me-1"></i>Create Subject
                </a>
            </div>
        </div>
    @endforelse
</div>

@if($subjects->hasPages())
    <div class="mt-4">{{ $subjects->links() }}</div>
@endif
@endsection
