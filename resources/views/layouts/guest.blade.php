<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'iAudit') }}</title>

        <!-- Fonts and styles: one plain CSS file, no build step -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Space+Grotesk:wght@500;600;700&display=swap">
        <link rel="stylesheet" href="{{ asset('css/admin.css') }}?v={{ filemtime(public_path('css/admin.css')) }}">
    </head>
    <body>
        <div class="guest">
            <a class="brand" href="/">
                <span class="logo" aria-hidden="true">iA</span>
                <span><b>iAudit</b><small>Admin panel</small></span>
            </a>

            <main class="card stack">
                {{ $slot }}
            </main>
        </div>
    </body>
</html>
