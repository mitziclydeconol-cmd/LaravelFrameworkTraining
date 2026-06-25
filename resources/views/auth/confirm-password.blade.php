<x-guest-layout>
    <h6 class="fw-semibold mb-1">Confirm password</h6>
    <p class="text-muted mb-4" style="font-size:.82rem;">Please confirm your password before continuing.</p>

    <form method="POST" action="{{ route('password.confirm') }}">
        @csrf
        <div class="mb-4">
            <label class="form-label fw-medium" style="font-size:.875rem;">Password</label>
            <input type="password" name="password" class="form-control @error('password') is-invalid @enderror"
                   required autocomplete="current-password">
            @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <button type="submit" class="btn btn-primary w-100">Confirm</button>
    </form>
</x-guest-layout>
