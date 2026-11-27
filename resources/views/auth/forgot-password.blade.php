<x-guest-layout>
    <h2>{{ __('Forgot your password?') }}</h2>

    <p class="sub">
        {{ __('Forgot your password? No problem. Just let us know your email address and we will email you a password reset link that will allow you to choose a new one.') }}
    </p>

    @if (session('status'))
        <div class="flash good" role="status">{{ session('status') }}</div>
    @endif

    <form method="POST" action="{{ route('password.email') }}" class="stack">
        @csrf

        <div class="field">
            <label for="email">{{ __('Email') }}</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus>
            @error('email')
                <p class="err">{{ $message }}</p>
            @enderror
        </div>

        <div class="actions">
            <a class="btn link" href="{{ route('login') }}">{{ __('Log in') }}</a>
            <button class="btn primary" type="submit">{{ __('Email Password Reset Link') }}</button>
        </div>
    </form>
</x-guest-layout>
