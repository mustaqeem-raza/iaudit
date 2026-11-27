<x-guest-layout>
    <h2>{{ __('Verify your email') }}</h2>

    <p class="sub">
        {{ __('Thanks for signing up! Before getting started, could you verify your email address by clicking on the link we just emailed to you? If you didn\'t receive the email, we will gladly send you another.') }}
    </p>

    @if (session('status') == 'verification-link-sent')
        <div class="flash good" role="status">
            {{ __('A new verification link has been sent to the email address you provided during registration.') }}
        </div>
    @endif

    <div class="spread">
        <form method="POST" action="{{ route('verification.send') }}">
            @csrf
            <button class="btn primary" type="submit">{{ __('Resend Verification Email') }}</button>
        </form>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button class="btn link" type="submit">{{ __('Log Out') }}</button>
        </form>
    </div>
</x-guest-layout>
