<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'CodeTrack AI') }} – @yield('title', 'Auth')</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #1E1B4B 0%, #312E81 50%, #1E1B4B 100%);
            min-height: 100vh;
            display: flex; align-items: center; justify-content: center;
        }
        .auth-card {
            background: #fff;
            border-radius: 16px;
            padding: 2.5rem;
            box-shadow: 0 25px 60px rgba(0,0,0,0.3);
            width: 100%; max-width: 440px;
        }
        .auth-brand { text-align: center; margin-bottom: 2rem; }
        .auth-brand .brand-icon {
            width: 56px; height: 56px;
            background: linear-gradient(135deg, #4F46E5, #06B6D4);
            border-radius: 14px;
            display: inline-flex; align-items: center; justify-content: center;
            font-size: 1.6rem; color: #fff; margin-bottom: .75rem;
        }
        .auth-brand h5 { font-weight: 700; margin-bottom: .2rem; }
        .form-control:focus, .form-select:focus {
            border-color: #4F46E5;
            box-shadow: 0 0 0 3px rgba(79,70,229,.12);
        }
        .btn-primary { background: #4F46E5; border-color: #4F46E5; }
        .btn-primary:hover { background: #4338CA; border-color: #4338CA; }
        .auth-divider { text-align: center; position: relative; margin: 1.25rem 0; }
        .auth-divider::before {
            content: ''; position: absolute; top: 50%; left: 0; right: 0;
            height: 1px; background: #E2E8F0;
        }
        .auth-divider span {
            background: #fff; padding: 0 .75rem;
            position: relative; color: #94A3B8; font-size: .78rem;
        }
        /* Decorative dots */
        .auth-bg-deco {
            position: fixed; top: 0; left: 0; right: 0; bottom: 0;
            pointer-events: none; overflow: hidden; z-index: 0;
        }
        .auth-bg-deco span {
            position: absolute; border-radius: 50%;
            background: rgba(255,255,255,.04);
        }
        .auth-card { position: relative; z-index: 1; }
    </style>
</head>
<body>
    <div class="auth-bg-deco">
        <span style="width:400px;height:400px;top:-100px;left:-100px;"></span>
        <span style="width:300px;height:300px;bottom:-80px;right:-80px;"></span>
        <span style="width:200px;height:200px;top:40%;left:5%;"></span>
    </div>

    <div class="auth-card">
        <div class="auth-brand">
            <div class="brand-icon"><i class="bi bi-code-slash"></i></div>
            <h5>CodeTrack AI</h5>
            <p class="text-muted mb-0" style="font-size:.82rem;">AI-Assisted Coding Progress Tracker</p>
        </div>

        {{ $slot }}
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
