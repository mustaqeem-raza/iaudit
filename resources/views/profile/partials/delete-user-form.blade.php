<section class="stack">
    <header>
        <h2>{{ __('Delete Account') }}</h2>
        <p class="sub">
            {{ __('Once your account is deleted, all of its resources and data will be permanently deleted. Please enter your password to confirm you would like to permanently delete your account.') }}
        </p>
    </header>

    {{-- data-confirm: the layout asks "Are you sure?" before the form is sent. --}}
    <form method="post" action="{{ route('profile.destroy') }}" class="stack"
          data-confirm="{{ __('Are you sure you want to delete your account?') }}">
        @csrf
        @method('delete')

        <label class="field">{{ __('Password') }}
            <input name="password" type="password" autocomplete="current-password">
        </label>

        <div class="row">
            <button class="btn danger" type="submit">{{ __('Delete Account') }}</button>
        </div>
    </form>
</section>
