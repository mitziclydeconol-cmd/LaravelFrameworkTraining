<x-guest-layout>
    @section('title', 'Register')

    <h6 class="fw-semibold mb-1">Create your account</h6>
    <p class="text-muted mb-4" style="font-size:.82rem;">Join CodeTrack AI and start tracking your progress</p>

    <form method="POST" action="{{ route('register') }}">
        @csrf

        <div class="mb-3">
            <label class="form-label fw-medium" style="font-size:.875rem;">Full Name</label>
            <div class="input-group">
                <span class="input-group-text bg-light border-end-0">
                    <i class="bi bi-person text-muted"></i>
                </span>
                <input type="text" name="name" class="form-control border-start-0 ps-0 @error('name') is-invalid @enderror"
                       placeholder="Juan Dela Cruz" value="{{ old('name') }}" required autofocus>
                @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
        </div>

        <div class="mb-3">
            <label class="form-label fw-medium" style="font-size:.875rem;">Email Address</label>
            <div class="input-group">
                <span class="input-group-text bg-light border-end-0">
                    <i class="bi bi-envelope text-muted"></i>
                </span>
                <input type="email" name="email" class="form-control border-start-0 ps-0 @error('email') is-invalid @enderror"
                       placeholder="you@example.com" value="{{ old('email') }}" required>
                @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
        </div>

        <div class="mb-3">
            <label class="form-label fw-medium" style="font-size:.875rem;">Role</label>
            <div class="d-flex gap-3">
                <label class="flex-fill">
                    <input type="radio" name="role" value="student" class="d-none" id="roleStudent"
                           {{ old('role', 'student') === 'student' ? 'checked' : '' }}>
                    <div class="border rounded-3 p-3 text-center role-option {{ old('role', 'student') === 'student' ? 'selected' : '' }}"
                         style="cursor:pointer;transition:all .15s;" onclick="selectRole('student')">
                        <i class="bi bi-person-badge fs-4 d-block mb-1"></i>
                        <span style="font-size:.82rem;font-weight:500;">Student</span>
                    </div>
                </label>
                <label class="flex-fill">
                    <input type="radio" name="role" value="instructor" class="d-none" id="roleInstructor"
                           {{ old('role') === 'instructor' ? 'checked' : '' }}>
                    <div class="border rounded-3 p-3 text-center role-option {{ old('role') === 'instructor' ? 'selected' : '' }}"
                         style="cursor:pointer;transition:all .15s;" onclick="selectRole('instructor')">
                        <i class="bi bi-mortarboard fs-4 d-block mb-1"></i>
                        <span style="font-size:.82rem;font-weight:500;">Instructor</span>
                    </div>
                </label>
            </div>
            @error('role')<div class="text-danger mt-1" style="font-size:.82rem;">{{ $message }}</div>@enderror
        </div>

        <div class="mb-3" id="studentIdField" style="{{ old('role', 'student') === 'student' ? '' : 'display:none;' }}">
            <label class="form-label fw-medium" style="font-size:.875rem;">Student ID <span class="text-muted fw-normal">(optional)</span></label>
            <input type="text" name="student_id" class="form-control @error('student_id') is-invalid @enderror"
                   placeholder="IT-2024-001" value="{{ old('student_id') }}">
            @error('student_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        <div class="mb-3">
            <label class="form-label fw-medium" style="font-size:.875rem;">Password</label>
            <input type="password" name="password" class="form-control @error('password') is-invalid @enderror"
                   placeholder="Min. 8 characters" required>
            @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        <div class="mb-4">
            <label class="form-label fw-medium" style="font-size:.875rem;">Confirm Password</label>
            <input type="password" name="password_confirmation" class="form-control" placeholder="Repeat password" required>
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
        .role-option.selected {
            border-color: #4F46E5 !important;
            background: #EEF2FF;
            color: #4F46E5;
        }
    </style>
    <script>
        function selectRole(role) {
            document.querySelectorAll('.role-option').forEach(el => el.classList.remove('selected'));
            document.getElementById('role' + role.charAt(0).toUpperCase() + role.slice(1)).checked = true;
            event.currentTarget.querySelector('.role-option').classList.add('selected');
            document.getElementById('studentIdField').style.display = role === 'student' ? '' : 'none';
        }
    </script>
</x-guest-layout>
