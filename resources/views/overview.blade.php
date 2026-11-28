{{--
    Database management overview, laid out like the mockup's first screen.
    Live numbers come from OverviewController; the capability list and the
    weeks finished come from config/database-management.php.
--}}
<x-app-layout title="Overview" subtitle="Options and approach">
    <section class="card hero">
        <div>
            <p class="sub">Database management layer · Option A</p>
            <h2>Database management for iAudit</h2>
            <p class="lead">
                A managed layer for the audit question bank, built inside the iAudit admin panel.
                Edit questions on screen, import in bulk with a safe preview, roll back mistakes
                and see who changed what.
            </p>
            <p class="lead">Weeks completed: <b>{{ $weeksDone }} of 4</b>.</p>
            <div class="row hero-actions">
                <a class="btn primary" href="{{ route('questions.index') }}">Open questions</a>
                <a class="btn" href="{{ route('questions.import.show') }}">Import &amp; export</a>
            </div>
        </div>

        <div class="try">
            <h3>Live question bank</h3>
            <dl class="stats">
                @foreach ($stats as $label => $value)
                    <div>
                        <dt>{{ $label }}</dt>
                        <dd>{{ $value }}</dd>
                    </div>
                @endforeach
            </dl>
        </div>
    </section>

    <section class="grid2">
        <article class="card opt rec">
            <div class="spread">
                <h2>Option A: Integrated module</h2>
                <span class="pill teal">Chosen</span>
            </div>
            <p class="sub">A CRM-style database management module inside the existing iAudit administration panel.</p>
            <ul>
                @foreach ($capabilities as $capability)
                    <li>
                        <span>{{ $capability['label'] }}</span>
                        @if ($capability['week'] <= $weeksDone)
                            <span class="row">
                                <span class="pill good">Done</span>
                                @if ($capability['route'])
                                    <a class="btn link sm" href="{{ route($capability['route']) }}">Open</a>
                                @endif
                            </span>
                        @else
                            <span class="pill lav">Week {{ $capability['week'] }}</span>
                        @endif
                    </li>
                @endforeach
            </ul>
            <p class="trade"><b>Trade-off:</b> it shares a deployment with iAudit, so it is not independent from the core app.</p>
        </article>

        <article class="card opt">
            <div class="spread">
                <h2>Option B: Standalone application</h2>
                <span class="pill">Later, if needed</span>
            </div>
            <p class="sub">A separate administration portal with its own frontend and authentication, connected to iAudit through secured APIs.</p>
            <ul>
                <li><span>Separates database administration from the core iAudit app</span></li>
                <li><span>Independent deployment and maintenance</span></li>
                <li><span>Supports several data sources, admin users and applications</span></li>
                <li><span>More room for future integrations</span></li>
            </ul>
            <p class="trade"><b>Trade-off:</b> extra development effort, infrastructure, authentication management and ongoing maintenance cost.</p>
        </article>
    </section>

    <section class="card stack">
        <h2>Side by side</h2>
        <div class="tablewrap">
            <table class="data cmp">
                <thead>
                    <tr>
                        <th>Criteria</th>
                        <th>Option A: Integrated</th>
                        <th>Option B: Standalone</th>
                    </tr>
                </thead>
                <tbody>
                    <tr><td>Initial development cost</td><td><span class="pill good">Lower</span></td><td><span class="pill warn">Higher</span></td></tr>
                    <tr><td>Infrastructure</td><td>Uses the existing iAudit setup</td><td>New hosting and deployment</td></tr>
                    <tr><td>Sign-in and permissions</td><td>Reuses iAudit accounts</td><td>Separate authentication to build and maintain</td></tr>
                    <tr><td>Ongoing maintenance</td><td>One system</td><td>Two systems</td></tr>
                    <tr><td>Several apps or data sources</td><td>Possible after extraction</td><td>Built in from day one</td></tr>
                    <tr><td>Switching later</td><td>Modular design allows extraction into Option B without a redesign</td><td>Not applicable</td></tr>
                </tbody>
            </table>
        </div>
        <p class="sub"><b>Decision:</b> Option A, with a modular backend and clear API boundaries.</p>
    </section>

    <section class="card stack">
        <div>
            <h2>How it fits together</h2>
            <p class="sub">The dashed block is the layer that can be lifted out into Option B later.</p>
        </div>
        <div class="arch">
            <div class="cols">
                <div class="tier">Admin panel</div>
                <div class="tier">Mobile app</div>
                <div class="tier">Database management module</div>
            </div>
            <div class="arrow">requests</div>
            <div class="tier dash">
                <b>Service and API layer</b>
                <code>GET /api/questions (v1, frozen for the live app)<br>GET /api/v2/questions<br>POST /api/answers<br>GET /questions (admin)<br>POST /questions/import (admin)</code>
                <span class="sub">Endpoints in use today. Version and audit-trail endpoints arrive in Weeks 3 and 4.</span>
            </div>
            <div class="arrow">reads and writes</div>
            <div class="tier">
                <b>iAudit database</b><br>
                <span class="sub">Questions, answers, audits, templates and criteria</span>
            </div>
        </div>
    </section>
</x-app-layout>
