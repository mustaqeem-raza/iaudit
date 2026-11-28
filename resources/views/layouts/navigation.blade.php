@php
    // Sidebar sections. To add one: add a line here and an icon <symbol> below.
    // 'active' lists the route names that highlight the link. A 'heading' line
    // prints a small group title instead of a link.
    $sections = [
        ['label' => 'Audits',          'route' => 'dashboard',             'active' => ['dashboard', 'audit.index'],                               'icon' => 'audits'],
        ['heading' => 'Database management'],
        ['label' => 'Overview',        'route' => 'overview.index',        'active' => ['overview.*'],                                             'icon' => 'overview'],
        ['label' => 'Questions',       'route' => 'questions.index',       'active' => ['questions.index', 'questions.create', 'questions.edit'], 'icon' => 'questions'],
        ['label' => 'Import & export', 'route' => 'questions.import.show', 'active' => ['questions.import.*'],                                     'icon' => 'import'],
        ['label' => 'Versions',        'route' => 'versions.index',        'active' => ['versions.*'],                                             'icon' => 'versions'],
        ['label' => 'Audit trail',     'route' => 'audit-trail.index',     'active' => ['audit-trail.*'],                                          'icon' => 'audit-trail'],
        ['label' => 'Access',          'route' => 'access.index',          'active' => ['access.*'],                                               'icon' => 'access'],
    ];
@endphp

{{-- Sidebar icons, drawn once and used by name: <use href="#icon-audits"/>. --}}
<svg hidden>
    <symbol id="icon-audits" viewBox="0 0 24 24"><path d="M9 2h6a1 1 0 0 1 1 1v2H8V3a1 1 0 0 1 1-1z"/><path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"/><polyline points="9 14 11 16 15 12"/></symbol>
    <symbol id="icon-overview" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><polygon points="16.24 7.76 14.12 14.12 7.76 16.24 9.88 9.88 16.24 7.76"/></symbol>
    <symbol id="icon-questions" viewBox="0 0 24 24"><line x1="8" y1="6" x2="21" y2="6"/><line x1="8" y1="12" x2="21" y2="12"/><line x1="8" y1="18" x2="21" y2="18"/><line x1="3" y1="6" x2="3.01" y2="6"/><line x1="3" y1="12" x2="3.01" y2="12"/><line x1="3" y1="18" x2="3.01" y2="18"/></symbol>
    <symbol id="icon-import" viewBox="0 0 24 24"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></symbol>
    <symbol id="icon-versions" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></symbol>
    <symbol id="icon-audit-trail" viewBox="0 0 24 24"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></symbol>
    <symbol id="icon-access" viewBox="0 0 24 24"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></symbol>
</svg>

<aside class="side">
    @include('layouts.brand', ['href' => route('dashboard')])

    <nav class="nav" aria-label="Sections">
        @foreach ($sections as $section)
            @isset($section['heading'])
                <p class="nav-group">{{ $section['heading'] }}</p>
            @else
                <a class="nav-btn" href="{{ route($section['route']) }}" @if (request()->routeIs($section['active'])) aria-current="page" @endif>
                    <svg aria-hidden="true"><use href="#icon-{{ $section['icon'] }}"/></svg>
                    <span>{{ $section['label'] }}</span>
                </a>
            @endisset
        @endforeach
    </nav>

    {{-- Signed-in user. users has first_name/last_name, not name. --}}
    <div class="account">
        <div class="account-name">
            <b>{{ trim(Auth::user()->first_name . ' ' . Auth::user()->last_name) }}</b>
            {{ Auth::user()->email }}
        </div>
        <div class="account-links">
            <a class="btn link sm" href="{{ route('profile.edit') }}">Profile</a>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button class="btn link sm" type="submit">Log out</button>
            </form>
        </div>
    </div>
</aside>
