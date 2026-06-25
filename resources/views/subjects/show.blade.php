@extends('layouts.app')

@section('title', $subject->name)

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('subjects.index') }}" class="text-decoration-none">Subjects</a></li>
    <li class="breadcrumb-item active">{{ $subject->code }}</li>
@endsection

@section('content')
{{-- Header --}}
<div class="ct-card p-4 mb-4" style="border-top: 4px solid {{ $subject->color }};">
    <div class="d-flex align-items-start justify-content-between flex-wrap gap-3">
        <div>
            <span class="badge mb-2" style="background:{{ $subject->color }}22;color:{{ $subject->color }};">{{ $subject->code }}</span>
            <h4 class="fw-bold mb-1">{{ $subject->name }}</h4>
            <p class="text-muted mb-0">{{ $subject->description ?? 'No description provided.' }}</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('subjects.edit', $subject) }}" class="btn btn-outline-primary btn-sm">
                <i class="bi bi-pencil me-1"></i>Edit
            </a>
        </div>
    </div>
    <div class="row g-3 mt-2">
        <div class="col-4">
            <div class="text-center">
                <div class="fw-bold fs-5">{{ $subject->students->count() }}</div>
                <div class="text-muted" style="font-size:.78rem;">Enrolled Students</div>
            </div>
        </div>
        <div class="col-4">
            <div class="text-center">
                <div class="fw-bold fs-5">{{ $subject->codingLogs->count() }}</div>
                <div class="text-muted" style="font-size:.78rem;">Total Logs</div>
            </div>
        </div>
        <div class="col-4">
            <div class="text-center">
                <div class="fw-bold fs-5">
                    @php $mins = $subject->codingLogs->sum(fn($l) => $l->hours * 60 + $l->minutes); @endphp
                    {{ floor($mins / 60) }}h
                </div>
                <div class="text-muted" style="font-size:.78rem;">Total Hours</div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    {{-- Enrolled Students --}}
    <div class="col-lg-6">
        <div class="ct-card p-4 h-100">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <h6 class="fw-semibold mb-0"><i class="bi bi-people me-2 text-primary"></i>Enrolled Students</h6>
            </div>

            @if($subject->students->count())
                <div class="mb-3">
                    @foreach($subject->students as $student)
                        <div class="d-flex align-items-center justify-content-between gap-3 py-2 {{ !$loop->last ? 'border-bottom' : '' }}">
                            <div class="d-flex align-items-center gap-2">
                                <div class="rounded-circle d-flex align-items-center justify-content-center text-white fw-bold"
                                     style="width:32px;height:32px;background:linear-gradient(135deg,#4F46E5,#06B6D4);font-size:.7rem;">
                                    {{ strtoupper(substr($student->name, 0, 2)) }}
                                </div>
                                <div>
                                    <div style="font-size:.875rem;font-weight:500;">{{ $student->name }}</div>
                                    <div style="font-size:.72rem;color:#64748B;">{{ $student->student_id ?? $student->email }}</div>
                                </div>
                            </div>
                            <form method="POST" action="{{ route('subjects.remove-student', [$subject, $student]) }}"
                                  onsubmit="return confirm('Remove this student from the subject?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-light text-danger" title="Remove">
                                    <i class="bi bi-person-dash"></i>
                                </button>
                            </form>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-muted text-center py-3">No students enrolled yet.</p>
            @endif

            {{-- Enroll Student Form --}}
            @if($allStudents->count())
                <hr>
                <h6 class="fw-semibold mb-2 mt-3" style="font-size:.82rem;">Enroll a Student</h6>
                <form method="POST" action="{{ route('subjects.assign-student', $subject) }}" class="d-flex gap-2">
                    @csrf
                    <select name="student_id" class="form-select form-select-sm" required>
                        <option value="">— Select student —</option>
                        @foreach($allStudents as $s)
                            <option value="{{ $s->id }}">{{ $s->name }} ({{ $s->student_id ?? $s->email }})</option>
                        @endforeach
                    </select>
                    <button type="submit" class="btn btn-sm btn-primary text-nowrap">
                        <i class="bi bi-person-plus"></i> Enroll
                    </button>
                </form>
            @endif
        </div>
    </div>

    {{-- Recent Coding Logs --}}
    <div class="col-lg-6">
        <div class="ct-card p-4 h-100">
            <h6 class="fw-semibold mb-3"><i class="bi bi-journal-code me-2 text-primary"></i>Recent Activity</h6>
            @forelse($subject->codingLogs->take(8) as $log)
                <div class="d-flex align-items-start gap-3 mb-3 pb-3 {{ !$loop->last ? 'border-bottom' : '' }}">
                    <div class="ct-stat-icon flex-shrink-0" style="width:32px;height:32px;font-size:.8rem;background:#EEF2FF;">
                        <i class="bi bi-code-square" style="color:#4F46E5;"></i>
                    </div>
                    <div>
                        <div class="fw-medium" style="font-size:.875rem;">{{ $log->title }}</div>
                        <div class="d-flex gap-2 mt-1 flex-wrap">
                            <span class="text-muted" style="font-size:.72rem;">
                                <i class="bi bi-person"></i> {{ $log->user->name }}
                            </span>
                            <span class="lang-badge" style="background:#EEF2FF;color:#4F46E5;font-size:.65rem;">{{ $log->programming_language }}</span>
                            <span class="text-muted" style="font-size:.72rem;"><i class="bi bi-clock"></i> {{ $log->duration }}</span>
                        </div>
                    </div>
                </div>
            @empty
                <p class="text-muted text-center py-4">No activity logged for this subject yet.</p>
            @endforelse
        </div>
    </div>
</div>
@endsection
