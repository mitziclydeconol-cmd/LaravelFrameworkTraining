@extends('layouts.app')
@section('title', 'Student Dashboard')
@section('breadcrumb')
    <li class="breadcrumb-item active">Dashboard</li>
@endsection

@section('content')
{{-- Header --}}
<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h4 class="fw-bold mb-0">
            Good {{ now()->hour < 12 ? 'morning' : (now()->hour < 17 ? 'afternoon' : 'evening') }},
            {{ explode(' ', $user->name)[0] }}! 👋
        </h4>
        <p class="text-muted mb-0" style="font-size:.875rem;">{{ now()->format('l, F j, Y') }}</p>
    </div>
    <a href="{{ route('student.logs.create') }}" class="btn btn-primary d-flex align-items-center gap-2">
        <i class="bi bi-plus-lg"></i> Log Session
    </a>
</div>

{{-- Announcements --}}
@if($announcements->count())
<div class="mb-4">
    @foreach($announcements as $ann)
    <div class="d-flex align-items-start gap-3 p-3 rounded-3 mb-2"
         style="background:{{ $ann->priority_color }}12;border:1px solid {{ $ann->priority_color }}30;">
        <i class="bi {{ $ann->priority_icon }} mt-1" style="color:{{ $ann->priority_color }};"></i>
        <div class="flex-grow-1">
            <div class="d-flex align-items-center gap-2 mb-1 flex-wrap">
                @if($ann->is_pinned)<i class="bi bi-pin-angle-fill text-warning" style="font-size:.75rem;"></i>@endif
                <span class="fw-semibold" style="font-size:.875rem;">{{ $ann->title }}</span>
                <span class="badge" style="background:{{ $ann->priority_color }}22;color:{{ $ann->priority_color }};font-size:.62rem;">{{ ucfirst($ann->priority) }}</span>
                @if($ann->subject)<span class="badge bg-secondary-subtle text-secondary" style="font-size:.62rem;">{{ $ann->subject->code }}</span>@endif
            </div>
            <p class="mb-0 text-secondary" style="font-size:.8rem;line-height:1.5;">{{ Str::limit($ann->body, 180) }}</p>
        </div>
        <small class="text-muted text-nowrap" style="font-size:.7rem;">{{ $ann->created_at->diffForHumans() }}</small>
    </div>
    @endforeach
</div>
@endif

{{-- Stat Cards --}}
<div class="row g-3 mb-4">
    <div class="col-6 col-lg-3">
        <div class="ct-stat-card">
            <div class="ct-stat-icon mb-3" style="background:#EEF2FF;"><i class="bi bi-clock" style="color:#4F46E5;"></i></div>
            <div class="ct-stat-value">{{ floor($totalMinutes/60) }}<small class="fs-5 fw-normal">h</small> {{ $totalMinutes%60 }}<small class="fs-5 fw-normal">m</small></div>
            <div class="ct-stat-label">Total Coding Time</div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="ct-stat-card">
            <div class="ct-stat-icon mb-3" style="background:#F0FDF4;"><i class="bi bi-journal-check" style="color:#16A34A;"></i></div>
            <div class="ct-stat-value">{{ $totalLogs }}</div>
            <div class="ct-stat-label">Activities Logged</div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="ct-stat-card">
            <div class="ct-stat-icon mb-3" style="background:#FFF7ED;"><i class="bi bi-calendar-week" style="color:#D97706;"></i></div>
            <div class="ct-stat-value">{{ floor($weeklyMinutes/60) }}<small class="fs-5 fw-normal">h</small> {{ $weeklyMinutes%60 }}<small class="fs-5 fw-normal">m</small></div>
            <div class="ct-stat-label">This Week</div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="ct-stat-card">
            <div class="ct-stat-icon mb-3" style="background:#FDF4FF;"><i class="bi bi-fire" style="color:#9333EA;"></i></div>
            <div class="ct-stat-value">{{ $currentStreak }}</div>
            <div class="ct-stat-label">Day Streak 🔥</div>
        </div>
    </div>
</div>

<div class="row g-3 mb-4">
    {{-- Weekly Chart --}}
    <div class="col-lg-8">
        <div class="ct-card p-4 h-100">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <h6 class="fw-semibold mb-0"><i class="bi bi-bar-chart me-2 text-primary"></i>Weekly Activity</h6>
                <a href="{{ route('student.heatmap') }}" class="btn btn-sm btn-outline-primary" style="font-size:.72rem;">Full Heatmap</a>
            </div>
            <canvas id="weeklyChart" height="120"></canvas>
        </div>
    </div>

    {{-- Badges Snapshot --}}
    <div class="col-lg-4">
        <div class="ct-card p-4 h-100">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <h6 class="fw-semibold mb-0"><i class="bi bi-award me-2 text-warning"></i>Badges</h6>
                <a href="{{ route('student.badges') }}" class="btn btn-sm btn-outline-warning" style="font-size:.72rem;">View All</a>
            </div>
            @if($earnedBadges->count())
            <div class="d-flex flex-wrap gap-2">
                @foreach($earnedBadges->take(9) as $badge)
                <div class="d-flex align-items-center justify-content-center rounded-circle"
                     style="width:42px;height:42px;background:{{ $badge->color }}18;cursor:default;"
                     data-bs-toggle="tooltip" title="{{ $badge->name }}">
                    <i class="bi {{ $badge->icon }}" style="color:{{ $badge->color }};font-size:1.1rem;"></i>
                </div>
                @endforeach
                @if($earnedBadges->count() > 9)
                <div class="d-flex align-items-center justify-content-center rounded-circle"
                     style="width:42px;height:42px;background:#F1F5F9;">
                    <small class="text-muted fw-bold">+{{ $earnedBadges->count()-9 }}</small>
                </div>
                @endif
            </div>
            <p class="text-muted mt-2 mb-0" style="font-size:.75rem;">{{ $earnedBadges->count() }} of {{ $totalBadges }} badges earned</p>
            @else
            <div class="text-center py-3">
                <i class="bi bi-award text-muted fs-2"></i>
                <p class="text-muted mt-2 mb-2" style="font-size:.8rem;">No badges yet! Keep logging to earn your first.</p>
            </div>
            @endif
        </div>
    </div>
</div>

<div class="row g-3 mb-4">
    {{-- Language Stats --}}
    <div class="col-lg-4">
        <div class="ct-card p-4 h-100">
            <h6 class="fw-semibold mb-3"><i class="bi bi-code-slash me-2 text-primary"></i>Top Languages</h6>
            @forelse($languageStats as $lang)
            @php
                $maxMins = $languageStats->first()->total_minutes ?: 1;
                $pct = round($lang->total_minutes / $maxMins * 100);
                $colors = ['#4F46E5','#0891B2','#D97706','#16A34A','#DC2626','#7C3AED'];
                $color  = $colors[$loop->index % count($colors)];
            @endphp
            <div class="mb-3">
                <div class="d-flex justify-content-between align-items-center mb-1">
                    <span class="lang-badge" style="background:{{ $color }}22;color:{{ $color }};">{{ $lang->programming_language }}</span>
                    <small class="text-muted">{{ $lang->count }} logs</small>
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

    {{-- Subject Progress --}}
    <div class="col-lg-4">
        <div class="ct-card p-4 h-100">
            <h6 class="fw-semibold mb-3"><i class="bi bi-book me-2 text-primary"></i>Subject Progress</h6>
            @forelse($subjectProgress as $progress)
            <div class="mb-3">
                <div class="d-flex justify-content-between align-items-center mb-1">
                    <span class="fw-medium" style="font-size:.82rem;">{{ $progress['subject']->name }}</span>
                    <span class="text-muted" style="font-size:.75rem;">{{ $progress['hours'] }}h</span>
                </div>
                @php $maxH = $subjectProgress->max('hours') ?: 1; $pct = round($progress['hours']/$maxH*100); @endphp
                <div class="progress ct-progress">
                    <div class="progress-bar" style="width:{{ $pct }}%;background:{{ $progress['subject']->color }};"></div>
                </div>
            </div>
            @empty
            <p class="text-muted text-center mt-4">No subjects enrolled.</p>
            @endforelse
        </div>
    </div>

    {{-- Active Goals --}}
    <div class="col-lg-4">
        <div class="ct-card p-4 h-100">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <h6 class="fw-semibold mb-0"><i class="bi bi-bullseye me-2 text-primary"></i>Active Goals</h6>
                <a href="{{ route('student.goals.index') }}" class="btn btn-sm btn-outline-primary" style="font-size:.72rem;">Manage</a>
            </div>
            @forelse($activeGoals as $goal)
            <div class="mb-3">
                <div class="d-flex justify-content-between mb-1">
                    <span class="fw-medium text-truncate" style="font-size:.82rem;max-width:65%;">{{ $goal->title }}</span>
                    <span class="text-muted" style="font-size:.72rem;">{{ $goal->end_date->diffForHumans() }}</span>
                </div>
                @if($goal->target_total_minutes > 0)
                <div class="progress ct-progress mb-1">
                    <div class="progress-bar {{ $goal->is_completed ? 'bg-success' : '' }}"
                         style="width:{{ $goal->time_progress_pct }}%;{{ $goal->is_completed ? '' : 'background:#4F46E5;' }}"></div>
                </div>
                <small class="text-muted">{{ $goal->time_progress_pct }}% time · {{ $goal->logs_progress_pct }}% logs</small>
                @endif
            </div>
            @empty
            <div class="text-center py-3">
                <i class="bi bi-bullseye text-muted fs-2"></i>
                <p class="text-muted mt-2 mb-2" style="font-size:.8rem;">No active goals.</p>
                <a href="{{ route('student.goals.index') }}" class="btn btn-sm btn-outline-primary">Set a Goal</a>
            </div>
            @endforelse
        </div>
    </div>
</div>

{{-- Recent Logs --}}
<div class="ct-card p-4">
    <div class="d-flex align-items-center justify-content-between mb-3">
        <h6 class="fw-semibold mb-0"><i class="bi bi-clock-history me-2 text-primary"></i>Recent Logs</h6>
        <a href="{{ route('student.logs.index') }}" class="btn btn-sm btn-outline-primary" style="font-size:.72rem;">View All</a>
    </div>
    @forelse($recentLogs as $log)
    <div class="d-flex align-items-start gap-3 mb-3 pb-3 {{ !$loop->last ? 'border-bottom' : '' }}">
        <div class="ct-stat-icon flex-shrink-0" style="width:36px;height:36px;background:#EEF2FF;font-size:.9rem;">
            <i class="bi bi-code-square" style="color:#4F46E5;"></i>
        </div>
        <div class="flex-grow-1 min-w-0">
            <a href="{{ route('student.logs.show', $log) }}" class="fw-medium text-decoration-none text-dark d-block text-truncate" style="font-size:.875rem;">{{ $log->title }}</a>
            <div class="d-flex gap-2 align-items-center mt-1 flex-wrap">
                <span class="lang-badge" style="background:#EEF2FF;color:#4F46E5;">{{ $log->programming_language }}</span>
                <span class="badge diff-{{ $log->difficulty }}" style="font-size:.62rem;">{{ ucfirst($log->difficulty) }}</span>
                <span class="text-muted" style="font-size:.72rem;"><i class="bi bi-clock"></i> {{ $log->duration }}</span>
                <span class="text-muted" style="font-size:.72rem;"><i class="bi bi-calendar3"></i> {{ $log->log_date->format('M j') }}</span>
                @if($log->comments_count > 0)
                <span class="text-muted" style="font-size:.72rem;"><i class="bi bi-chat-dots"></i> {{ $log->comments_count }}</span>
                @endif
            </div>
        </div>
    </div>
    @empty
    <div class="text-center py-4">
        <i class="bi bi-journal-plus text-muted fs-1"></i>
        <p class="text-muted mt-2">No logs yet. Start by logging your first session!</p>
        <a href="{{ route('student.logs.create') }}" class="btn btn-primary btn-sm">Create First Log</a>
    </div>
    @endforelse
</div>
@endsection

@push('scripts')
<script>
document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(el => new bootstrap.Tooltip(el));
new Chart(document.getElementById('weeklyChart').getContext('2d'), {
    type: 'bar',
    data: {
        labels: @json($weeklyData['labels']),
        datasets: [{
            label: 'Hours Coded',
            data: @json($weeklyData['data']),
            backgroundColor: 'rgba(79,70,229,.15)',
            borderColor: '#4F46E5',
            borderWidth: 2,
            borderRadius: 6,
        }]
    },
    options: {
        responsive: true,
        plugins: { legend: { display: false }, tooltip: { callbacks: { label: c => ` ${c.raw}h coded` }}},
        scales: {
            y: { beginAtZero: true, grid: { color: '#F1F5F9' }, ticks: { font: { size: 11 }}},
            x: { grid: { display: false }, ticks: { font: { size: 11 }}}
        }
    }
});
</script>
@endpush
