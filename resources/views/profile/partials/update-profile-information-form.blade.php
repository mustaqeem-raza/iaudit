<section class="stack">
    <header>
        <h2>{{ __('Profile Information') }}</h2>
        <p class="sub">{{ __("Update your account's profile information and email address.") }}</p>
    </header>

    {{-- Submitted by the re-send button below (form="send-verification"); hidden so it takes no space. --}}
    <form id="send-verification" method="post" action="{{ route('verification.send') }}" hidden>
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" class="stack">
        @csrf
        @method('patch')

        <div class="field">
            <label for="name">{{ __('Name') }}</label>
            <input id="name" name="name" type="text" value="{{ old('name', $user->name) }}" required autofocus autocomplete="name">
            @error('name')
                <p class="err">{{ $message }}</p>
            @enderror
        </div>

        <div class="field">
            <label for="email">{{ __('Email') }}</label>
            <input id="email" name="email" type="email" value="{{ old('email', $user->email) }}" required autocomplete="username">
            @error('email')
                <p class="err">{{ $message }}</p>
            @enderror

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <p class="sub">
                    {{ __('Your email address is unverified.') }}
                    <button form="send-verification" class="btn link sm" type="submit">
                        {{ __('Click here to re-send the verification email.') }}
                    </button>
                </p>

                @if (session('status') === 'verification-link-sent')
                    <p class="flash good">{{ __('A new verification link has been sent to your email address.') }}</p>
                @endif
            @endif
        </div>

        <div class="row">
            <button class="btn primary" type="submit">{{ __('Save') }}</button>

            @if (session('status') === 'profile-updated')
                <span class="sub" role="status">{{ __('Saved.') }}</span>
            @endif
        </div>
    </form>
</section>
