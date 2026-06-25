@extends('layouts.app')
@section('title','Activity Heatmap')
@section('breadcrumb')<li class="breadcrumb-item active">Activity Heatmap</li>@endsection

@section('content')
<div class="mb-4">
    <h4 class="fw-bold mb-0">Activity Heatmap 📅</h4>
    <p class="text-muted mb-0" style="font-size:.875rem;">Your coding activity over the past year</p>
</div>

<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="ct-stat-card text-center">
            <div class="ct-stat-value text-primary">{{ $totalDaysActive }}</div>
            <div class="ct-stat-label">Days Active</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="ct-stat-card text-center">
            <div class="ct-stat-value" style="color:#F97316;">{{ $currentStreak }}</div>
            <div class="ct-stat-label">Current Streak 🔥</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="ct-stat-card text-center">
            <div class="ct-stat-value" style="color:#7C3AED;">{{ $longestStreak }}</div>
            <div class="ct-stat-label">Longest Streak 🏆</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="ct-stat-card text-center">
            <div class="ct-stat-value" style="color:#16A34A;">{{ $totalDaysActive > 0 ? round($totalDaysActive / 52, 1) : 0 }}</div>
            <div class="ct-stat-label">Avg Days/Week</div>
        </div>
    </div>
</div>

<div class="ct-card p-4 mb-4">
    <h6 class="fw-semibold mb-3"><i class="bi bi-calendar3 me-2 text-primary"></i>Past 52 Weeks</h6>

    {{-- Month labels --}}
    <div class="d-flex gap-1 mb-1" style="padding-left:2px;">
        @foreach($monthLabels as $w => $label)
            <div style="width:13px;min-width:13px;font-size:.6rem;color:#94A3B8;text-align:center;">{{ $label }}</div>
        @endforeach
    </div>

    {{-- Heatmap grid --}}
    <div class="d-flex gap-1 align-items-start" style="overflow-x:auto;padding-bottom:4px;">
        @foreach($weeks as $week)
        <div class="d-flex flex-column gap-1">
            @foreach($week as $day)
            <div class="heatmap-cell"
                 data-level="{{ $day['level'] }}"
                 data-bs-toggle="tooltip"
                 title="{{ $day['label'] }}: {{ $day['count'] }} log{{ $day['count'] !== 1 ? 's' : '' }}{{ $day['minutes'] > 0 ? ' (' . floor($day['minutes']/60) . 'h ' . $day['minutes']%60 . 'm)' : '' }}">
            </div>
            @endforeach
        </div>
        @endforeach
    </div>

    {{-- Legend --}}
    <div class="d-flex align-items-center gap-2 mt-3">
        <span class="text-muted" style="font-size:.72rem;">Less</span>
        @foreach([0,1,2,3,4] as $lvl)
            <div class="heatmap-cell" data-level="{{ $lvl }}" style="cursor:default;"></div>
        @endforeach
        <span class="text-muted" style="font-size:.72rem;">More</span>
        <span class="text-muted ms-3" style="font-size:.72rem;">
            <span class="heatmap-cell d-inline-block" data-level="1"></span> &lt;30m &nbsp;
            <span class="heatmap-cell d-inline-block" data-level="2"></span> 30–60m &nbsp;
            <span class="heatmap-cell d-inline-block" data-level="3"></span> 1–2h &nbsp;
            <span class="heatmap-cell d-inline-block" data-level="4"></span> 2h+
        </span>
    </div>
</div>

{{-- Day of week breakdown --}}
<div class="ct-card p-4">
    <h6 class="fw-semibold mb-3"><i class="bi bi-bar-chart me-2 text-primary"></i>Activity by Day of Week</h6>
    <canvas id="dowChart" height="80"></canvas>
</div>
@endsection

@push('scripts')
<script>
// Enable tooltips
document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(el => new bootstrap.Tooltip(el, {placement:'top'}));

// Day of week chart
const dowData = @json(
    collect($weeks)->flatten(1)
        ->groupBy(fn($d) => \Carbon\Carbon::parse($d['date'])->dayOfWeek)
        ->map(fn($days) => round($days->sum('minutes') / max($days->count(), 1)))
        ->toArray()
);
const labels = ['Sun','Mon','Tue','Wed','Thu','Fri','Sat'];
const data   = labels.map((_, i) => dowData[i] ?? 0);
new Chart(document.getElementById('dowChart').getContext('2d'), {
    type: 'bar',
    data: {
        labels,
        datasets: [{
            label: 'Avg Minutes',
            data,
            backgroundColor: data.map(v => v > 60 ? '#4F46E5' : v > 30 ? '#818CF8' : '#C7D2FE'),
            borderRadius: 6,
        }]
    },
    options: {
        responsive: true,
        plugins: { legend: { display: false }, tooltip: { callbacks: { label: ctx => ` ${ctx.raw}min avg` }}},
        scales: { y: { beginAtZero: true, grid: { color: '#F1F5F9' }}, x: { grid: { display: false }}}
    }
});
</script>
@endpush
