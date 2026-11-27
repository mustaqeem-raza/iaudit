<section class="stack">
    <header>
        <h2>{{ __('Update Password') }}</h2>
        <p class="sub">{{ __('Ensure your account is using a long, random password to stay secure.') }}</p>
    </header>

    <form method="post" action="{{ route('password.update') }}" class="stack">
        @csrf
        @method('put')

        <div class="field">
            <label for="update_password_current_password">{{ __('Current Password') }}</label>
            <input id="update_password_current_password" name="current_password" type="password" autocomplete="current-password">
            @error('current_password', 'updatePassword')
                <p class="err">{{ $message }}</p>
            @enderror
        </div>

        <div class="field">
            <label for="update_password_password">{{ __('New Password') }}</label>
            <input id="update_password_password" name="password" type="password" autocomplete="new-password">
            @error('password', 'updatePassword')
                <p class="err">{{ $message }}</p>
            @enderror
        </div>

        <div class="field">
            <label for="update_password_password_confirmation">{{ __('Confirm Password') }}</label>
            <input id="update_password_password_confirmation" name="password_confirmation" type="password" autocomplete="new-password">
            @error('password_confirmation', 'updatePassword')
                <p class="err">{{ $message }}</p>
            @enderror
        </div>

        <div class="row">
            <button class="btn primary" type="submit">{{ __('Save') }}</button>

            @if (session('status') === 'password-updated')
                <span class="sub" role="status">{{ __('Saved.') }}</span>
            @endif
        </div>
    </form>
</section>
