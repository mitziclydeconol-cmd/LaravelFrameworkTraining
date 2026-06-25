<x-guest-layout>
    @section('title', 'Register')

    <h6 class="fw-semibold mb-1">Create your account</h6>
    <p class="text-muted mb-4" style="font-size:.82rem;">Join CodeTrack AI and start tracking your progress</p>

    @if($errors->any())
        <div class="alert alert-danger mb-3 p-2" style="font-size:.8rem;">
            <ul class="mb-0 ps-3">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('register') }}">
        @csrf

        {{-- Full Name --}}
        <div class="mb-3">
            <label class="form-label fw-medium" style="font-size:.875rem;">Full Name <span class="text-danger">*</span></label>
            <div class="input-group">
                <span class="input-group-text bg-light border-end-0"><i class="bi bi-person text-muted"></i></span>
                <input type="text" name="name"
                       class="form-control border-start-0 ps-0 @error('name') is-invalid @enderror"
                       placeholder="Juan Dela Cruz"
                       value="{{ old('name') }}" required autofocus>
                @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
        </div>

        {{-- Email --}}
        <div class="mb-3">
            <label class="form-label fw-medium" style="font-size:.875rem;">Email Address <span class="text-danger">*</span></label>
            <div class="input-group">
                <span class="input-group-text bg-light border-end-0"><i class="bi bi-envelope text-muted"></i></span>
                <input type="email" name="email"
                       class="form-control border-start-0 ps-0 @error('email') is-invalid @enderror"
                       placeholder="you@example.com"
                       value="{{ old('email') }}" required>
                @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
        </div>

        {{-- Role Selector --}}
        <div class="mb-3">
            <label class="form-label fw-medium" style="font-size:.875rem;">I am a… <span class="text-danger">*</span></label>
            <div class="row g-2">
                <div class="col-6">
                    <input type="radio" name="role" value="student" id="roleStudent" class="d-none"
                           {{ old('role', 'student') === 'student' ? 'checked' : '' }}>
                    <label for="roleStudent" class="role-card d-block text-center p-3 rounded-3 border {{ old('role', 'student') === 'student' ? 'role-active' : '' }}" style="cursor:pointer;">
                        <i class="bi bi-person-badge d-block mb-1" style="font-size:1.6rem;"></i>
                        <span style="font-size:.82rem;font-weight:600;">Student</span>
                        <div style="font-size:.7rem;color:#64748B;margin-top:2px;">Track my coding</div>
                    </label>
                </div>
                <div class="col-6">
                    <input type="radio" name="role" value="instructor" id="roleInstructor" class="d-none"
                           {{ old('role') === 'instructor' ? 'checked' : '' }}>
                    <label for="roleInstructor" class="role-card d-block text-center p-3 rounded-3 border {{ old('role') === 'instructor' ? 'role-active' : '' }}" style="cursor:pointer;">
                        <i class="bi bi-mortarboard d-block mb-1" style="font-size:1.6rem;"></i>
                        <span style="font-size:.82rem;font-weight:600;">Instructor</span>
                        <div style="font-size:.7rem;color:#64748B;margin-top:2px;">Monitor students</div>
                    </label>
                </div>
            </div>
            @error('role')<div class="text-danger mt-1" style="font-size:.8rem;">{{ $message }}</div>@enderror
        </div>

        {{-- Student ID (students only) --}}
        <div class="mb-3" id="studentIdField" style="{{ old('role', 'student') === 'student' ? '' : 'display:none;' }}">
            <label class="form-label fw-medium" style="font-size:.875rem;">
                Student ID <span class="text-muted fw-normal">(optional)</span>
            </label>
            <div class="input-group">
                <span class="input-group-text bg-light border-end-0"><i class="bi bi-card-text text-muted"></i></span>
                <input type="text" name="student_id"
                       class="form-control border-start-0 ps-0 @error('student_id') is-invalid @enderror"
                       placeholder="IT-2024-001"
                       value="{{ old('student_id') }}">
                @error('student_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
        </div>

        {{-- Password --}}
        <div class="mb-3">
            <label class="form-label fw-medium" style="font-size:.875rem;">Password <span class="text-danger">*</span></label>
            <div class="input-group">
                <span class="input-group-text bg-light border-end-0"><i class="bi bi-lock text-muted"></i></span>
                <input type="password" name="password" id="pw1"
                       class="form-control border-start-0 border-end-0 ps-0 @error('password') is-invalid @enderror"
                       placeholder="Min. 8 characters" required>
                <button type="button" class="input-group-text bg-light border-start-0"
                        onclick="togglePw('pw1','eye1')" style="cursor:pointer;">
                    <i class="bi bi-eye text-muted" id="eye1"></i>
                </button>
                @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
        </div>

        {{-- Confirm Password --}}
        <div class="mb-4">
            <label class="form-label fw-medium" style="font-size:.875rem;">Confirm Password <span class="text-danger">*</span></label>
            <div class="input-group">
                <span class="input-group-text bg-light border-end-0"><i class="bi bi-lock-fill text-muted"></i></span>
                <input type="password" name="password_confirmation" id="pw2"
                       class="form-control border-start-0 border-end-0 ps-0"
                       placeholder="Repeat your password" required>
                <button type="button" class="input-group-text bg-light border-start-0"
                        onclick="togglePw('pw2','eye2')" style="cursor:pointer;">
                    <i class="bi bi-eye text-muted" id="eye2"></i>
                </button>
            </div>
        </div>

        <button type="submit" class="btn btn-primary w-100 fw-medium mb-3">
            <i class="bi bi-person-plus me-2"></i>Create Account
        </button>

        <div class="text-center" style="font-size:.82rem;">
            Already have an account?
            <a href="{{ route('login') }}" class="text-decoration-none fw-medium" style="color:#4F46E5;">Sign in</a>
        </div>
    </form>

    <style>
        .role-card { transition: all .15s ease; }
        .role-card:hover { border-color: #4F46E5 !important; background: #F5F4FF; }
        .role-active { border-color: #4F46E5 !important; background: #EEF2FF; color: #4F46E5; }
        .role-active i, .role-active span { color: #4F46E5; }
    </style>

    <script>
        // Role card toggle
        document.querySelectorAll('input[name="role"]').forEach(function(radio) {
            radio.addEventListener('change', function() {
                document.querySelectorAll('.role-card').forEach(function(card) {
                    card.classList.remove('role-active');
                });
                this.nextElementSibling.classList.add('role-active');
                document.getElementById('studentIdField').style.display =
                    this.value === 'student' ? '' : 'none';
            });
        });

        // Password toggle
        function togglePw(fieldId, iconId) {
            var f = document.getElementById(fieldId);
            var i = document.getElementById(iconId);
            if (f.type === 'password') {
                f.type = 'text';
                i.className = 'bi bi-eye-slash text-muted';
            } else {
                f.type = 'password';
                i.className = 'bi bi-eye text-muted';
            }
        }
    </script>
</x-guest-layout>