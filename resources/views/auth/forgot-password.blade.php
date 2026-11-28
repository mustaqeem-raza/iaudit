<x-guest-layout>
    <h2>{{ __('Forgot your password?') }}</h2>

    <p class="sub">
        {{ __('Forgot your password? No problem. Just let us know your email address and we will email you a password reset link that will allow you to choose a new one.') }}
    </p>

    <form method="POST" action="{{ route('password.email') }}" class="stack">
        @csrf

        <label class="field">{{ __('Email') }}
            <input name="email" type="email" value="{{ old('email') }}" required autofocus>
        </label>

        <div class="actions">
            <a class="btn link" href="{{ route('login') }}">{{ __('Log in') }}</a>
            <button class="btn primary" type="submit">{{ __('Email Password Reset Link') }}</button>
        </div>
    </form>
</x-guest-layout>
