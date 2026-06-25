@extends('layouts.app')

@section('title', 'Student Dashboard')

@section('breadcrumb')
    <li class="breadcrumb-item active">Dashboard</li>
@endsection

@section('content')
{{-- ── Header ──────────────────────────────────────────── --}}
<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h4 class="fw-700 mb-0" style="font-weight:700;">
            Good {{ now()->hour < 12 ? 'morning' : (now()->hour < 17 ? 'afternoon' : 'evening') }},
            {{ explode(' ', $user->name)[0] }}! 👋
        </h4>
        <p class="text-muted mb-0" style="font-size:.875rem;">{{ now()->format('l, F j, Y') }}</p>
    </div>
    <a href="{{ route('student.logs.create') }}" class="btn btn-primary d-flex align-items-center gap-2">
        <i class="bi bi-plus-lg"></i> Log Coding Session
    </a>
</div>

{{-- ── Stat Cards ──────────────────────────────────────── --}}
<div class="row g-3 mb-4">
    <div class="col-6 col-lg-3">
        <div class="ct-stat-card">
            <div class="ct-stat-icon mb-3" style="background:#EEF2FF;">
                <i class="bi bi-clock" style="color:#4F46E5;"></i>
            </div>
            <div class="ct-stat-value">
                {{ floor($totalMinutes / 60) }}<small class="fs-5 fw-normal">h</small>
                {{ $totalMinutes % 60 }}<small class="fs-5 fw-normal">m</small>
            </div>
            <div class="ct-stat-label">Total Coding Time</div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="ct-stat-card">
            <div class="ct-stat-icon mb-3" style="background:#F0FDF4;">
                <i class="bi bi-journal-check" style="color:#16A34A;"></i>
            </div>
            <div class="ct-stat-value">{{ $totalLogs }}</div>
            <div class="ct-stat-label">Activities Logged</div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="ct-stat-card">
            <div class="ct-stat-icon mb-3" style="background:#FFF7ED;">
                <i class="bi bi-calendar-week" style="color:#D97706;"></i>
            </div>
            <div class="ct-stat-value">
                {{ floor($weeklyMinutes / 60) }}<small class="fs-5 fw-normal">h</small>
                {{ $weeklyMinutes % 60 }}<small class="fs-5 fw-normal">m</small>
            </div>
            <div class="ct-stat-label">This Week</div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="ct-stat-card">
            <div class="ct-stat-icon mb-3" style="background:#FDF4FF;">
                <i class="bi bi-fire" style="color:#9333EA;"></i>
            </div>
            <div class="ct-stat-value">{{ $currentStreak }}</div>
            <div class="ct-stat-label">Day Streak 🔥</div>
        </div>
    </div>
</div>

<div class="row g-3 mb-4">
    {{-- Weekly Activity Chart --}}
    <div class="col-lg-8">
        <div class="ct-card p-4 h-100">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <h6 class="fw-semibold mb-0"><i class="bi bi-bar-chart me-2 text-primary"></i>Weekly Coding Activity</h6>
                <span class="badge bg-primary-subtle text-primary" style="font-size:.7rem;">Last 7 Days</span>
            </div>
            <canvas id="weeklyChart" height="120"></canvas>
        </div>
    </div>

    {{-- Language Stats --}}
    <div class="col-lg-4">
        <div class="ct-card p-4 h-100">
            <h6 class="fw-semibold mb-3"><i class="bi bi-code-slash me-2 text-primary"></i>Top Languages</h6>
            @forelse($languageStats as $lang)
                @php
                    $maxMins = $languageStats->first()->total_minutes ?: 1;
                    $pct = round($lang->total_minutes / $maxMins * 100);
                    $colors = ['#4F46E5','#0891B2','#D97706','#16A34A','#DC2626','#7C3AED'];
                    $color = $colors[$loop->index % count($colors)];
                @endphp
                <div class="mb-3">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <span class="lang-badge" style="background:{{ $color }}22;color:{{ $color }};">{{ $lang->programming_language }}</span>
                        <small class="text-muted">{{ $lang->count }} {{ Str::plural('log', $lang->count) }}</small>
                    </div>
                    <div class="progress ct-progress">
                        <div class="progress-bar" style="width:{{ $pct }}%;background:{{ $color }};"></div>
                    </div>
                </div>
            @empty
                <p class="text-muted text-center mt-4">No logs yet. Start coding! 🚀</p>
            @endforelse
        </div>
    </div>
</div>

<div class="row g-3">
    {{-- Subject Progress --}}
    <div class="col-lg-6">
        <div class="ct-card p-4 h-100">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <h6 class="fw-semibold mb-0"><i class="bi bi-book me-2 text-primary"></i>Progress by Subject</h6>
            </div>
            @forelse($subjectProgress as $progress)
                <div class="mb-3">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <div>
                            <span class="fw-medium" style="font-size:.875rem;">{{ $progress['subject']->name }}</span>
                            <span class="badge ms-2" style="background:{{ $progress['subject']->color }}22;color:{{ $progress['subject']->color }};font-size:.65rem;">
                                {{ $progress['subject']->code }}
                            </span>
                        </div>
                        <span class="text-muted" style="font-size:.78rem;">{{ $progress['hours'] }}h · {{ $progress['logs'] }} logs</span>
                    </div>
                    @php $maxHours = $subjectProgress->max('hours') ?: 1; $pct = round($progress['hours'] / $maxHours * 100); @endphp
                    <div class="progress ct-progress">
                        <div class="progress-bar" style="width:{{ $pct }}%;background:{{ $progress['subject']->color }};"></div>
                    </div>
                </div>
            @empty
                <div class="text-center py-3">
                    <i class="bi bi-book text-muted fs-2"></i>
                    <p class="text-muted mt-2 mb-0">No subjects enrolled yet.</p>
                </div>
            @endforelse
        </div>
    </div>

    {{-- Recent Logs --}}
    <div class="col-lg-6">
        <div class="ct-card p-4 h-100">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <h6 class="fw-semibold mb-0"><i class="bi bi-clock-history me-2 text-primary"></i>Recent Logs</h6>
                <a href="{{ route('student.logs.index') }}" class="btn btn-sm btn-outline-primary" style="font-size:.75rem;">View All</a>
            </div>
            @forelse($recentLogs as $log)
                <div class="d-flex align-items-start gap-3 mb-3 pb-3 {{ !$loop->last ? 'border-bottom' : '' }}">
                    <div class="ct-stat-icon flex-shrink-0" style="width:36px;height:36px;background:#EEF2FF;font-size:.9rem;">
                        <i class="bi bi-code-square" style="color:#4F46E5;"></i>
                    </div>
                    <div class="flex-grow-1 min-w-0">
                        <a href="{{ route('student.logs.show', $log) }}" class="fw-medium text-decoration-none text-dark d-block text-truncate" style="font-size:.875rem;">
                            {{ $log->title }}
                        </a>
                        <div class="d-flex gap-2 align-items-center mt-1 flex-wrap">
                            <span class="lang-badge" style="background:#EEF2FF;color:#4F46E5;">{{ $log->programming_language }}</span>
                            <span class="text-muted" style="font-size:.72rem;"><i class="bi bi-clock"></i> {{ $log->duration }}</span>
                            <span class="text-muted" style="font-size:.72rem;"><i class="bi bi-calendar3"></i> {{ $log->log_date->format('M j') }}</span>
                        </div>
                    </div>
                </div>
            @empty
                <div class="text-center py-4">
                    <i class="bi bi-journal-plus text-muted fs-1"></i>
                    <p class="text-muted mt-2">No logs yet.<br>Start by logging your first session!</p>
                    <a href="{{ route('student.logs.create') }}" class="btn btn-primary btn-sm">Create First Log</a>
                </div>
            @endforelse
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
const ctx = document.getElementById('weeklyChart').getContext('2d');
new Chart(ctx, {
    type: 'bar',
    data: {
        labels: @json($weeklyData['labels']),
        datasets: [{
            label: 'Hours Coded',
            data: @json($weeklyData['data']),
            backgroundColor: 'rgba(79, 70, 229, 0.15)',
            borderColor: '#4F46E5',
            borderWidth: 2,
            borderRadius: 6,
            hoverBackgroundColor: 'rgba(79, 70, 229, 0.3)',
        }]
    },
    options: {
        responsive: true,
        plugins: { legend: { display: false }, tooltip: {
            callbacks: { label: ctx => ` ${ctx.raw}h coded` }
        }},
        scales: {
            y: { beginAtZero: true, grid: { color: '#F1F5F9' }, ticks: { font: { size: 11 } } },
            x: { grid: { display: false }, ticks: { font: { size: 11 } } }
        }
    }
});
</script>
@endpush
