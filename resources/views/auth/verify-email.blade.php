<x-guest-layout>
    <div class="mb-4 text-muted" style="font-size:.875rem;">
        Thanks for signing up! Before getting started, please verify your email address by clicking the link we just emailed to you.
    </div>

    @if (session('status') == 'verification-link-sent')
        <div class="alert alert-success mb-4" style="font-size:.875rem;">
            A new verification link has been sent to your email address.
        </div>
    @endif

    <div class="d-flex align-items-center justify-content-between">
        <form method="POST" action="{{ route('verification.send') }}">
            @csrf
            <button type="submit" class="btn btn-primary">Resend Verification Email</button>
        </form>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="btn btn-link text-muted" style="font-size:.875rem;">Log Out</button>
        </form>
    </div>
</x-guest-layout>
