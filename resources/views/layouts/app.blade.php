<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        @include('layouts.head', ['title' => $title])
    </head>
    <body>
        <div class="shell">
            @include('layouts.navigation')

            <div class="content">
                <header class="top">
                    <div>
                        <h1>{{ $title }}</h1>
                        <small>{{ $subtitle }}</small>
                    </div>
                </header>

                @include('layouts.messages')

                <main id="main" class="stack">
                    {{ $slot }}
                </main>
            </div>
        </div>

        <script>
            // A form with data-confirm="…" asks before it is sent (used by every delete button).
            document.addEventListener('submit', function (event) {
                var message = event.target.dataset.confirm;
                if (message && !confirm(message)) {
                    event.preventDefault();
                }
            });
        </script>
    </body>
</html>
