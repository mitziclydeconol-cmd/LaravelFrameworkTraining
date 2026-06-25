@extends('layouts.app')

@section('title', $student->name . ' – Profile')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('instructor.students.index') }}" class="text-decoration-none">Students</a></li>
    <li class="breadcrumb-item active">{{ $student->name }}</li>
@endsection

@section('content')
{{-- Student Header --}}
<div class="ct-card p-4 mb-4">
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
        <div class="d-flex align-items-center gap-4">
            <div class="rounded-circle d-flex align-items-center justify-content-center text-white fw-bold"
                 style="width:64px;height:64px;background:linear-gradient(135deg,#4F46E5,#06B6D4);font-size:1.4rem;">
                {{ strtoupper(substr($student->name, 0, 2)) }}
            </div>
            <div>
                <h4 class="fw-bold mb-1">{{ $student->name }}</h4>
                <div class="d-flex gap-3 text-muted flex-wrap" style="font-size:.82rem;">
                    <span><i class="bi bi-envelope me-1"></i>{{ $student->email }}</span>
                    @if($student->student_id)
                        <span><i class="bi bi-person-badge me-1"></i>{{ $student->student_id }}</span>
                    @endif
                    <span><i class="bi bi-calendar-check me-1"></i>Joined {{ $student->created_at->format('M Y') }}</span>
                </div>
            </div>
        </div>
        <a href="{{ route('instructor.students.export', $student) }}" class="btn btn-outline-primary">
            <i class="bi bi-download me-1"></i>Export CSV
        </a>
    </div>
</div>

{{-- Stats --}}
<div class="row g-3 mb-4">
    <div class="col-6 col-lg-3">
        <div class="ct-stat-card">
            <div class="ct-stat-icon mb-2" style="background:#EEF2FF;font-size:1rem;"><i class="bi bi-clock" style="color:#4F46E5;"></i></div>
            <div class="ct-stat-value" style="font-size:1.5rem;">{{ floor($totalMinutes / 60) }}h {{ $totalMinutes % 60 }}m</div>
            <div class="ct-stat-label">Total Coding Time</div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="ct-stat-card">
            <div class="ct-stat-icon mb-2" style="background:#F0FDF4;font-size:1rem;"><i class="bi bi-journal-check" style="color:#16A34A;"></i></div>
            <div class="ct-stat-value" style="font-size:1.5rem;">{{ $totalLogs }}</div>
            <div class="ct-stat-label">Total Logs</div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="ct-stat-card">
            <div class="ct-stat-icon mb-2" style="background:#FFF7ED;font-size:1rem;"><i class="bi bi-calendar-week" style="color:#D97706;"></i></div>
            <div class="ct-stat-value" style="font-size:1.5rem;">{{ floor($weeklyMinutes / 60) }}h {{ $weeklyMinutes % 60 }}m</div>
            <div class="ct-stat-label">This Week</div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="ct-stat-card">
            <div class="ct-stat-icon mb-2" style="background:#FDF4FF;font-size:1rem;"><i class="bi bi-book" style="color:#9333EA;"></i></div>
            <div class="ct-stat-value" style="font-size:1.5rem;">{{ $student->subjects->count() }}</div>
            <div class="ct-stat-label">Enrolled Subjects</div>
        </div>
    </div>
</div>

<div class="row g-3">
    {{-- Subject Progress --}}
    <div class="col-lg-5">
        <div class="ct-card p-4 h-100">
            <h6 class="fw-semibold mb-3"><i class="bi bi-book me-2 text-primary"></i>Progress by Subject</h6>
            @forelse($subjectProgress as $progress)
                <div class="mb-3">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <div>
                            <span class="fw-medium" style="font-size:.875rem;">{{ $progress['subject']->name }}</span>
                        </div>
                        <span class="text-muted" style="font-size:.78rem;">{{ $progress['hours'] }}h · {{ $progress['logs'] }} logs</span>
                    </div>
                    @php $maxH = $subjectProgress->max('hours') ?: 1; $pct = round($progress['hours'] / $maxH * 100); @endphp
                    <div class="progress ct-progress">
                        <div class="progress-bar" style="width:{{ $pct }}%;background:{{ $progress['subject']->color }};"></div>
                    </div>
                </div>
            @empty
                <p class="text-muted text-center py-4">No subject progress yet.</p>
            @endforelse

            @if($languageStats->count())
                <hr class="my-3">
                <h6 class="fw-semibold mb-3"><i class="bi bi-code-slash me-2 text-primary"></i>Languages Used</h6>
                <div class="d-flex flex-wrap gap-2">
                    @foreach($languageStats as $lang)
                        <span class="lang-badge" style="background:#EEF2FF;color:#4F46E5;">
                            {{ $lang->programming_language }}
                            <span class="badge bg-primary-subtle ms-1" style="font-size:.6rem;">{{ $lang->count }}</span>
                        </span>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    {{-- Recent Logs --}}
    <div class="col-lg-7">
        <div class="ct-card p-4 h-100">
            <h6 class="fw-semibold mb-3"><i class="bi bi-clock-history me-2 text-primary"></i>Recent Activity</h6>
            @forelse($recentLogs as $log)
                <div class="d-flex align-items-start gap-3 mb-3 pb-3 {{ !$loop->last ? 'border-bottom' : '' }}">
                    <div class="ct-stat-icon flex-shrink-0" style="width:34px;height:34px;background:#EEF2FF;font-size:.85rem;">
                        <i class="bi bi-code-square" style="color:#4F46E5;"></i>
                    </div>
                    <div class="flex-grow-1">
                        <div class="fw-medium" style="font-size:.875rem;">{{ $log->title }}</div>
                        <div class="d-flex gap-2 flex-wrap mt-1">
                            <span class="lang-badge" style="background:#EEF2FF;color:#4F46E5;font-size:.65rem;">{{ $log->programming_language }}</span>
                            @if($log->subject)
                                <span class="badge" style="background:{{ $log->subject->color }}22;color:{{ $log->subject->color }};font-size:.65rem;">{{ $log->subject->code }}</span>
                            @endif
                            <span class="badge diff-{{ $log->difficulty }}" style="font-size:.65rem;">{{ ucfirst($log->difficulty) }}</span>
                            <span class="text-muted" style="font-size:.72rem;"><i class="bi bi-clock"></i> {{ $log->duration }}</span>
                            <span class="text-muted" style="font-size:.72rem;"><i class="bi bi-calendar3"></i> {{ $log->log_date->format('M j, Y') }}</span>
                        </div>
                    </div>
                </div>
            @empty
                <p class="text-muted text-center py-4">No coding activity yet.</p>
            @endforelse
        </div>
    </div>
</div>
@endsection
