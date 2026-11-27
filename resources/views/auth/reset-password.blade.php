<x-guest-layout>
    <h2>{{ __('Reset Password') }}</h2>

    <form method="POST" action="{{ route('password.store') }}" class="stack">
        @csrf

        <!-- Password Reset Token -->
        <input type="hidden" name="token" value="{{ $request->route('token') }}">

        <div class="field">
            <label for="email">{{ __('Email') }}</label>
            <input id="email" type="email" name="email" value="{{ old('email', $request->email) }}" required autofocus autocomplete="username">
            @error('email')
                <p class="err">{{ $message }}</p>
            @enderror
        </div>

        <div class="field">
            <label for="password">{{ __('Password') }}</label>
            <input id="password" type="password" name="password" required autocomplete="new-password">
            @error('password')
                <p class="err">{{ $message }}</p>
            @enderror
        </div>

        <div class="field">
            <label for="password_confirmation">{{ __('Confirm Password') }}</label>
            <input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password">
            @error('password_confirmation')
                <p class="err">{{ $message }}</p>
            @enderror
        </div>

        <div class="actions">
            <button class="btn primary" type="submit">{{ __('Reset Password') }}</button>
        </div>
    </form>
</x-guest-layout>
