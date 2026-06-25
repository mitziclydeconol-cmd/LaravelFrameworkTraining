@extends('layouts.app')
@section('title','Badges & Achievements')
@section('breadcrumb')<li class="breadcrumb-item active">Badges</li>@endsection

@section('content')
<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h4 class="fw-bold mb-0">Badges & Achievements 🏆</h4>
        <p class="text-muted mb-0" style="font-size:.875rem;">{{ $totalEarned }} of {{ $totalBadges }} badges earned</p>
    </div>
    <div class="ct-stat-card px-4 text-center" style="min-width:120px;">
        <div class="fw-bold fs-4" style="color:#F59E0B;">{{ $totalEarned }}</div>
        <div class="text-muted" style="font-size:.75rem;">Earned</div>
    </div>
</div>

{{-- Overall progress --}}
<div class="ct-card p-4 mb-4">
    <div class="d-flex justify-content-between align-items-center mb-2">
        <span class="fw-medium" style="font-size:.875rem;">Overall Progress</span>
        <span class="text-muted" style="font-size:.82rem;">{{ $totalEarned }}/{{ $totalBadges }}</span>
    </div>
    <div class="progress" style="height:10px;border-radius:99px;">
        <div class="progress-bar" style="width:{{ $totalBadges > 0 ? round($totalEarned/$totalBadges*100) : 0 }}%;background:linear-gradient(90deg,#4F46E5,#7C3AED);"></div>
    </div>
</div>

@php
$typeLabels = ['logs'=>'Log Count','hours'=>'Hours Coded','streak'=>'Coding Streaks','languages'=>'Languages','milestone'=>'Special Milestones'];
$typeIcons  = ['logs'=>'bi-journal-check','hours'=>'bi-clock','streak'=>'bi-fire','languages'=>'bi-translate','milestone'=>'bi-stars'];
@endphp

@foreach($grouped as $type => $badges)
<h6 class="fw-semibold text-muted text-uppercase mb-3 mt-4" style="font-size:.72rem;letter-spacing:.06em;">
    <i class="bi {{ $typeIcons[$type] ?? 'bi-award' }} me-1"></i>{{ $typeLabels[$type] ?? ucfirst($type) }}
</h6>
<div class="row g-3 mb-2">
    @foreach($badges as $badge)
    <div class="col-6 col-md-4 col-lg-3">
        <div class="badge-card h-100 {{ $badge['earned'] ? 'earned' : 'locked' }}" style="color:{{ $badge['color'] }};background:{{ $badge['earned'] ? $badge['color'].'18' : '#F8FAFC' }};border-color:{{ $badge['earned'] ? $badge['color'] : 'transparent' }};">
            <div style="font-size:2rem;" class="mb-2">
                <i class="bi {{ $badge['icon'] }}" style="color:{{ $badge['color'] }};"></i>
            </div>
            <div class="fw-semibold mb-1" style="font-size:.82rem;color:#1E293B;">{{ $badge['name'] }}</div>
            <div class="text-muted" style="font-size:.72rem;">{{ $badge['description'] }}</div>
            @if($badge['earned'] && $badge['earned_at'])
                <div class="mt-2">
                    <span class="badge bg-success-subtle text-success" style="font-size:.62rem;">
                        <i class="bi bi-check me-1"></i>{{ \Carbon\Carbon::parse($badge['earned_at'])->format('M j, Y') }}
                    </span>
                </div>
            @elseif(!$badge['earned'])
                <div class="mt-2">
                    <span class="badge bg-secondary-subtle text-secondary" style="font-size:.62rem;">
                        <i class="bi bi-lock me-1"></i>Locked
                    </span>
                </div>
            @endif
        </div>
    </div>
    @endforeach
</div>
@endforeach
@endsection
