<x-guest-layout>
    <h2>{{ __('Register') }}</h2>

    <form method="POST" action="{{ route('register') }}" class="stack">
        @csrf

        <div class="field">
            <label for="name">{{ __('Name') }}</label>
            <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus autocomplete="name">
            @error('name')
                <p class="err">{{ $message }}</p>
            @enderror
        </div>

        <div class="field">
            <label for="email">{{ __('Email') }}</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required autocomplete="username">
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
            <a class="btn link" href="{{ route('login') }}">{{ __('Already registered?') }}</a>
            <button class="btn primary" type="submit">{{ __('Register') }}</button>
        </div>
    </form>
</x-guest-layout>
