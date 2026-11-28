<x-guest-layout>
    <h2>{{ __('Log in') }}</h2>

    <form method="POST" action="{{ route('login') }}" class="stack">
        @csrf

        <label class="field">{{ __('Email') }}
            <input name="email" type="email" value="{{ old('email') }}" required autofocus autocomplete="username">
        </label>

        <label class="field">{{ __('Password') }}
            <input name="password" type="password" required autocomplete="current-password">
        </label>

        <label class="check">
            <input name="remember" type="checkbox">
            {{ __('Remember me') }}
        </label>

        <div class="actions">
            @if (Route::has('password.request'))
                <a class="btn link" href="{{ route('password.request') }}">{{ __('Forgot your password?') }}</a>
            @endif

            <button class="btn primary" type="submit">{{ __('Log in') }}</button>
        </div>
    </form>
</x-guest-layout>
