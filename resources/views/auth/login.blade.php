<x-guest-layout>
    @section('title', 'Sign In')

    <h6 class="fw-semibold mb-1">Welcome back</h6>
    <p class="text-muted mb-4" style="font-size:.82rem;">Sign in to your CodeTrack account</p>

    {{-- Session Status --}}
    @if (session('status'))
        <div class="alert alert-success mb-3" style="font-size:.82rem;">{{ session('status') }}</div>
    @endif

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <div class="mb-3">
            <label class="form-label fw-medium" style="font-size:.875rem;">Email Address</label>
            <div class="input-group">
                <span class="input-group-text bg-light border-end-0">
                    <i class="bi bi-envelope text-muted"></i>
                </span>
                <input type="email" name="email" class="form-control border-start-0 ps-0 @error('email') is-invalid @enderror"
                       placeholder="you@example.com" value="{{ old('email') }}" required autofocus>
                @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
        </div>

        <div class="mb-3">
            <div class="d-flex justify-content-between align-items-center mb-1">
                <label class="form-label fw-medium mb-0" style="font-size:.875rem;">Password</label>
                @if (Route::has('password.request'))
                    <a href="{{ route('password.request') }}" class="text-decoration-none" style="font-size:.78rem;color:#4F46E5;">Forgot password?</a>
                @endif
            </div>
            <div class="input-group">
                <span class="input-group-text bg-light border-end-0">
                    <i class="bi bi-lock text-muted"></i>
                </span>
                <input type="password" name="password" id="passwordField"
                       class="form-control border-start-0 border-end-0 ps-0 @error('password') is-invalid @enderror"
                       placeholder="••••••••" required>
                <button type="button" class="input-group-text bg-light border-start-0"
                        onclick="togglePass()" style="cursor:pointer;">
                    <i class="bi bi-eye text-muted" id="eyeIcon"></i>
                </button>
                @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
        </div>

        <div class="mb-3 d-flex align-items-center">
            <input type="checkbox" name="remember" id="remember" class="form-check-input me-2">
            <label for="remember" class="form-check-label" style="font-size:.82rem;">Remember me</label>
        </div>

        <button type="submit" class="btn btn-primary w-100 fw-medium mb-3">
            <i class="bi bi-box-arrow-in-right me-2"></i>Sign In
        </button>

        <div class="auth-divider"><span>or</span></div>

        <div class="text-center mt-3" style="font-size:.82rem;">
            Don't have an account?
            <a href="{{ route('register') }}" class="text-decoration-none fw-medium" style="color:#4F46E5;">Create one</a>
        </div>
    </form>

    {{-- Demo Credentials --}}
    <div class="mt-4 p-3 rounded-3" style="background:#F8FAFC;border:1px dashed #CBD5E1;">
        <p class="fw-semibold mb-2" style="font-size:.75rem;color:#64748B;">DEMO ACCOUNTS</p>
        <div class="row g-1" style="font-size:.72rem;color:#475569;">
            <div class="col-6">
                <div class="fw-medium">👨‍🏫 Instructor</div>
                <div>instructor@codetrack.dev</div>
            </div>
            <div class="col-6">
                <div class="fw-medium">👨‍🎓 Student</div>
                <div>juan@student.dev</div>
            </div>
            <div class="col-12 mt-1 text-muted">Password: <code>password</code></div>
        </div>
    </div>

    <script>
        function togglePass() {
            const f = document.getElementById('passwordField');
            const i = document.getElementById('eyeIcon');
            if (f.type === 'password') { f.type = 'text'; i.className = 'bi bi-eye-slash text-muted'; }
            else { f.type = 'password'; i.className = 'bi bi-eye text-muted'; }
        }
    </script>
</x-guest-layout>
