<x-guest-layout>
    <h2>{{ __('Confirm password') }}</h2>

    <p class="sub">
        {{ __('This is a secure area of the application. Please confirm your password before continuing.') }}
    </p>

    <form method="POST" action="{{ route('password.confirm') }}" class="stack">
        @csrf

        <div class="field">
            <label for="password">{{ __('Password') }}</label>
            <input id="password" type="password" name="password" required autocomplete="current-password">
            @error('password')
                <p class="err">{{ $message }}</p>
            @enderror
        </div>

        <div class="actions">
            <button class="btn primary" type="submit">{{ __('Confirm') }}</button>
        </div>
    </form>
</x-guest-layout>
