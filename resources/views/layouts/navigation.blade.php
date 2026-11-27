{{-- Left sidebar. Each link is written out on its own; add a new one by copying a block. --}}
<aside class="side">
    <a class="brand" href="{{ route('dashboard') }}">
        <span class="logo" aria-hidden="true">iA</span>
        <span><b>iAudit</b><small>Admin panel</small></span>
    </a>

    <nav class="nav" aria-label="Sections">
        <a class="nav-btn" href="{{ route('dashboard') }}" @if (request()->routeIs('dashboard', 'audit.index')) aria-current="page" @endif>
            <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M9 2h6a1 1 0 0 1 1 1v2H8V3a1 1 0 0 1 1-1z"/><path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"/><polyline points="9 14 11 16 15 12"/></svg>
            <span>Audits</span>
        </a>

        <a class="nav-btn" href="{{ route('questions.index') }}" @if (request()->routeIs('questions.index', 'questions.create', 'questions.edit')) aria-current="page" @endif>
            <svg viewBox="0 0 24 24" aria-hidden="true"><line x1="8" y1="6" x2="21" y2="6"/><line x1="8" y1="12" x2="21" y2="12"/><line x1="8" y1="18" x2="21" y2="18"/><line x1="3" y1="6" x2="3.01" y2="6"/><line x1="3" y1="12" x2="3.01" y2="12"/><line x1="3" y1="18" x2="3.01" y2="18"/></svg>
            <span>Questions</span>
        </a>

        <a class="nav-btn" href="{{ route('questions.import.show') }}" @if (request()->routeIs('questions.import.*')) aria-current="page" @endif>
            <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
            <span>Import &amp; export</span>
        </a>

        <a class="nav-btn" href="{{ route('versions.index') }}" @if (request()->routeIs('versions.*')) aria-current="page" @endif>
            <svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
            <span>Versions</span>
        </a>

        <a class="nav-btn" href="{{ route('audit-trail.index') }}" @if (request()->routeIs('audit-trail.*')) aria-current="page" @endif>
            <svg viewBox="0 0 24 24" aria-hidden="true"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg>
            <span>Audit trail</span>
        </a>

        <a class="nav-btn" href="{{ route('access.index') }}" @if (request()->routeIs('access.*')) aria-current="page" @endif>
            <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
            <span>Access</span>
        </a>
    </nav>

    {{-- Signed-in user. users has first_name/last_name, not name. --}}
    <div class="live">
        <div class="who">
            <b>{{ trim(Auth::user()->first_name . ' ' . Auth::user()->last_name) }}</b>
            {{ Auth::user()->email }}
        </div>
        <div class="acts">
            <a class="btn link sm" href="{{ route('profile.edit') }}" @if (request()->routeIs('profile.*')) aria-current="page" @endif>Profile</a>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button class="btn link sm" type="submit">Log out</button>
            </form>
        </div>
    </div>
</aside>
