<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Import Questions') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 space-y-4">
                    <x-auth-session-status class="mb-2" :status="session('success')" />
                    <x-error-session-status class="mb-2" :status="session('error')" />

                    @if ($errors->any())
                        <div class="font-medium text-sm text-red-600">
                            <ul class="list-disc list-inside">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <p class="text-sm text-gray-600">
                        Upload the question-bank workbook (<code>.xlsx</code>) to import its
                        <code>Data</code> sheet.
                    </p>

                    <div class="rounded-md bg-amber-50 border border-amber-200 p-4 text-sm text-amber-800 flex gap-2">
                        <span aria-hidden="true">⚠️</span>
                        <p>
                            <strong>Every import replaces existing data for that template.</strong>
                        </p>
                    </div>

                    <form method="POST" action="{{ route('questions.import.store') }}" enctype="multipart/form-data" class="space-y-4">
                        @csrf

                        <div>
                            <x-input-label for="workbook" value="Workbook (.xlsx)" />
                            <input id="workbook" name="workbook" type="file" accept=".xlsx,.xls" required
                                class="mt-1 block w-full text-sm text-gray-600 border border-gray-300 rounded-md file:mr-4 file:py-2 file:px-4 file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100" />
                            <x-input-error :messages="$errors->get('workbook')" class="mt-2" />
                        </div>

                        <x-primary-button>
                            {{ __('Import') }}
                        </x-primary-button>
                    </form>
                </div>
            </div>

            @if (session('importSummary'))
                @php($summary = session('importSummary'))

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border-t-4 border-green-500">
                    <div class="p-6 text-gray-900 space-y-4">
                        <div class="flex items-start gap-3">
                            <span class="text-2xl leading-none" aria-hidden="true">✅</span>
                            <div>
                                <h3 class="font-semibold text-lg text-gray-800">
                                    Template <span class="font-mono">{{ $summary['template'] }}</span> is fully up to date
                                </h3>
                                <p class="text-sm text-gray-600 mt-1">
                                    {{ $summary['liveTotals']['question'] ?? 0 }} questions,
                                    {{ $summary['liveTotals']['text'] ?? 0 }} text blocks, and
                                    {{ $summary['liveTotals']['criteria'] ?? 0 }} criteria items from this
                                    workbook are now live in the database.
                                </p>
                            </div>
                        </div>

                        <table class="min-w-full text-sm text-left">
                            <thead>
                                <tr class="text-xs text-gray-500 uppercase tracking-wide">
                                    <th class="py-1 pr-4">Row type</th>
                                    <th class="py-1 pr-4">Replaced (old)</th>
                                    <th class="py-1 pr-4">Imported (new)</th>
                                    <th class="py-1 pr-4">Now in database</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach (['question' => 'Questions', 'text' => 'Text blocks', 'criteria' => 'Criteria'] as $key => $label)
                                    <tr class="border-t border-gray-100">
                                        <td class="py-1 pr-4">{{ $label }}</td>
                                        <td class="py-1 pr-4 text-gray-500">{{ $summary['replaced'][$key] ?? 0 }}</td>
                                        <td class="py-1 pr-4 font-medium text-green-700">{{ $summary['created'][$key] ?? 0 }}</td>
                                        <td class="py-1 pr-4 font-semibold">{{ $summary['liveTotals'][$key] ?? 0 }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>

                        @if (empty($summary['skipped']))
                            <p class="text-sm text-green-700 flex items-center gap-1.5">
                                <span>✓</span>
                                <span>Every row with data imported cleanly — nothing needs attention.</span>
                            </p>
                        @else
                            <details class="text-sm" open>
                                <summary class="cursor-pointer font-medium text-amber-700">
                                    {{ count($summary['skipped']) }} row(s) need a look
                                </summary>
                                <ul class="list-disc list-inside mt-2 text-gray-600 space-y-1">
                                    @foreach ($summary['skipped'] as $s)
                                        <li>Row {{ $s['row'] }}: {{ $s['reason'] }}</li>
                                    @endforeach
                                </ul>
                            </details>
                        @endif

                        @if (!empty($summary['blankRowsIgnored']))
                            <p class="text-xs text-gray-400">
                                ({{ $summary['blankRowsIgnored'] }} empty row(s) in the sheet were ignored automatically — normal spreadsheet formatting noise, not a data problem.)
                            </p>
                        @endif

                        @if (!empty($summary['warnings']))
                            <details class="text-sm">
                                <summary class="cursor-pointer font-medium text-gray-500">
                                    {{ count($summary['warnings']) }} data-quality warning(s) (informational only)
                                </summary>
                                <ul class="list-disc list-inside mt-2 text-gray-500 space-y-1 max-h-64 overflow-y-auto">
                                    @foreach ($summary['warnings'] as $w)
                                        <li>{{ $w }}</li>
                                    @endforeach
                                </ul>
                            </details>
                        @endif
                    </div>
                </div>
            @endif

        </div>
    </div>
</x-app-layout>
