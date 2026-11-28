<x-app-layout :title="__('Audits')" subtitle="Submitted ship inspections, newest first">
    <section class="card">
        <div class="spread">
            <div>
                <h2>Audit listing</h2>
                <p class="sub">{{ $audits->total() }} {{ Str::plural('audit', $audits->total()) }}</p>
            </div>
        </div>

        {{-- Filters are sent as a normal GET request; AuditReportController::index reads them. --}}
        <form method="GET" action="{{ url()->current() }}" class="row filters">
            <input type="search" name="search" value="{{ request('search') }}" class="grow"
                   placeholder="Search reference, user or ship" aria-label="Search audits">

            <select name="status" aria-label="Filter by status">
                <option value="">All statuses</option>
                @foreach (\App\Models\Audit::STATUSES as $value => $label)
                    <option value="{{ $value }}" @selected(request('status') === $value)>{{ $label }}</option>
                @endforeach
            </select>

            <button class="btn" type="submit">Filter</button>

            @if (request()->filled('search') || request()->filled('status'))
                <a class="btn link" href="{{ url()->current() }}">Clear</a>
            @endif
        </form>

        <div class="tablewrap">
            <table class="data">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Reference</th>
                        <th>User</th>
                        <th>Ship</th>
                        <th>Status</th>
                        <th>Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($audits as $audit)
                        <tr>
                            <td class="num">{{ $audit->id }}</td>
                            <td class="mono">{{ $audit->reference_number }}</td>
                            <td>
                                @if ($audit->user)
                                    <b>{{ $audit->user->first_name }} {{ $audit->user->last_name }}</b><br>
                                    <span class="sub">{{ $audit->user->email }}</span>
                                @else
                                    <span class="sub">N/A</span>
                                @endif
                            </td>
                            <td>
                                @if ($audit->ship)
                                    {{ $audit->ship->name }}
                                    <span class="sub">({{ $audit->ship->mnemonic }})</span>
                                @else
                                    <span class="sub">N/A</span>
                                @endif
                            </td>
                            <td>
                                <span @class(['pill', 'good' => $audit->status === 'completed', 'warn' => $audit->status === 'pending'])>
                                    {{ ucfirst($audit->status) }}
                                </span>
                            </td>
                            <td class="num">{{ $audit->created_at->format('Y-m-d H:i') }}</td>
                            <td>
                                <div class="acts">
                                    <a class="btn sm" href="{{ route('audit.report.show', $audit->id) }}">View</a>
                                    <a class="btn sm" href="{{ route('audit.report.pdf.show', $audit->id) }}">PDF</a>
                                    <a class="btn sm" href="{{ route('audit.download', $audit->id) }}">Download</a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="empty">No audits match these filters.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{ $audits->links() }}
    </section>
</x-app-layout>
