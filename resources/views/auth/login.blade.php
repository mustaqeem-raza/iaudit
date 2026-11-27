<x-guest-layout>
    <h2>{{ __('Log in') }}</h2>

    @if (session('status'))
        <div class="flash good" role="status">{{ session('status') }}</div>
    @endif

    <form method="POST" action="{{ route('login') }}" class="stack">
        @csrf

        <div class="field">
            <label for="email">{{ __('Email') }}</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username">
            @error('email')
                <p class="err">{{ $message }}</p>
            @enderror
        </div>

        <div class="field">
            <label for="password">{{ __('Password') }}</label>
            <input id="password" type="password" name="password" required autocomplete="current-password">
            @error('password')
                <p class="err">{{ $message }}</p>
            @enderror
        </div>

        <label class="check" for="remember_me">
            <input id="remember_me" type="checkbox" name="remember">
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
