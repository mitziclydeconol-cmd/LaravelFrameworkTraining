@extends('layouts.app')

@section('title', 'Students')

@section('breadcrumb')
    <li class="breadcrumb-item active">Students</li>
@endsection

@section('content')
<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h4 class="fw-bold mb-0">Students</h4>
        <p class="text-muted mb-0" style="font-size:.875rem;">Manage and monitor student progress</p>
    </div>
    <a href="{{ route('instructor.export.all') }}" class="btn btn-outline-primary d-flex align-items-center gap-2">
        <i class="bi bi-download"></i> Export CSV
    </a>
</div>

{{-- Filters --}}
<div class="ct-card p-3 mb-4">
    <form method="GET" class="row g-2 align-items-end">
        <div class="col-md-5">
            <input type="text" name="search" class="form-control form-control-sm"
                   placeholder="Search by name, email, or student ID..." value="{{ request('search') }}">
        </div>
        <div class="col-md-4">
            <select name="subject_id" class="form-select form-select-sm">
                <option value="">All Subjects</option>
                @foreach($subjects as $subject)
                    <option value="{{ $subject->id }}" {{ request('subject_id') == $subject->id ? 'selected' : '' }}>
                        {{ $subject->name }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="col-md-3 d-flex gap-2">
            <button type="submit" class="btn btn-primary btn-sm flex-fill">Filter</button>
            <a href="{{ route('instructor.students.index') }}" class="btn btn-light btn-sm">Reset</a>
        </div>
    </form>
</div>

<div class="ct-card overflow-hidden">
    @if($students->isNotEmpty())
        <div class="table-responsive">
            <table class="table ct-table mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-4">Student</th>
                        <th>Student ID</th>
                        <th>Subjects</th>
                        <th>Total Logs</th>
                        <th>Total Time</th>
                        <th class="pe-4">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($students as $student)
                        <tr>
                            <td class="ps-4">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="rounded-circle d-flex align-items-center justify-content-center text-white fw-bold"
                                         style="width:36px;height:36px;min-width:36px;background:linear-gradient(135deg,#4F46E5,#06B6D4);font-size:.75rem;">
                                        {{ strtoupper(substr($student->name, 0, 2)) }}
                                    </div>
                                    <div>
                                        <div class="fw-medium" style="font-size:.875rem;">{{ $student->name }}</div>
                                        <div class="text-muted" style="font-size:.75rem;">{{ $student->email }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="text-muted font-mono" style="font-size:.8rem;">
                                {{ $student->student_id ?? '—' }}
                            </td>
                            <td>
                                @foreach($student->subjects->take(3) as $subject)
                                    <span class="badge me-1 mb-1" style="background:{{ $subject->color }}22;color:{{ $subject->color }};font-size:.65rem;">
                                        {{ $subject->code }}
                                    </span>
                                @endforeach
                                @if($student->subjects->count() > 3)
                                    <span class="text-muted" style="font-size:.72rem;">+{{ $student->subjects->count() - 3 }} more</span>
                                @endif
                            </td>
                            <td>
                                <span class="fw-semibold">{{ $student->coding_logs_count }}</span>
                                <span class="text-muted" style="font-size:.75rem;">logs</span>
                            </td>
                            <td class="font-mono" style="font-size:.82rem;">
                                @php
                                    $mins = $student->coding_logs_sum ?? 0;
                                    echo floor($mins / 60) . 'h ' . ($mins % 60) . 'm';
                                @endphp
                            </td>
                            <td class="pe-4">
                                <div class="d-flex gap-1">
                                    <a href="{{ route('instructor.students.show', $student) }}" class="btn btn-sm btn-light">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="{{ route('instructor.students.export', $student) }}" class="btn btn-sm btn-light" title="Export CSV">
                                        <i class="bi bi-download"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="p-3 border-top d-flex justify-content-between align-items-center">
            <small class="text-muted">Showing {{ $students->firstItem() }}–{{ $students->lastItem() }} of {{ $students->total() }} students</small>
            {{ $students->links() }}
        </div>
    @else
        <div class="text-center py-5">
            <i class="bi bi-people text-muted" style="font-size:3rem;"></i>
            <h5 class="mt-3 text-muted">No students found</h5>
            <p class="text-muted">Try adjusting your filters.</p>
        </div>
    @endif
</div>
@endsection
