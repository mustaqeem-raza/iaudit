<x-app-layout>
    <x-slot name="header">
        <div>
            <h1>Import &amp; export</h1>
            <small>Upload the question-bank workbook</small>
        </div>
    </x-slot>

    <section class="card stack">
        <div>
            <h2>{{ __('Import Questions') }}</h2>
            <p class="sub">
                Upload the question-bank workbook (<code>.xlsx</code>) to import its
                <code>Data</code> sheet.
            </p>
        </div>

        @if ($errors->any())
            <div class="flash bad" role="alert">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if (! session('success') && ! session('error') && ! session('importSummary') && ! $errors->any())
            <div class="note">
                <b>Re-importing updates this template in place.</b>
                <ul>
                    <li>Submitted audit answers stay attached to their questions.</li>
                    <li>Questions removed from the sheet are deleted only if they have no answers.</li>
                    <li>Otherwise they are retired so existing audits still render.</li>
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('questions.import.store') }}" enctype="multipart/form-data" class="stack measure">
            @csrf

            <div class="field">
                <label for="workbook">Workbook (.xlsx)</label>
                <input id="workbook" name="workbook" type="file" accept=".xlsx,.xls" required>
                @error('workbook')
                    <p class="err">{{ $message }}</p>
                @enderror
            </div>

            <div class="row">
                <button class="btn primary" type="submit">{{ __('Import') }}</button>
            </div>
        </form>
    </section>

    @if (session('importSummary'))
        @php($summary = session('importSummary'))

        <section class="card stack">
            <div class="spread">
                <div>
                    <h2>Template <span class="mono">{{ $summary['template'] }}</span> is fully up to date</h2>
                    <p class="sub">
                        {{ $summary['liveTotals']['question'] ?? 0 }} questions,
                        {{ $summary['liveTotals']['text'] ?? 0 }} text blocks, and
                        {{ $summary['liveTotals']['criteria'] ?? 0 }} criteria items from this
                        workbook are now live in the database.
                    </p>
                </div>
                <span class="pill good">Imported</span>
            </div>

            <div class="tablewrap">
                <table class="data">
                    <thead>
                        <tr>
                            <th>Row type</th>
                            <th>Removed</th>
                            <th>Updated</th>
                            <th>Imported (new)</th>
                            <th>Now in database</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach (['question' => 'Questions', 'text' => 'Text blocks', 'criteria' => 'Criteria'] as $key => $label)
                            <tr>
                                <td>{{ $label }}</td>
                                <td class="num sub">{{ $summary['replaced'][$key] ?? 0 }}</td>
                                <td class="num">{{ $summary['updated'][$key] ?? 0 }}</td>
                                <td class="num"><b>{{ $summary['created'][$key] ?? 0 }}</b></td>
                                <td class="num"><b>{{ $summary['liveTotals'][$key] ?? 0 }}</b></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if (empty($summary['skipped']))
                <p class="flash good">✓ Every row with data imported cleanly — nothing needs attention.</p>
            @else
                <details class="note" open>
                    <summary>{{ count($summary['skipped']) }} row(s) need a look</summary>
                    <ul>
                        @foreach ($summary['skipped'] as $s)
                            <li>Row {{ $s['row'] }}: {{ $s['reason'] }}</li>
                        @endforeach
                    </ul>
                </details>
            @endif

            @if (! empty($summary['blankRowsIgnored']))
                <p class="sub">
                    ({{ $summary['blankRowsIgnored'] }} empty row(s) in the sheet were ignored automatically — normal spreadsheet formatting noise, not a data problem.)
                </p>
            @endif

            @if (! empty($summary['warnings']))
                <details class="sub">
                    <summary>{{ count($summary['warnings']) }} data-quality warning(s) (informational only)</summary>
                    <ul class="scroll">
                        @foreach ($summary['warnings'] as $w)
                            <li>{{ $w }}</li>
                        @endforeach
                    </ul>
                </details>
            @endif
        </section>
    @endif

    {{-- Week 2 of the delivery plan replaces this page with the full import and export engine. --}}
    <section class="card">
        <div class="spread">
            <div>
                <h2>CSV and Excel import and export</h2>
                <p class="sub">Field mapping, row validation with clear messages, and export of the live question set to CSV and Excel.</p>
            </div>
            <span class="pill lav">Coming in Week 2</span>
        </div>
    </section>
</x-app-layout>
