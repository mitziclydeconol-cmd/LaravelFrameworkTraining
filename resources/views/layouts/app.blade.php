<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'CodeTrack AI') }} – @yield('title', 'Dashboard')</title>

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">

    <style>
        :root {
            --ct-primary:   #4F46E5;
            --ct-secondary: #7C3AED;
            --ct-accent:    #06B6D4;
            --ct-success:   #10B981;
            --ct-warning:   #F59E0B;
            --ct-danger:    #EF4444;
            --ct-dark:      #1E1B4B;
            --ct-sidebar-w: 260px;
            --ct-sidebar-bg:#1E1B4B;
        }

        body { font-family: 'Inter', sans-serif; background: #F1F5F9; }
        code, pre, .font-mono { font-family: 'JetBrains Mono', monospace; }

        /* ── Sidebar ────────────────────────────── */
        .ct-sidebar {
            position: fixed; top: 0; left: 0; bottom: 0;
            width: var(--ct-sidebar-w);
            background: var(--ct-sidebar-bg);
            color: #fff;
            display: flex; flex-direction: column;
            z-index: 1000; transition: transform .25s ease;
            overflow-y: auto;
        }
        .ct-sidebar-brand {
            padding: 1.5rem 1.25rem;
            border-bottom: 1px solid rgba(255,255,255,.08);
        }
        .ct-sidebar-brand .brand-icon {
            width: 36px; height: 36px;
            background: linear-gradient(135deg, var(--ct-primary), var(--ct-accent));
            border-radius: 8px;
            display: inline-flex; align-items: center; justify-content: center;
            font-size: 1.1rem; margin-right: .6rem;
        }
        .ct-sidebar-brand span { font-weight: 700; font-size: 1.15rem; letter-spacing: -.3px; }
        .ct-sidebar-nav { padding: 1rem .75rem; flex: 1; }
        .ct-nav-label {
            font-size: .68rem; font-weight: 600; letter-spacing: .08em;
            text-transform: uppercase; color: rgba(255,255,255,.4);
            padding: .75rem .5rem .25rem;
        }
        .ct-nav-link {
            display: flex; align-items: center; gap: .6rem;
            padding: .55rem .85rem; border-radius: 8px;
            color: rgba(255,255,255,.72); text-decoration: none;
            font-size: .875rem; font-weight: 500;
            transition: all .15s ease; margin-bottom: 2px;
        }
        .ct-nav-link:hover, .ct-nav-link.active {
            background: rgba(255,255,255,.1);
            color: #fff;
        }
        .ct-nav-link.active { background: var(--ct-primary); }
        .ct-nav-link i { width: 18px; text-align: center; font-size: 1rem; }

        /* ── Main Content ───────────────────────── */
        .ct-main { margin-left: var(--ct-sidebar-w); min-height: 100vh; }
        .ct-topbar {
            background: #fff; border-bottom: 1px solid #E2E8F0;
            padding: .85rem 1.75rem;
            position: sticky; top: 0; z-index: 100;
        }
        .ct-content { padding: 1.75rem; }

        /* ── Cards ──────────────────────────────── */
        .ct-card {
            background: #fff; border-radius: 12px;
            border: 1px solid #E2E8F0;
            box-shadow: 0 1px 3px rgba(0,0,0,.05);
        }
        .ct-stat-card {
            background: #fff; border-radius: 12px;
            border: 1px solid #E2E8F0;
            padding: 1.25rem 1.5rem;
            transition: box-shadow .2s;
        }
        .ct-stat-card:hover { box-shadow: 0 4px 15px rgba(0,0,0,.08); }
        .ct-stat-icon {
            width: 48px; height: 48px; border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
            font-size: 1.4rem;
        }
        .ct-stat-value { font-size: 1.9rem; font-weight: 700; line-height: 1.1; }
        .ct-stat-label { font-size: .8rem; color: #64748B; font-weight: 500; }

        /* ── Badges ─────────────────────────────── */
        .lang-badge {
            display: inline-block; padding: .2rem .6rem;
            border-radius: 6px; font-size: .72rem; font-weight: 600;
            font-family: 'JetBrains Mono', monospace;
        }
        .diff-easy   { background: #DCFCE7; color: #166534; }
        .diff-medium { background: #FEF3C7; color: #92400E; }
        .diff-hard   { background: #FEE2E2; color: #991B1B; }

        /* ── Code Block ─────────────────────────── */
        .code-block {
            background: #0F172A; color: #E2E8F0;
            border-radius: 10px; padding: 1.25rem;
            font-family: 'JetBrains Mono', monospace;
            font-size: .82rem; line-height: 1.6;
            overflow-x: auto; max-height: 450px;
        }

        /* ── Progress Bar ───────────────────────── */
        .ct-progress { height: 8px; border-radius: 99px; }

        /* ── Table ──────────────────────────────── */
        .ct-table th { font-size: .75rem; font-weight: 600; text-transform: uppercase;
                       letter-spacing: .05em; color: #64748B; border-bottom: 2px solid #E2E8F0; }
        .ct-table td { vertical-align: middle; }

        /* ── AI Feedback Box ────────────────────── */
        .ai-feedback-box {
            background: linear-gradient(135deg, #EEF2FF, #F0F9FF);
            border: 1px solid #C7D2FE; border-radius: 12px; padding: 1.5rem;
        }
        .ai-feedback-box p { margin-bottom: .5rem; }
        .ai-feedback-box h1, .ai-feedback-box h2, .ai-feedback-box h3 {
            font-size: 1rem; font-weight: 600; margin: 1rem 0 .25rem;
        }

        /* ── Heatmap ────────────────────────────── */
        .heatmap-cell {
            width: 18px; height: 18px; border-radius: 3px;
            background: #E2E8F0; display: inline-block;
        }
        .heatmap-cell[data-level="1"] { background: #A5B4FC; }
        .heatmap-cell[data-level="2"] { background: #818CF8; }
        .heatmap-cell[data-level="3"] { background: #6366F1; }
        .heatmap-cell[data-level="4"] { background: #4338CA; }

        @media (max-width: 768px) {
            .ct-sidebar { transform: translateX(-100%); }
            .ct-sidebar.show { transform: translateX(0); }
            .ct-main { margin-left: 0; }
        }
    </style>

    @stack('styles')
</head>
<body>

{{-- ── Sidebar ─────────────────────────────────────────── --}}
<nav class="ct-sidebar" id="ctSidebar">
    <div class="ct-sidebar-brand">
        <div class="brand-icon"><i class="bi bi-code-slash text-white"></i></div>
        <span>CodeTrack AI</span>
    </div>

    <div class="ct-sidebar-nav">
        @if(auth()->user()->isStudent())
            <p class="ct-nav-label">Student</p>
            <a href="{{ route('student.dashboard') }}" class="ct-nav-link {{ request()->routeIs('student.dashboard') ? 'active' : '' }}">
                <i class="bi bi-speedometer2"></i> Dashboard
            </a>
            <a href="{{ route('student.logs.index') }}" class="ct-nav-link {{ request()->routeIs('student.logs.*') ? 'active' : '' }}">
                <i class="bi bi-journal-code"></i> My Coding Logs
            </a>
            <a href="{{ route('student.logs.create') }}" class="ct-nav-link">
                <i class="bi bi-plus-circle"></i> New Log Entry
            </a>
        @else
            <p class="ct-nav-label">Instructor</p>
            <a href="{{ route('instructor.dashboard') }}" class="ct-nav-link {{ request()->routeIs('instructor.dashboard') ? 'active' : '' }}">
                <i class="bi bi-speedometer2"></i> Dashboard
            </a>
            <a href="{{ route('instructor.students.index') }}" class="ct-nav-link {{ request()->routeIs('instructor.students.*') ? 'active' : '' }}">
                <i class="bi bi-people"></i> Students
            </a>
            <a href="{{ route('subjects.index') }}" class="ct-nav-link {{ request()->routeIs('subjects.*') ? 'active' : '' }}">
                <i class="bi bi-book"></i> Subjects
            </a>
            <a href="{{ route('instructor.export.all') }}" class="ct-nav-link">
                <i class="bi bi-download"></i> Export All Reports
            </a>
        @endif

        <div class="mt-auto pt-3 border-top border-white border-opacity-10 mt-4">
            <p class="ct-nav-label">Account</p>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="ct-nav-link border-0 w-100 text-start" style="background:none; cursor:pointer;">
                    <i class="bi bi-box-arrow-left"></i> Sign Out
                </button>
            </form>
        </div>
    </div>
</nav>

{{-- ── Main Content ────────────────────────────────────── --}}
<div class="ct-main">
    {{-- Topbar --}}
    <header class="ct-topbar d-flex align-items-center justify-content-between">
        <div class="d-flex align-items-center gap-3">
            <button class="btn btn-sm btn-light d-md-none" onclick="document.getElementById('ctSidebar').classList.toggle('show')">
                <i class="bi bi-list fs-5"></i>
            </button>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0" style="font-size:.82rem;">
                    @yield('breadcrumb')
                </ol>
            </nav>
        </div>
        <div class="d-flex align-items-center gap-3">
            <span class="badge rounded-pill px-3 py-2"
                  style="background:{{ auth()->user()->isInstructor() ? '#7C3AED' : '#4F46E5' }}; font-size:.72rem;">
                <i class="bi bi-{{ auth()->user()->isInstructor() ? 'mortarboard' : 'person-badge' }} me-1"></i>
                {{ ucfirst(auth()->user()->role->name) }}
            </span>
            <div class="d-flex align-items-center gap-2">
                <div class="rounded-circle d-flex align-items-center justify-content-center text-white fw-bold"
                     style="width:34px;height:34px;background:linear-gradient(135deg,#4F46E5,#06B6D4);font-size:.8rem;">
                    {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
                </div>
                <div class="d-none d-md-block">
                    <div style="font-size:.82rem;font-weight:600;">{{ auth()->user()->name }}</div>
                    <div style="font-size:.72rem;color:#64748B;">{{ auth()->user()->email }}</div>
                </div>
            </div>
        </div>
    </header>

    {{-- Flash Messages --}}
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
    </div>

    <main class="ct-content">
        @yield('content')
    </main>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>
@stack('scripts')
</body>
</html>
