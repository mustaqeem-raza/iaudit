{{--
    Layout for every error page (401, 403, 404, 419, 429, 500, 503).
    Laravel's own error views all @extends('errors::minimal'); a file with this
    name in resources/views/errors replaces the framework's copy, so this one
    file gives every error page the iAudit look, inside the guest layout.
--}}
<x-guest-layout>
    <p class="error-code">@yield('code')</p>
    <h1 class="error-message">@yield('message')</h1>

    <div class="row">
        <a class="btn primary" href="{{ url('/') }}">Back to iAudit</a>
    </div>
</x-guest-layout>
