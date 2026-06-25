<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'CodeTrack AI') }} – @yield('title', 'Dashboard')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
    <style>
        :root {
            --ct-primary:#4F46E5;--ct-secondary:#7C3AED;--ct-accent:#06B6D4;
            --ct-success:#10B981;--ct-warning:#F59E0B;--ct-danger:#EF4444;
            --ct-dark:#1E1B4B;--ct-sidebar-w:265px;
        }
        body{font-family:'Inter',sans-serif;background:#F1F5F9;}
        code,pre,.font-mono{font-family:'JetBrains Mono',monospace;}
        .ct-sidebar{position:fixed;top:0;left:0;bottom:0;width:var(--ct-sidebar-w);background:#1E1B4B;color:#fff;display:flex;flex-direction:column;z-index:1000;overflow-y:auto;transition:transform .25s ease;}
        .ct-sidebar-brand{padding:1.25rem 1.1rem;border-bottom:1px solid rgba(255,255,255,.08);display:flex;align-items:center;gap:.6rem;}
        .ct-sidebar-brand .brand-icon{width:34px;height:34px;background:linear-gradient(135deg,#4F46E5,#06B6D4);border-radius:8px;display:inline-flex;align-items:center;justify-content:center;font-size:1rem;flex-shrink:0;}
        .ct-sidebar-brand span{font-weight:700;font-size:1.05rem;letter-spacing:-.3px;}
        .ct-sidebar-nav{padding:.75rem .65rem;flex:1;}
        .ct-nav-label{font-size:.65rem;font-weight:600;letter-spacing:.08em;text-transform:uppercase;color:rgba(255,255,255,.4);padding:.6rem .5rem .2rem;}
        .ct-nav-link{display:flex;align-items:center;gap:.55rem;padding:.48rem .8rem;border-radius:7px;color:rgba(255,255,255,.72);text-decoration:none;font-size:.82rem;font-weight:500;transition:all .15s ease;margin-bottom:1px;}
        .ct-nav-link:hover,.ct-nav-link.active{background:rgba(255,255,255,.1);color:#fff;}
        .ct-nav-link.active{background:var(--ct-primary);}
        .ct-nav-link i{width:16px;text-align:center;font-size:.95rem;}
        .ct-main{margin-left:var(--ct-sidebar-w);min-height:100vh;}
        .ct-topbar{background:#fff;border-bottom:1px solid #E2E8F0;padding:.75rem 1.5rem;position:sticky;top:0;z-index:100;}
        .ct-content{padding:1.5rem;}
        .ct-card{background:#fff;border-radius:12px;border:1px solid #E2E8F0;box-shadow:0 1px 3px rgba(0,0,0,.05);}
        .ct-stat-card{background:#fff;border-radius:12px;border:1px solid #E2E8F0;padding:1.1rem 1.25rem;transition:box-shadow .2s;}
        .ct-stat-card:hover{box-shadow:0 4px 15px rgba(0,0,0,.08);}
        .ct-stat-icon{width:44px;height:44px;border-radius:10px;display:flex;align-items:center;justify-content:center;font-size:1.25rem;}
        .ct-stat-value{font-size:1.75rem;font-weight:700;line-height:1.1;}
        .ct-stat-label{font-size:.75rem;color:#64748B;font-weight:500;}
        .lang-badge{display:inline-block;padding:.18rem .55rem;border-radius:5px;font-size:.7rem;font-weight:600;font-family:'JetBrains Mono',monospace;}
        .diff-easy{background:#DCFCE7;color:#166534;}
        .diff-medium{background:#FEF3C7;color:#92400E;}
        .diff-hard{background:#FEE2E2;color:#991B1B;}
        .code-block{background:#0F172A;color:#E2E8F0;border-radius:10px;padding:1.1rem;font-family:'JetBrains Mono',monospace;font-size:.8rem;line-height:1.6;overflow-x:auto;max-height:420px;}
        .ct-progress{height:7px;border-radius:99px;}
        .ct-table th{font-size:.72rem;font-weight:600;text-transform:uppercase;letter-spacing:.05em;color:#64748B;border-bottom:2px solid #E2E8F0;}
        .ct-table td{vertical-align:middle;}
        .ai-feedback-box{background:linear-gradient(135deg,#EEF2FF,#F0F9FF);border:1px solid #C7D2FE;border-radius:12px;padding:1.25rem;}
        .heatmap-cell{width:13px;height:13px;border-radius:2px;background:#E2E8F0;display:inline-block;cursor:pointer;}
        .heatmap-cell[data-level="1"]{background:#A5B4FC;}
        .heatmap-cell[data-level="2"]{background:#818CF8;}
        .heatmap-cell[data-level="3"]{background:#6366F1;}
        .heatmap-cell[data-level="4"]{background:#4338CA;}
        .badge-card{border-radius:10px;padding:1rem;text-align:center;border:2px solid transparent;transition:all .2s;}
        .badge-card.earned{border-color:currentColor;background:rgba(79,70,229,.05);}
        .badge-card.locked{opacity:.45;filter:grayscale(1);}
        .star-rating .bi{font-size:1.4rem;cursor:pointer;color:#CBD5E1;transition:color .1s;}
        .star-rating .bi.active{color:#F59E0B;}
        @media(max-width:768px){.ct-sidebar{transform:translateX(-100%)}.ct-sidebar.show{transform:translateX(0)}.ct-main{margin-left:0}}
    </style>
    @stack('styles')
</head>
<body>
<nav class="ct-sidebar" id="ctSidebar">
    <div class="ct-sidebar-brand">
        <div class="brand-icon"><i class="bi bi-code-slash text-white"></i></div>
        <span>CodeTrack AI</span>
    </div>
    <div class="ct-sidebar-nav">
        @if(auth()->user()->isStudent())
            <p class="ct-nav-label">Student</p>
            <a href="{{ route('student.dashboard') }}" class="ct-nav-link {{ request()->routeIs('student.dashboard') ? 'active' : '' }}"><i class="bi bi-speedometer2"></i> Dashboard</a>
            <a href="{{ route('student.logs.index') }}" class="ct-nav-link {{ request()->routeIs('student.logs.*') ? 'active' : '' }}"><i class="bi bi-journal-code"></i> Coding Logs</a>
            <a href="{{ route('student.logs.create') }}" class="ct-nav-link"><i class="bi bi-plus-circle"></i> New Log</a>
            <a href="{{ route('student.goals.index') }}" class="ct-nav-link {{ request()->routeIs('student.goals.*') ? 'active' : '' }}"><i class="bi bi-bullseye"></i> Goals</a>
            <a href="{{ route('student.heatmap') }}" class="ct-nav-link {{ request()->routeIs('student.heatmap') ? 'active' : '' }}"><i class="bi bi-calendar3"></i> Activity Heatmap</a>
            <a href="{{ route('student.badges') }}" class="ct-nav-link {{ request()->routeIs('student.badges') ? 'active' : '' }}"><i class="bi bi-award"></i> Badges</a>
            <a href="{{ route('student.suggestions.index') }}" class="ct-nav-link {{ request()->routeIs('student.suggestions.*') ? 'active' : '' }}"><i class="bi bi-lightbulb"></i> AI Suggestions</a>
            <a href="{{ route('leaderboard') }}" class="ct-nav-link {{ request()->routeIs('leaderboard') ? 'active' : '' }}"><i class="bi bi-bar-chart-steps"></i> Leaderboard</a>
        @else
            <p class="ct-nav-label">Instructor</p>
            <a href="{{ route('instructor.dashboard') }}" class="ct-nav-link {{ request()->routeIs('instructor.dashboard') ? 'active' : '' }}"><i class="bi bi-speedometer2"></i> Dashboard</a>
            <a href="{{ route('instructor.students.index') }}" class="ct-nav-link {{ request()->routeIs('instructor.students.*') ? 'active' : '' }}"><i class="bi bi-people"></i> Students</a>
            <a href="{{ route('subjects.index') }}" class="ct-nav-link {{ request()->routeIs('subjects.*') ? 'active' : '' }}"><i class="bi bi-book"></i> Subjects</a>
            <a href="{{ route('instructor.announcements.index') }}" class="ct-nav-link {{ request()->routeIs('instructor.announcements.*') ? 'active' : '' }}"><i class="bi bi-megaphone"></i> Announcements</a>
            <a href="{{ route('leaderboard') }}" class="ct-nav-link {{ request()->routeIs('leaderboard') ? 'active' : '' }}"><i class="bi bi-bar-chart-steps"></i> Leaderboard</a>
            <a href="{{ route('instructor.export.all') }}" class="ct-nav-link"><i class="bi bi-file-earmark-spreadsheet"></i> Export CSV</a>
            <a href="{{ route('instructor.reports.all-pdf') }}" class="ct-nav-link" target="_blank"><i class="bi bi-file-earmark-pdf"></i> PDF Report</a>
        @endif
        <div class="mt-4 pt-3 border-top border-white border-opacity-10">
            <p class="ct-nav-label">Account</p>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="ct-nav-link border-0 w-100 text-start" style="background:none;cursor:pointer;">
                    <i class="bi bi-box-arrow-left"></i> Sign Out
                </button>
            </form>
        </div>
    </div>
</nav>

<div class="ct-main">
    <header class="ct-topbar d-flex align-items-center justify-content-between">
        <div class="d-flex align-items-center gap-3">
            <button class="btn btn-sm btn-light d-md-none" onclick="document.getElementById('ctSidebar').classList.toggle('show')"><i class="bi bi-list fs-5"></i></button>
            <nav aria-label="breadcrumb"><ol class="breadcrumb mb-0" style="font-size:.8rem;">@yield('breadcrumb')</ol></nav>
        </div>
        <div class="d-flex align-items-center gap-3">
            @if(auth()->user()->isStudent())
                <a href="{{ route('student.badges') }}" class="text-decoration-none">
                    <span class="badge rounded-pill" style="background:#F59E0B;font-size:.7rem;">
                        <i class="bi bi-award me-1"></i>{{ auth()->user()->badges->count() }} badges
                    </span>
                </a>
            @endif
            <span class="badge rounded-pill px-3 py-2" style="background:{{ auth()->user()->isInstructor() ? '#7C3AED' : '#4F46E5' }};font-size:.68rem;">
                <i class="bi bi-{{ auth()->user()->isInstructor() ? 'mortarboard' : 'person-badge' }} me-1"></i>{{ ucfirst(auth()->user()->role->name) }}
            </span>
            <div class="d-flex align-items-center gap-2">
                <div class="rounded-circle d-flex align-items-center justify-content-center text-white fw-bold" style="width:32px;height:32px;background:linear-gradient(135deg,#4F46E5,#06B6D4);font-size:.75rem;">
                    {{ strtoupper(substr(auth()->user()->name,0,2)) }}
                </div>
                <div class="d-none d-md-block">
                    <div style="font-size:.8rem;font-weight:600;">{{ auth()->user()->name }}</div>
                    <div style="font-size:.7rem;color:#64748B;">{{ auth()->user()->email }}</div>
                </div>
            </div>
        </div>
    </header>

    <div class="px-4 pt-3">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show d-flex align-items-center gap-2" role="alert">
                <i class="bi bi-check-circle-fill"></i> {{ session('success') }}
                <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
            </div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show d-flex align-items-center gap-2" role="alert">
                <i class="bi bi-exclamation-triangle-fill"></i> {{ session('error') }}
                <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
            </div>
        @endif
        @if(session('ai_feedback'))
            <div class="alert alert-dismissible fade show d-flex align-items-start gap-2" style="background:#EEF2FF;border:1px solid #C7D2FE;" role="alert">
                <i class="bi bi-stars text-primary fs-5"></i>
                <div><strong>AI Feedback Ready!</strong> Check the log page to view it.</div>
                <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
            </div>
        @endif
    </div>

    <main class="ct-content">@yield('content')</main>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>
@stack('scripts')
</body>
</html>
