<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>All Students Report – CodeTrack AI</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: Arial, sans-serif; font-size: 12px; color: #1E293B; padding: 24px; }
        .header { border-bottom: 3px solid #4F46E5; padding-bottom: 16px; margin-bottom: 20px; display:flex; justify-content:space-between; align-items:flex-end; }
        .brand { font-size: 20px; font-weight: 800; color: #4F46E5; }
        .stats-row { display: flex; gap: 16px; margin-bottom: 24px; }
        .stat-box { flex: 1; border: 1px solid #E2E8F0; border-radius: 8px; padding: 12px; text-align: center; }
        .stat-val { font-size: 22px; font-weight: 700; color: #4F46E5; }
        .stat-lbl { font-size: 10px; color: #64748B; }
        table { width: 100%; border-collapse: collapse; font-size: 11px; }
        th { background: #F8FAFC; text-align: left; padding: 7px 8px; font-size: 10px; text-transform: uppercase; color: #64748B; border-bottom: 2px solid #E2E8F0; }
        td { padding: 7px 8px; border-bottom: 1px solid #F1F5F9; vertical-align: middle; }
        .progress-bar-wrap { background: #F1F5F9; border-radius: 99px; height: 5px; width: 80px; }
        .progress-bar-fill { height: 5px; border-radius: 99px; background: #4F46E5; }
        .footer { margin-top: 30px; border-top: 1px solid #E2E8F0; padding-top: 10px; font-size: 10px; color: #94A3B8; display: flex; justify-content: space-between; }
        @media print { .no-print { display: none; } body { padding: 0; } }
    </style>
</head>
<body>
<div class="no-print" style="background:#4F46E5;color:#fff;padding:12px 24px;margin:-24px -24px 24px;display:flex;justify-content:space-between;align-items:center;">
    <span style="font-weight:600;">📄 All Students Report — Press Ctrl+P to save as PDF</span>
    <button onclick="window.print()" style="background:#fff;color:#4F46E5;border:none;padding:6px 16px;border-radius:6px;font-weight:600;cursor:pointer;">🖨 Print / Save PDF</button>
</div>

<div class="header">
    <div>
        <div class="brand">⌨ CodeTrack AI</div>
        <div style="font-size:11px;color:#64748B;margin-top:2px;">All Students Progress Report</div>
    </div>
    <div style="text-align:right;font-size:11px;color:#64748B;">Generated {{ now()->format('F j, Y') }}</div>
</div>

<div class="stats-row">
    <div class="stat-box">
        <div class="stat-val">{{ $students->count() }}</div>
        <div class="stat-lbl">Total Students</div>
    </div>
    <div class="stat-box">
        <div class="stat-val">{{ $totalLogs }}</div>
        <div class="stat-lbl">Total Log Entries</div>
    </div>
    <div class="stat-box">
        <div class="stat-val">{{ floor($totalMinutes/60) }}h</div>
        <div class="stat-lbl">Total Hours Coded</div>
    </div>
    <div class="stat-box">
        <div class="stat-val">{{ $students->count() > 0 ? round($totalMinutes/$students->count()/60, 1) : 0 }}h</div>
        <div class="stat-lbl">Avg Hours/Student</div>
    </div>
</div>

<table>
    <thead>
        <tr>
            <th>#</th>
            <th>Student</th>
            <th>Student ID</th>
            <th>Subjects</th>
            <th>Logs</th>
            <th>Time Coded</th>
            <th>Badges</th>
            <th>Progress</th>
        </tr>
    </thead>
    <tbody>
    @php $maxMins = $students->max('coding_logs_sum') ?: 1; @endphp
    @foreach($students->sortByDesc('coding_logs_sum') as $i => $student)
    <tr>
        <td style="color:#94A3B8;">{{ $i + 1 }}</td>
        <td>
            <strong>{{ $student->name }}</strong><br>
            <span style="font-size:10px;color:#64748B;">{{ $student->email }}</span>
        </td>
        <td style="font-family:monospace;font-size:10px;">{{ $student->student_id ?? '—' }}</td>
        <td style="font-size:10px;">{{ $student->subjects->pluck('code')->join(', ') ?: '—' }}</td>
        <td><strong>{{ $student->coding_logs_count }}</strong></td>
        <td>{{ floor(($student->coding_logs_sum ?? 0)/60) }}h {{ ($student->coding_logs_sum ?? 0)%60 }}m</td>
        <td>{{ $student->badges->count() }}</td>
        <td>
            @php $pct = round(($student->coding_logs_sum ?? 0) / $maxMins * 100); @endphp
            <div class="progress-bar-wrap"><div class="progress-bar-fill" style="width:{{ $pct }}%;"></div></div>
        </td>
    </tr>
    @endforeach
    </tbody>
</table>

<div class="footer">
    <span>CodeTrack AI · All Students Progress Report</span>
    <span>{{ now()->format('F j, Y \a\t g:i A') }}</span>
</div>
</body>
</html>
