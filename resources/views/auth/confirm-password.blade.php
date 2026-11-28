<x-guest-layout>
    <h2>{{ __('Confirm password') }}</h2>

    <p class="sub">
        {{ __('This is a secure area of the application. Please confirm your password before continuing.') }}
    </p>

    <form method="POST" action="{{ route('password.confirm') }}" class="stack">
        @csrf

        <label class="field">{{ __('Password') }}
            <input name="password" type="password" required autocomplete="current-password">
        </label>

        <div class="actions">
            <button class="btn primary" type="submit">{{ __('Confirm') }}</button>
        </div>
    </form>
</x-guest-layout>
