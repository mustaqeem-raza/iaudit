<x-guest-layout>
    <h2>{{ __('Reset Password') }}</h2>

    <form method="POST" action="{{ route('password.store') }}" class="stack">
        @csrf

        <input type="hidden" name="token" value="{{ $request->route('token') }}">

        <label class="field">{{ __('Email') }}
            <input name="email" type="email" value="{{ old('email', $request->email) }}" required autofocus autocomplete="username">
        </label>

        <label class="field">{{ __('Password') }}
            <input name="password" type="password" required autocomplete="new-password">
        </label>

        <label class="field">{{ __('Confirm Password') }}
            <input name="password_confirmation" type="password" required autocomplete="new-password">
        </label>

        <div class="actions">
            <button class="btn primary" type="submit">{{ __('Reset Password') }}</button>
        </div>
    </form>
</x-guest-layout>
