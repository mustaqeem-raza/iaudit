<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        @include('layouts.head')
    </head>
    <body>
        <div class="guest">
            @include('layouts.brand')

            <main class="card stack">
                {{-- Breeze sends password-reset confirmations as a 'status' sentence.
                     'verification-link-sent' is a key, shown by verify-email itself. --}}
                @if (session('status') && session('status') !== 'verification-link-sent')
                    <div class="flash good" role="status">{{ session('status') }}</div>
                @endif

                @include('layouts.messages')

                {{ $slot }}
            </main>
        </div>
    </body>
</html>
