<section class="stack">
    <header>
        <h2>{{ __('Delete Account') }}</h2>
        <p class="sub">
            {{ __('Once your account is deleted, all of its resources and data will be permanently deleted. Please enter your password to confirm you would like to permanently delete your account.') }}
        </p>
    </header>

    {{-- The browser's own confirm box asks once more before the form is sent. --}}
    <form method="post" action="{{ route('profile.destroy') }}" class="stack"
          onsubmit="return confirm('{{ __('Are you sure you want to delete your account?') }}');">
        @csrf
        @method('delete')

        <div class="field">
            <label for="password">{{ __('Password') }}</label>
            <input id="password" name="password" type="password" placeholder="{{ __('Password') }}" autocomplete="current-password">
            @error('password', 'userDeletion')
                <p class="err">{{ $message }}</p>
            @enderror
        </div>

        <div class="row">
            <button class="btn danger" type="submit">{{ __('Delete Account') }}</button>
        </div>
    </form>
</section>
