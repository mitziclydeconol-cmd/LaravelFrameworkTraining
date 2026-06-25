@extends('layouts.app')
@section('title','Leaderboard')
@section('breadcrumb')<li class="breadcrumb-item active">Leaderboard</li>@endsection

@section('content')
<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h4 class="fw-bold mb-0">Leaderboard 🏆</h4>
        <p class="text-muted mb-0" style="font-size:.875rem;">{{ ucfirst($period) }} rankings · {{ $startDate->format('M j') }} – {{ $endDate->format('M j, Y') }}</p>
    </div>
</div>

{{-- Filters --}}
<div class="ct-card p-3 mb-4">
    <form method="GET" class="row g-2 align-items-end">
        <div class="col-md-4">
            <label class="form-label fw-medium mb-1" style="font-size:.78rem;">Period</label>
            <select name="period" class="form-select form-select-sm">
                <option value="weekly"  {{ $period === 'weekly'  ? 'selected' : '' }}>This Week</option>
                <option value="monthly" {{ $period === 'monthly' ? 'selected' : '' }}>This Month</option>
                <option value="alltime" {{ $period === 'alltime' ? 'selected' : '' }}>All Time</option>
            </select>
        </div>
        <div class="col-md-4">
            <label class="form-label fw-medium mb-1" style="font-size:.78rem;">Subject</label>
            <select name="subject_id" class="form-select form-select-sm">
                <option value="">All Subjects</option>
                @foreach($subjects as $s)
                    <option value="{{ $s->id }}" {{ $subjectId == $s->id ? 'selected' : '' }}>{{ $s->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2">
            <button type="submit" class="btn btn-primary btn-sm w-100">Apply</button>
        </div>
        <div class="col-md-2">
            <a href="{{ route('leaderboard') }}" class="btn btn-light btn-sm w-100">Reset</a>
        </div>
    </form>
</div>

{{-- My rank banner (students only) --}}
@if(auth()->user()->isStudent() && $myRank)
<div class="ct-card p-3 mb-4 d-flex align-items-center gap-3" style="border-left:4px solid #4F46E5;">
    <div class="fw-bold fs-3 text-primary">#{{ $myRank }}</div>
    <div>
        <div class="fw-semibold">Your Rank</div>
        <div class="text-muted" style="font-size:.8rem;">
            {{ $me ? floor($me->period_minutes/60).'h '.($me->period_minutes%60).'m · '.$me->period_logs.' logs' : '' }}
        </div>
    </div>
    @if($myRank === 1) <span class="ms-auto fs-2">🥇</span>
    @elseif($myRank === 2) <span class="ms-auto fs-2">🥈</span>
    @elseif($myRank === 3) <span class="ms-auto fs-2">🥉</span>
    @else <span class="ms-auto text-muted" style="font-size:.8rem;">Keep coding to climb! 💪</span>
    @endif
</div>
@endif

{{-- Top 3 Podium --}}
@if($leaderboard->count() >= 3)
<div class="row g-3 mb-4 justify-content-center">
    @php $top3 = $leaderboard->take(3); @endphp
    @foreach([1,0,2] as $idx)
    @php $student = $top3->get($idx); $rank = $idx + 1; @endphp
    @if($student)
    <div class="col-md-4">
        <div class="ct-card p-4 text-center h-100 {{ $rank === 1 ? 'border-warning' : '' }}" style="{{ $rank === 1 ? 'border-width:2px;' : '' }}">
            <div class="mb-2" style="font-size:2.2rem;">{{ ['🥇','🥈','🥉'][$idx] }}</div>
            <div class="rounded-circle d-flex align-items-center justify-content-center text-white fw-bold mx-auto mb-2"
                 style="width:52px;height:52px;background:linear-gradient(135deg,#4F46E5,#06B6D4);font-size:1.1rem;">
                {{ strtoupper(substr($student->name,0,2)) }}
            </div>
            <div class="fw-semibold">{{ $student->name }}</div>
            <div class="text-muted" style="font-size:.78rem;">{{ $student->student_id ?? '' }}</div>
            <div class="mt-2">
                <span class="badge bg-primary-subtle text-primary">{{ floor($student->period_minutes/60) }}h {{ $student->period_minutes%60 }}m</span>
                <span class="badge bg-secondary-subtle text-secondary ms-1">{{ $student->period_logs }} logs</span>
            </div>
        </div>
    </div>
    @endif
    @endforeach
</div>
@endif

{{-- Full Rankings Table --}}
<div class="ct-card overflow-hidden">
    <div class="p-3 border-bottom bg-light">
        <h6 class="fw-semibold mb-0"><i class="bi bi-list-ol me-2 text-primary"></i>Full Rankings</h6>
    </div>
    @if($leaderboard->count())
    <div class="table-responsive">
        <table class="table ct-table mb-0">
            <thead class="bg-light">
                <tr>
                    <th class="ps-4" style="width:60px;">Rank</th>
                    <th>Student</th>
                    <th>Time Coded</th>
                    <th>Log Entries</th>
                    <th class="pe-4">Avg/Day</th>
                </tr>
            </thead>
            <tbody>
                @foreach($leaderboard as $i => $student)
                <tr class="{{ auth()->id() === $student->id ? 'table-primary' : '' }}">
                    <td class="ps-4 fw-bold">
                        @if($i < 3)
                            <span style="font-size:1.1rem;">{{ ['🥇','🥈','🥉'][$i] }}</span>
                        @else
                            <span class="text-muted">#{{ $i + 1 }}</span>
                        @endif
                    </td>
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            <div class="rounded-circle d-flex align-items-center justify-content-center text-white fw-bold"
                                 style="width:32px;height:32px;min-width:32px;background:linear-gradient(135deg,#4F46E5,#06B6D4);font-size:.7rem;">
                                {{ strtoupper(substr($student->name,0,2)) }}
                            </div>
                            <div>
                                <div class="fw-medium" style="font-size:.875rem;">
                                    {{ $student->name }}
                                    @if(auth()->id() === $student->id) <span class="badge bg-primary-subtle text-primary ms-1" style="font-size:.6rem;">You</span> @endif
                                </div>
                                <div class="text-muted" style="font-size:.72rem;">{{ $student->student_id ?? $student->email }}</div>
                            </div>
                        </div>
                    </td>
                    <td>
                        <span class="fw-semibold">{{ floor($student->period_minutes/60) }}h {{ $student->period_minutes%60 }}m</span>
                    </td>
                    <td><span class="badge bg-secondary-subtle text-secondary">{{ $student->period_logs }}</span></td>
                    <td class="pe-4 text-muted" style="font-size:.82rem;">
                        @php $days = max($startDate->diffInDays($endDate), 1); @endphp
                        {{ round($student->period_minutes / $days) }}m
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @else
    <div class="text-center py-5">
        <i class="bi bi-trophy text-muted" style="font-size:2.5rem;"></i>
        <p class="text-muted mt-3">No activity yet for this period.</p>
    </div>
    @endif
</div>
@endsection
