@extends('layouts.app')

@section('title', 'Instructor Dashboard')

@section('breadcrumb')
    <li class="breadcrumb-item active">Dashboard</li>
@endsection

@section('content')
<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h4 class="fw-bold mb-0">Instructor Dashboard</h4>
        <p class="text-muted mb-0" style="font-size:.875rem;">{{ now()->format('l, F j, Y') }}</p>
    </div>
    <a href="{{ route('instructor.export.all') }}" class="btn btn-outline-primary d-flex align-items-center gap-2">
        <i class="bi bi-download"></i> Export All Data
    </a>
</div>

{{-- Stat Cards --}}
<div class="row g-3 mb-4">
    <div class="col-6 col-lg-3">
        <div class="ct-stat-card">
            <div class="ct-stat-icon mb-3" style="background:#EEF2FF;"><i class="bi bi-people" style="color:#4F46E5;"></i></div>
            <div class="ct-stat-value">{{ $totalStudents }}</div>
            <div class="ct-stat-label">Total Students</div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="ct-stat-card">
            <div class="ct-stat-icon mb-3" style="background:#F0FDF4;"><i class="bi bi-book" style="color:#16A34A;"></i></div>
            <div class="ct-stat-value">{{ $totalSubjects }}</div>
            <div class="ct-stat-label">Subjects</div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="ct-stat-card">
            <div class="ct-stat-icon mb-3" style="background:#FFF7ED;"><i class="bi bi-journal-check" style="color:#D97706;"></i></div>
            <div class="ct-stat-value">{{ $totalLogs }}</div>
            <div class="ct-stat-label">Total Log Entries</div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="ct-stat-card">
            <div class="ct-stat-icon mb-3" style="background:#FDF4FF;"><i class="bi bi-lightning" style="color:#9333EA;"></i></div>
            <div class="ct-stat-value">{{ $activeThisWeek }}</div>
            <div class="ct-stat-label">Active This Week</div>
        </div>
    </div>
</div>

<div class="row g-3 mb-4">
    {{-- Weekly Activity Chart --}}
    <div class="col-lg-8">
        <div class="ct-card p-4 h-100">
            <h6 class="fw-semibold mb-3"><i class="bi bi-bar-chart me-2 text-primary"></i>System-wide Activity (Last 7 Days)</h6>
            <canvas id="weeklyChart" height="120"></canvas>
        </div>
    </div>

    {{-- Language Distribution --}}
    <div class="col-lg-4">
        <div class="ct-card p-4 h-100">
            <h6 class="fw-semibold mb-3"><i class="bi bi-pie-chart me-2 text-primary"></i>Language Distribution</h6>
            <canvas id="langChart" height="200"></canvas>
        </div>
    </div>
</div>

<div class="row g-3">
    {{-- Top Students --}}
    <div class="col-lg-6">
        <div class="ct-card p-4 h-100">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <h6 class="fw-semibold mb-0"><i class="bi bi-trophy me-2 text-warning"></i>Top Students This Month</h6>
                <a href="{{ route('instructor.students.index') }}" class="btn btn-sm btn-outline-primary" style="font-size:.75rem;">View All</a>
            </div>
            @forelse($topStudents as $student)
                <div class="d-flex align-items-center gap-3 mb-3 pb-3 {{ !$loop->last ? 'border-bottom' : '' }}">
                    <div class="fw-bold text-muted" style="width:20px;font-size:.85rem;">{{ $loop->iteration }}</div>
                    <div class="rounded-circle d-flex align-items-center justify-content-center text-white fw-bold"
                         style="width:36px;height:36px;min-width:36px;background:linear-gradient(135deg,#4F46E5,#06B6D4);font-size:.75rem;">
                        {{ strtoupper(substr($student->name, 0, 2)) }}
                    </div>
                    <div class="flex-grow-1 min-w-0">
                        <a href="{{ route('instructor.students.show', $student) }}" class="fw-medium text-dark text-decoration-none d-block text-truncate" style="font-size:.875rem;">
                            {{ $student->name }}
                        </a>
                        <small class="text-muted">{{ $student->monthly_logs }} logs · {{ round($student->monthly_minutes / 60, 1) }}h this month</small>
                    </div>
                    @if($loop->iteration <= 3)
                        <span>{{ ['🥇','🥈','🥉'][$loop->index] }}</span>
                    @endif
                </div>
            @empty
                <p class="text-muted text-center py-3">No activity this month yet.</p>
            @endforelse
        </div>
    </div>

    {{-- Subject Engagement --}}
    <div class="col-lg-6">
        <div class="ct-card p-4 h-100">
            <h6 class="fw-semibold mb-3"><i class="bi bi-book me-2 text-primary"></i>Subject Engagement</h6>
            @forelse($subjectStats as $subject)
                @php
                    $maxLogs = $subjectStats->max('coding_logs_count') ?: 1;
                    $pct = round($subject->coding_logs_count / $maxLogs * 100);
                @endphp
                <div class="mb-3">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <span class="fw-medium" style="font-size:.875rem;">{{ $subject->name }}</span>
                        <span class="text-muted" style="font-size:.78rem;">
                            {{ $subject->coding_logs_count }} logs ·
                            {{ round(($subject->coding_logs_sum_hours_60_minutes ?? 0) / 60, 1) }}h
                        </span>
                    </div>
                    <div class="progress ct-progress">
                        <div class="progress-bar" style="width:{{ $pct }}%;background:{{ $subject->color }};"></div>
                    </div>
                </div>
            @empty
                <p class="text-muted text-center py-3">No subjects created yet.</p>
            @endforelse
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Weekly Chart
new Chart(document.getElementById('weeklyChart').getContext('2d'), {
    type: 'line',
    data: {
        labels: @json($weeklyData['labels']),
        datasets: [
            {
                label: 'Hours Coded',
                data: @json($weeklyData['minutes']),
                borderColor: '#4F46E5',
                backgroundColor: 'rgba(79, 70, 229, 0.08)',
                fill: true,
                tension: 0.4,
                pointBackgroundColor: '#4F46E5',
            },
            {
                label: 'Log Entries',
                data: @json($weeklyData['counts']),
                borderColor: '#06B6D4',
                backgroundColor: 'transparent',
                tension: 0.4,
                borderDash: [5, 5],
                pointBackgroundColor: '#06B6D4',
            }
        ]
    },
    options: {
        responsive: true,
        plugins: { legend: { position: 'bottom', labels: { font: { size: 11 } } } },
        scales: {
            y: { beginAtZero: true, grid: { color: '#F1F5F9' } },
            x: { grid: { display: false } }
        }
    }
});

// Language Doughnut
const langLabels = @json($languageStats->pluck('programming_language'));
const langData   = @json($languageStats->pluck('count'));
const colors     = ['#4F46E5','#0891B2','#D97706','#16A34A','#DC2626','#7C3AED','#EA580C','#0F766E'];

new Chart(document.getElementById('langChart').getContext('2d'), {
    type: 'doughnut',
    data: {
        labels: langLabels,
        datasets: [{ data: langData, backgroundColor: colors, borderWidth: 2, borderColor: '#fff' }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: { position: 'bottom', labels: { font: { size: 11 }, padding: 12 } }
        },
        cutout: '65%',
    }
});
</script>
@endpush
