<x-app-layout :title="__('Profile')" subtitle="Your account details and password">
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
