<x-guest-layout>
    @section('title', 'Reset Password')

    <h6 class="fw-semibold mb-1">Forgot your password?</html>
    <p class="text-muted mb-4" style="font-size:.82rem;">No worries. Enter your email and we'll send a reset link.</p>

    @if (session('status'))
        <div class="alert alert-success mb-3" style="font-size:.82rem;">{{ session('status') }}</div>
    @endif

    <form method="POST" action="{{ route('password.email') }}">
        @csrf
        <div class="mb-3">
            <label class="form-label fw-medium" style="font-size:.875rem;">Email Address</label>
            <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                   placeholder="you@example.com" value="{{ old('email') }}" required autofocus>
            @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        <button type="submit" class="btn btn-primary w-100 fw-medium mb-3">
            <i class="bi bi-envelope me-2"></i>Send Reset Link
        </button>

        <div class="text-center" style="font-size:.82rem;">
            <a href="{{ route('login') }}" class="text-decoration-none" style="color:#4F46E5;">
                <i class="bi bi-arrow-left me-1"></i>Back to login
            </a>
        </div>
    </form>
</x-guest-layout>
