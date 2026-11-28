<section class="stack">
    <header>
        <h2>{{ __('Update Password') }}</h2>
        <p class="sub">{{ __('Ensure your account is using a long, random password to stay secure.') }}</p>
    </header>

    <form method="post" action="{{ route('password.update') }}" class="stack">
        @csrf
        @method('put')

        <label class="field">{{ __('Current Password') }}
            <input name="current_password" type="password" autocomplete="current-password">
        </label>

        <label class="field">{{ __('New Password') }}
            <input name="password" type="password" autocomplete="new-password">
        </label>

        <label class="field">{{ __('Confirm Password') }}
            <input name="password_confirmation" type="password" autocomplete="new-password">
        </label>

        <div class="row">
            <button class="btn primary" type="submit">{{ __('Save') }}</button>

            @if (session('status') === 'password-updated')
                <span class="sub" role="status">{{ __('Saved.') }}</span>
            @endif
        </div>
    </form>
</section>
