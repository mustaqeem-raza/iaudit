{{-- Placeholder for a section that arrives in a later week. Text comes from config/coming-soon.php. --}}
<x-app-layout>
    <x-slot name="header">
        <div>
            <h1>{{ $title }}</h1>
            <small>{{ $subtitle }}</small>
        </div>
    </x-slot>

    <section class="card stack">
        <div class="spread">
            <div>
                <h2>{{ $title }} is coming soon</h2>
                <p class="sub">{{ $intro }}</p>
            </div>
            <span class="pill lav">Coming in Week {{ $week }}</span>
        </div>

        <div>
            <h3>What this section will do</h3>
            <ul class="list">
                @foreach ($items as $item)
                    <li>{{ $item }}</li>
                @endforeach
            </ul>
        </div>

        <div class="row">
            <a class="btn primary" href="{{ route('dashboard') }}">Back to audits</a>
        </div>
    </section>
</x-app-layout>
