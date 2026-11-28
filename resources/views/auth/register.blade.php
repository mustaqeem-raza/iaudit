<x-guest-layout>
    <h2>{{ __('Register') }}</h2>

    <form method="POST" action="{{ route('register') }}" class="stack">
        @csrf

        <label class="field">{{ __('Name') }}
            <input name="name" type="text" value="{{ old('name') }}" required autofocus autocomplete="name">
        </label>

        <label class="field">{{ __('Email') }}
            <input name="email" type="email" value="{{ old('email') }}" required autocomplete="username">
        </label>

        <label class="field">{{ __('Password') }}
            <input name="password" type="password" required autocomplete="new-password">
        </label>

        <label class="field">{{ __('Confirm Password') }}
            <input name="password_confirmation" type="password" required autocomplete="new-password">
        </label>

        <div class="actions">
            <a class="btn link" href="{{ route('login') }}">{{ __('Already registered?') }}</a>
            <button class="btn primary" type="submit">{{ __('Register') }}</button>
        </div>
    </form>
</x-guest-layout>
