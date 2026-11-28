{{-- Shared <head> contents for every page. Optional: $title, shown first in the browser tab. --}}
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">

<title>{{ ($title ?? '') !== '' ? $title . ' · ' : '' }}{{ config('app.name', 'iAudit') }}</title>

<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Space+Grotesk:wght@500;600;700&display=swap">

{{-- ?v= changes whenever the file is edited, so browsers never keep an old copy. --}}
<link rel="stylesheet" href="{{ asset('css/admin.css') }}?v={{ filemtime(public_path('css/admin.css')) }}">
