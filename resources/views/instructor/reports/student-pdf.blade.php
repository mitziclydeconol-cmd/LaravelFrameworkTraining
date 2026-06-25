<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Student Report – {{ $student->name }}</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: Arial, sans-serif; font-size: 12px; color: #1E293B; padding: 24px; }
        .header { display: flex; justify-content: space-between; align-items: flex-start; border-bottom: 3px solid #4F46E5; padding-bottom: 16px; margin-bottom: 20px; }
        .brand { font-size: 20px; font-weight: 800; color: #4F46E5; }
        .report-title { font-size: 11px; color: #64748B; margin-top: 2px; }
        .student-name { font-size: 18px; font-weight: 700; }
        .meta { font-size: 11px; color: #64748B; margin-top: 4px; }
        .stats-row { display: flex; gap: 16px; margin-bottom: 20px; }
        .stat-box { flex: 1; border: 1px solid #E2E8F0; border-radius: 8px; padding: 12px; text-align: center; }
        .stat-val { font-size: 22px; font-weight: 700; color: #4F46E5; }
        .stat-lbl { font-size: 10px; color: #64748B; margin-top: 2px; }
        h2 { font-size: 13px; font-weight: 700; color: #1E293B; border-bottom: 1px solid #E2E8F0; padding-bottom: 6px; margin-bottom: 12px; margin-top: 20px; }
        table { width: 100%; border-collapse: collapse; font-size: 11px; }
        th { background: #F8FAFC; text-align: left; padding: 6px 8px; font-size: 10px; text-transform: uppercase; color: #64748B; border-bottom: 1px solid #E2E8F0; }
        td { padding: 6px 8px; border-bottom: 1px solid #F1F5F9; vertical-align: top; }
        tr:last-child td { border-bottom: none; }
        .badge { display: inline-block; padding: 2px 6px; border-radius: 4px; font-size: 9px; font-weight: 600; }
        .progress-bar-wrap { background: #F1F5F9; border-radius: 99px; height: 6px; margin-top: 3px; }
        .progress-bar-fill { height: 6px; border-radius: 99px; background: #4F46E5; }
        .badge-grid { display: flex; flex-wrap: wrap; gap: 6px; margin-top: 4px; }
        .badge-item { background: #EEF2FF; color: #4F46E5; padding: 3px 8px; border-radius: 4px; font-size: 10px; }
        .footer { margin-top: 30px; border-top: 1px solid #E2E8F0; padding-top: 10px; font-size: 10px; color: #94A3B8; display: flex; justify-content: space-between; }
        .diff-easy { background: #DCFCE7; color: #166534; }
        .diff-medium { background: #FEF3C7; color: #92400E; }
        .diff-hard { background: #FEE2E2; color: #991B1B; }
        @media print {
            body { padding: 0; }
            .no-print { display: none; }
        }
    </style>
</head>
<body>
<div class="no-print" style="background:#4F46E5;color:#fff;padding:12px 24px;margin:-24px -24px 24px;display:flex;justify-content:space-between;align-items:center;">
    <span style="font-weight:600;">📄 Student Progress Report — Press Ctrl+P to save as PDF</span>
    <button onclick="window.print()" style="background:#fff;color:#4F46E5;border:none;padding:6px 16px;border-radius:6px;font-weight:600;cursor:pointer;">🖨 Print / Save PDF</button>
</div>

<div class="header">
    <div>
        <div class="brand">⌨ CodeTrack AI</div>
        <div class="report-title">Student Progress Report · Generated {{ now()->format('F j, Y') }}</div>
    </div>
    <div style="text-align:right;">
        <div class="student-name">{{ $student->name }}</div>
        <div class="meta">{{ $student->email }} @if($student->student_id) · {{ $student->student_id }} @endif</div>
        <div class="meta">Joined {{ $student->created_at->format('M Y') }}</div>
    </div>
</div>

<div class="stats-row">
    <div class="stat-box">
        <div class="stat-val">{{ floor($totalMinutes/60) }}h {{ $totalMinutes%60 }}m</div>
        <div class="stat-lbl">Total Time Coded</div>
    </div>
    <div class="stat-box">
        <div class="stat-val">{{ $totalLogs }}</div>
        <div class="stat-lbl">Log Entries</div>
    </div>
    <div class="stat-box">
        <div class="stat-val">{{ $student->subjects->count() }}</div>
        <div class="stat-lbl">Subjects</div>
    </div>
    <div class="stat-box">
        <div class="stat-val">{{ $student->badges->count() }}</div>
        <div class="stat-lbl">Badges Earned</div>
    </div>
    @if($avgAssessment)
    <div class="stat-box">
        <div class="stat-val">{{ $avgAssessment }}/5</div>
        <div class="stat-lbl">Avg Self-Assessment</div>
    </div>
    @endif
</div>

<h2>Subject Progress</h2>
<table>
    <thead><tr><th>Subject</th><th>Code</th><th>Sessions</th><th>Hours</th><th>Progress</th></tr></thead>
    <tbody>
    @foreach($subjectProgress as $p)
    <tr>
        <td>{{ $p['subject']->name }}</td>
        <td><span style="font-family:monospace;background:#EEF2FF;color:#4F46E5;padding:1px 5px;border-radius:3px;font-size:10px;">{{ $p['subject']->code }}</span></td>
        <td>{{ $p['logs'] }}</td>
        <td>{{ $p['hours'] }}h</td>
        <td style="width:120px;">
            @php $maxH = collect($subjectProgress)->max('hours') ?: 1; $pct = round($p['hours']/$maxH*100); @endphp
            <div class="progress-bar-wrap"><div class="progress-bar-fill" style="width:{{ $pct }}%;background:{{ $p['subject']->color }};"></div></div>
        </td>
    </tr>
    @endforeach
    </tbody>
</table>

<h2>Language Usage</h2>
<table>
    <thead><tr><th>Language</th><th>Sessions</th><th>Total Time</th></tr></thead>
    <tbody>
    @foreach($languageStats as $lang)
    <tr>
        <td><strong>{{ $lang->programming_language }}</strong></td>
        <td>{{ $lang->count }}</td>
        <td>{{ floor($lang->total_minutes/60) }}h {{ $lang->total_minutes%60 }}m</td>
    </tr>
    @endforeach
    </tbody>
</table>

@if($student->badges->count())
<h2>Badges Earned</h2>
<div class="badge-grid">
    @foreach($student->badges as $badge)
    <div class="badge-item">{{ $badge->name }}</div>
    @endforeach
</div>
@endif

<h2>Recent Coding Sessions (Last 20)</h2>
<table>
    <thead><tr><th>Date</th><th>Title</th><th>Subject</th><th>Language</th><th>Duration</th><th>Difficulty</th><th>Self-Assess</th></tr></thead>
    <tbody>
    @foreach($recentLogs as $log)
    <tr>
        <td>{{ $log->log_date->format('M j, Y') }}</td>
        <td>{{ Str::limit($log->title, 40) }}</td>
        <td>{{ $log->subject?->code ?? '—' }}</td>
        <td><span style="font-family:monospace;font-size:10px;">{{ $log->programming_language }}</span></td>
        <td>{{ $log->duration }}</td>
        <td><span class="badge diff-{{ $log->difficulty }}">{{ ucfirst($log->difficulty) }}</span></td>
        <td>{{ $log->selfAssessment ? $log->selfAssessment->average_score.'/5' : '—' }}</td>
    </tr>
    @endforeach
    </tbody>
</table>

<div class="footer">
    <span>CodeTrack AI · Student Progress Report</span>
    <span>Generated on {{ now()->format('F j, Y \a\t g:i A') }}</span>
</div>
</body>
</html>
