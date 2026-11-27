<x-app-layout>
    <x-slot name="header">
        <div>
            <h1>{{ __('Profile') }}</h1>
            <small>Your account details and password</small>
        </div>
    </x-slot>

    <section class="card">
        <div class="measure">
            @include('profile.partials.update-profile-information-form')
        </div>
    </section>

    <section class="card">
        <div class="measure">
            @include('profile.partials.update-password-form')
        </div>
    </section>

    <section class="card">
        <div class="measure">
            @include('profile.partials.delete-user-form')
        </div>
    </section>
</x-app-layout>
