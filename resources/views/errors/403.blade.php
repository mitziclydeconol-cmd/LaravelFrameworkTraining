<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Access Denied – CodeTrack AI</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; background: #F1F5F9; }
        .error-code { font-size: 6rem; font-weight: 800; color: #4F46E5; line-height: 1; }
    </style>
</head>
<body class="d-flex align-items-center justify-content-center min-vh-100">
    <div class="text-center px-4">
        <div class="error-code">403</div>
        <h4 class="mt-3 fw-bold">Access Denied</h4>
        <p class="text-muted">You don't have permission to access this page.</p>
        <a href="{{ url('/dashboard') }}" class="btn btn-primary mt-2">
            <i class="bi bi-house me-2"></i>Back to Dashboard
        </a>
    </div>
</body>
</html>
