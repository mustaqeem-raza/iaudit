@php
    /** @var array $auditData */
    $auditData      = $auditData ?? [];
    $shipName       = $auditData['ship_name']     ?? 'N/A';
    $shipMnemonic   = $auditData['ship_mnemonic'] ?? '';
    $userName       = $auditData['user_name']     ?? 'N/A';
    $userTitle      = $auditData['user_title']    ?? '';
    $userEmail      = $auditData['user_email']    ?? '';
    $reference      = $auditData['reference']     ?? 'N/A';
    $companyName    = $auditData['company_name']  ?? 'N/A';
    $fleetName      = $auditData['fleet_name']    ?? 'N/A';
    $status         = $auditData['status']        ?? 'N/A';
    $score          = $auditData['score']         ?? null;
    $totalAnswers   = $auditData['total_answers'] ?? 0;
    $yesCount       = $auditData['yes_count']     ?? 0;
    $noCount        = $auditData['no_count']      ?? 0;
    $naCount        = $auditData['na_count']      ?? 0;
    $departments    = $auditData['departments']    ?? collect();
    $trapLocations  = $auditData['trap_locations'] ?? collect();
    $efkLocations   = $auditData['efk_locations']  ?? collect();

    $compliance = $totalAnswers > 0
        ? number_format(($yesCount / $totalAnswers) * 100, 1) . '%'
        : 'N/A';
    $submittedAt = isset($auditData['submitted_at']) && $auditData['submitted_at']
        ? \Carbon\Carbon::parse($auditData['submitted_at'])->format('l, j M Y')
        : 'N/A';
    $submittedShort = isset($auditData['submitted_at']) && $auditData['submitted_at']
        ? \Carbon\Carbon::parse($auditData['submitted_at'])->format('D, j M Y')
        : 'N/A';

    $headerImg = 'data:image/png;base64,' . base64_encode(file_get_contents(public_path('assets/header.png')));
    $footerImg = 'data:image/png;base64,' . base64_encode(file_get_contents(public_path('assets/footer.png')));

    // Page numbering & section numbering
    $pageNum    = 2; // cover is 1, details starts at 2
    $sectionNum = 1; // section 1 = Audit Details

    $answerCellClass = function ($answer) {
        return match ($answer) {
            'Yes'  => 'doc-yes',
            'No'   => 'doc-no',
            'N/A'  => 'doc-yes',
            default => 'doc-yes',
        };
    };

    $hasReport = $departments->contains(fn($d) => ($d['has_answers'] ?? false));
@endphp
<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <title>Integrated Pest Management Audit Report</title>
    <style>
        {!! file_get_contents(public_path('css/pdf-style.css')) !!}

        body { font-family: 'Montserrat', Arial, Helvetica, sans-serif; }
    </style>
</head>

<body>
    <main class="stage">

        {{-- =========================================================== --}}
        {{-- PAGE 1: COVER --}}
        {{-- =========================================================== --}}
        <section class="page" aria-label="Cover page">
            <img class="header" src="{{ $headerImg }}" alt="" />
            <div class="stamp" aria-label="Internal use only stamp">
                <div>Ship &amp; Head Office</div>
                <div>Internal Use ONLY</div>
            </div>
            <div class="block title">
                <div class="h2">Integrated Pest Management</div>
                <div class="h2">Audit Report</div>
            </div>
            <div class="block vessel">
                {{ $shipName }}@if($shipMnemonic) ({{ $shipMnemonic }})@endif
            </div>
            <div class="block consultant">
                <div class="label">IPM Consultant</div>
                <div class="value">{{ $userName }}@if($userTitle), {{ $userTitle }}@endif</div>
            </div>
            <div class="block onbehalf"><em>For and on behalf of Nutra Stat (UK) Limited</em></div>
            <div class="block period">
                <div class="label">Audit Date</div>
                <div class="value">{{ $submittedAt }}</div>
            </div>
            <div class="block ports">
                <div class="label">Reference</div>
                <div class="value">{{ $reference }}</div>
            </div>
            <div class="block prepared">
                <div class="label">Prepared For</div>
                <div class="value">{{ $companyName }} — {{ $fleetName }}</div>
            </div>
            <div class="footer-text" aria-label="Company details">
                <div class="ft-row"><span class="k">Office Address:</span> Nutra Stat (UK) Limited, Hangar 7, Cecil Pashley Way, Shoreham Airport, Shoreham-by-Sea, West Sussex, BN43 5FF</div>
                <div class="ft-row"><span class="k">Contact Details:</span> Telephone: +44 (0)7774 014896 &nbsp;&nbsp; Email: admin@nutrastat.com &nbsp;&nbsp; Web: www.nutrastat.com</div>
                <div class="ft-row"><span class="k">Incorporated:</span> Companies House, Crown Way, Maindy, Cardiff, CF14 3UZ, United Kingdom (DX 33050) &nbsp;&nbsp; Registration No: 2894963</div>
                <div class="ft-row"><span class="k">Registered office:</span> c/o Ayres Bright Vickers, Chartered Accountants, Bishopstoke, 36 Crescent Road, Worthing, West Sussex, BN11 1RL, UK</div>
                <div class="ft-row"><span class="k">Prof. Association:</span> National Pest Technicians Association, NPTA House, Hall Lane, Knoulton, Nottingham, NG12 3EF &nbsp;&nbsp; Member: 504</div>
            </div>
            <img class="footer-wedge" src="{{ $footerImg }}" alt="" />
        </section>

        {{-- =========================================================== --}}
        {{-- PAGE 2: AUDIT DETAILS, APPROVAL & SUMMARY (Section 1) --}}
        {{-- =========================================================== --}}
        <section class="page page-inner" aria-label="Audit details page">
            <img class="header" src="{{ $headerImg }}" alt="" />

            <div class="meta-top">
                <div>IPM Audit Report: <b>{{ $shipName }}@if($shipMnemonic) ({{ $shipMnemonic }})@endif</b></div>
                <div>Audit Date: <b>{{ $submittedShort }}</b></div>
            </div>

            <div class="content-area">
                <div class="section-title">
                    <span class="section-num">{{ $sectionNum }}.</span>
                    <span class="section-text">Audit Details</span>
                </div>
                <div class="section-rule"></div>

                <div class="subheading">Audit and Ship Contact Details</div>

                <table class="report-table contact-table">
                    <thead>
                        <tr><th colspan="4">Contact Details</th></tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="cell-label">Ship Name:</td>
                            <td class="cell-value">{{ $shipName }}</td>
                            <td class="cell-label">Mnemonic:</td>
                            <td class="cell-value">{{ $shipMnemonic ?: '—' }}</td>
                        </tr>
                        <tr>
                            <td class="cell-label">Fleet:</td>
                            <td class="cell-value">{{ $fleetName }}</td>
                            <td class="cell-label">Company:</td>
                            <td class="cell-value">{{ $companyName }}</td>
                        </tr>
                        <tr>
                            <td class="cell-label">IPM Consultant Name:</td>
                            <td class="cell-value">{{ $userName }}</td>
                            <td class="cell-label">Position:</td>
                            <td class="cell-value">{{ $userTitle ?: '—' }}</td>
                        </tr>
                        <tr>
                            <td class="cell-label">Consultant Email:</td>
                            <td class="cell-value" colspan="3">{{ $userEmail ?: '—' }}</td>
                        </tr>
                        <tr>
                            <td class="cell-label">Audit Date:</td>
                            <td class="cell-value">{{ $submittedAt }}</td>
                            <td class="cell-label">Reference:</td>
                            <td class="cell-value">{{ $reference }}</td>
                        </tr>
                        <tr>
                            <td class="cell-label">Status:</td>
                            <td class="cell-value">{{ ucfirst($status) }}</td>
                            <td class="cell-label">Score:</td>
                            <td class="cell-value">{{ $score ?? '—' }}</td>
                        </tr>
                    </tbody>
                </table>

                <div class="spacer-xl"></div>

                <div class="subheading subheading-lg">Report Approval and Contact Details</div>

                <table class="report-table approval-table">
                    <thead>
                        <tr><th colspan="6">Approval and Contact Details</th></tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="cell-label">Approved by:</td>
                            <td class="cell-label">Name:</td>
                            <td class="cell-value" colspan="2">Graham Hobson</td>
                            <td class="cell-label">Position:</td>
                            <td class="cell-value">Managing Director</td>
                        </tr>
                        <tr class="signature-row">
                            <td class="cell-label">Signature:</td>
                            <td class="cell-value" colspan="5"></td>
                        </tr>
                        <tr class="address-row">
                            <td class="cell-label">Contact Address:</td>
                            <td class="cell-value address" colspan="5">
                                <b>Nutra Stat (UK) Limited</b><br>
                                Hangar 7 (1<sup>st</sup> Floor Offices)<br>
                                Cecil Pashley Way, Shoreham Airport<br>
                                Shoreham-by-Sea<br>
                                West Sussex<br>
                                BN43 5FF United Kingdom
                            </td>
                        </tr>
                        <tr>
                            <td class="cell-label">Email:</td>
                            <td class="cell-value">trainers@nutrastat.com</td>
                            <td class="cell-label">Telephone:</td>
                            <td class="cell-value">0845 2300 717</td>
                            <td class="cell-label">Mobile:</td>
                            <td class="cell-value">+44 (0)7774 014896</td>
                        </tr>
                    </tbody>
                </table>

                <div class="spacer-xl"></div>

                <div class="subheading subheading-lg">Audit Summary</div>

                <table class="report-table approval-table">
                    <thead>
                        <tr><th colspan="4">Answer Summary</th></tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="cell-label">Total Answers:</td>
                            <td class="cell-value">{{ $totalAnswers }}</td>
                            <td class="cell-label">Compliance Rate:</td>
                            <td class="cell-value">{{ $compliance }}</td>
                        </tr>
                        <tr>
                            <td class="cell-label">Yes:</td>
                            <td class="cell-value">{{ $yesCount }}</td>
                            <td class="cell-label">No:</td>
                            <td class="cell-value">{{ $noCount }}</td>
                        </tr>
                        <tr>
                            <td class="cell-label">N/A:</td>
                            <td class="cell-value" colspan="3">{{ $naCount }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <img class="footer-wedge" src="{{ $footerImg }}" alt="" />
            <div class="page-no">Page {{ $pageNum++ }}</div>
        </section>

        {{-- =========================================================== --}}
        {{-- DYNAMIC DEPARTMENT SECTIONS (Section 2..N) --}}
        {{-- =========================================================== --}}
        @php $sectionNum = 2; @endphp
        @forelse($departments as $dept)
            @if(! ($dept['has_answers'] ?? false))
                @continue
            @endif

            <section class="page page-inner" aria-label="{{ $dept['department_name'] }} page">
                <img class="header" src="{{ $headerImg }}" alt="" />

                <div class="meta-top">
                    <div>IPM Audit Report: <b>{{ $shipName }}@if($shipMnemonic) ({{ $shipMnemonic }})@endif</b></div>
                    <div>Audit Date: <b>{{ $submittedShort }}</b></div>
                </div>

                <div class="content-area">
                    <div class="section-title">
                        <span class="section-num">{{ $sectionNum++ }}.</span>
                        <span class="section-text">{{ $dept['department_name'] }}</span>
                    </div>
                    <div class="section-rule"></div>

                    <div class="section-lead">
                        The Integrated Pest Management observations and audit results for the
                        <b>{{ $dept['department_name'] }}</b> department.
                    </div>

                    @foreach($dept['headings'] as $heading)
                        @if(! ($heading['has_answers'] ?? false))
                            @continue
                        @endif

                        <div class="subheading subheading-lg">{{ $heading['heading_name'] }}</div>

                        @foreach($heading['subheadings'] as $sub)
                            @if(! ($sub['has_answers'] ?? false))
                                @continue
                            @endif

                            <div class="subheading">{{ $sub['subheading_name'] }}</div>

                            {{-- Criteria box (information_text from any question in this subheading) --}}
                            @if(!empty($sub['criteria_text']))
                                <div class="criteria-box criteria-box-tight">
                                    <table>
                                        <colgroup>
                                            <col style="width: 14%">
                                            <col style="width: 86%">
                                        </colgroup>
                                        <tbody>
                                            <tr>
                                                <td class="criteria-label">Criteria:</td>
                                                <td class="criteria-body">{!! nl2br(e($sub['criteria_text'])) !!}</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            @endif

                            {{-- One doc-table per category, listing every question + its answer --}}
                            @foreach($sub['categories'] as $cat)
                                <table class="doc-table doc-table-tight">
                                    <colgroup>
                                        <col style="width: 18%">
                                        <col style="width: 72%">
                                        <col style="width: 10%">
                                    </colgroup>
                                    <thead>
                                        <tr>
                                            <th class="doc-head" colspan="3">{{ $cat['category_name'] }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($cat['questions'] as $q)
                                            <tr class="doc-row">
                                                <td class="doc-item">Q{{ $q['question_id'] }}</td>
                                                <td class="doc-q">{{ $q['question_text'] ?? '—' }}</td>
                                                <td class="{{ $answerCellClass($q['answer']) }}">
                                                    {{ $q['answer'] ?? '—' }}
                                                </td>
                                            </tr>
                                            @if(!empty($q['note']))
                                                <tr class="doc-row">
                                                    <td class="doc-item" style="font-style:italic;color:#666;">Note</td>
                                                    <td class="doc-q" colspan="2" style="font-style:italic;color:#555;">
                                                        {{ $q['note'] }}
                                                    </td>
                                                </tr>
                                            @endif
                                        @endforeach
                                    </tbody>
                                </table>
                            @endforeach

                            {{-- Non-compliance blocks for any "No" answers in this subheading --}}
                            @if(!empty($sub['non_compliant']) && count($sub['non_compliant']) > 0)
                                <div class="noncompliance-title">Non-Compliance: {{ $sub['subheading_name'] }}</div>

                                @foreach($sub['non_compliant'] as $nc)
                                    @php
                                        $ncRefs = collect($nc['ncs'] ?? []);
                                        $firstNc = $ncRefs->first();
                                    @endphp
                                    <div class="nc-box">
                                        <div class="nc-head">
                                            {{ optional($firstNc)->nc_heading ?? ('Non-Compliance: ' . ($nc['question_text'] ?? '—')) }}
                                        </div>

                                        @if(optional($firstNc)->nc_text)
                                            <div class="nc-desc">
                                                {!! nl2br(e($firstNc->nc_text)) !!}
                                            </div>
                                        @else
                                            <div class="nc-desc">
                                                {{ $nc['question_text'] ?? '—' }}
                                            </div>
                                        @endif

                                        <div class="nc-rows">
                                            @if(optional($firstNc)->nc_rem_text)
                                                <div class="nc-lab">{{ $firstNc->nc_rem_hd ?? 'Remedial Action:' }}</div>
                                                <div class="nc-val">{{ $firstNc->nc_rem_text }}</div>
                                            @endif

                                            @if(!empty($nc['note']))
                                                <div class="nc-lab">{{ optional($firstNc)->nc_con_hd ?? 'Consultant Comment:' }}</div>
                                                <div class="nc-val">{{ $nc['note'] }}</div>
                                            @elseif(optional($firstNc)->nc_con_text)
                                                <div class="nc-lab">{{ $firstNc->nc_con_hd ?? 'Consultant Comment:' }}</div>
                                                <div class="nc-val">{{ $firstNc->nc_con_text }}</div>
                                            @endif

                                            @if(optional($firstNc)->nc_usph_text)
                                                <div class="nc-lab">{{ $firstNc->nc_usph_hd ?? 'USPH/VSP Reference:' }}</div>
                                                <div class="nc-val">{{ $firstNc->nc_usph_text }}</div>
                                            @endif

                                            @if(optional($firstNc)->nc_ipm_ref)
                                                <div class="nc-lab">{{ $firstNc->nc_ipm_hd ?? 'IPM Plan Reference:' }}</div>
                                                <div class="nc-val">{{ $firstNc->nc_ipm_ref }}</div>
                                            @endif
                                        </div>

                                        {{-- Photos / attachments --}}
                                        @if(!empty($nc['files']))
                                            <div class="pic-grid">
                                                @php $chunks = collect($nc['files'])->chunk(2); @endphp
                                                @foreach($chunks as $rowFiles)
                                                    <div class="pic-row">
                                                        @foreach($rowFiles as $file)
                                                            <div class="pic-cell">
                                                                <div class="pic-head">{{ $file['original_name'] ?? 'Attachment' }}</div>
                                                                @if(isset($file['mime']) && str_starts_with($file['mime'], 'image/') && file_exists(public_path('storage/' . $file['path'])))
                                                                    <div class="pic-body">
                                                                        <img src="data:{{ $file['mime'] }};base64,{{ base64_encode(file_get_contents(public_path('storage/' . $file['path']))) }}"
                                                                             style="max-width:100%; max-height:140px;" alt="">
                                                                    </div>
                                                                @else
                                                                    <div class="pic-body" style="font-size:10px;color:#666;padding:8px;">
                                                                        {{ $file['original_name'] ?? '' }}
                                                                    </div>
                                                                @endif
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                @endforeach
                                            </div>
                                        @endif
                                    </div>
                                @endforeach
                            @endif
                        @endforeach
                    @endforeach

                    {{-- =========================================================== --}}
                    {{-- DEPARTMENT REFERENCE: Cockroach Trap Locations            --}}
                    {{-- =========================================================== --}}
                    @php $deptTraps = $trapLocations->get($dept['department_id']); @endphp
                    @if($deptTraps && !empty($deptTraps['rows']))
                        <div class="subheading">Cockroach Trap Locations</div>

                        <table class="doc-table doc-table-tight">
                            <colgroup>
                                <col style="width: 16%">
                                <col style="width: 22%">
                                <col style="width: 22%">
                                <col style="width: 30%">
                                <col style="width: 10%">
                            </colgroup>
                            <thead>
                                <tr>
                                    <th class="doc-head" colspan="5">Trap Locations — {{ $deptTraps['department_name'] ?? $dept['department_name'] }}</th>
                                </tr>
                                <tr>
                                    <th>Deck</th>
                                    <th>Main Section</th>
                                    <th>Sub Section</th>
                                    <th>Trap Location</th>
                                    <th>Type</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($deptTraps['rows'] as $row)
                                    <tr class="doc-row">
                                        <td class="doc-item">{{ $row->deck ?? '—' }}</td>
                                        <td class="doc-q">{{ $row->main_section ?? '—' }}</td>
                                        <td class="doc-q">{{ $row->sub_section ?? '—' }}</td>
                                        <td class="doc-q">{{ $row->trap_location ?? '—' }}</td>
                                        <td class="doc-yes">{{ $row->trap_type ?? '—' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @endif

                    {{-- =========================================================== --}}
                    {{-- DEPARTMENT REFERENCE: Electric Fly Killer Locations       --}}
                    {{-- =========================================================== --}}
                    @php $deptEfks = $efkLocations->get($dept['department_id']); @endphp
                    @if($deptEfks && !empty($deptEfks['rows']))
                        <div class="subheading">Electric Fly Killer Locations</div>

                        <table class="doc-table doc-table-tight">
                            <colgroup>
                                <col style="width: 18%">
                                <col style="width: 28%">
                                <col style="width: 38%">
                                <col style="width: 16%">
                            </colgroup>
                            <thead>
                                <tr>
                                    <th class="doc-head" colspan="4">EFK Locations — {{ $deptEfks['department_name'] ?? $dept['department_name'] }}</th>
                                </tr>
                                <tr>
                                    <th>Deck</th>
                                    <th>Area</th>
                                    <th>Location</th>
                                    <th>Type</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($deptEfks['rows'] as $row)
                                    <tr class="doc-row">
                                        <td class="doc-item">{{ $row->deck ?? '—' }}</td>
                                        <td class="doc-q">{{ $row->area ?? '—' }}</td>
                                        <td class="doc-q">{{ $row->location ?? '—' }}</td>
                                        <td class="doc-yes">{{ $row->type ?? '—' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @endif
                </div>

                <img class="footer-wedge" src="{{ $footerImg }}" alt="" />
                <div class="page-no">Page {{ $pageNum++ }}</div>
            </section>
        @empty
            {{-- no departments at all --}}
        @endforelse

        {{-- =========================================================== --}}
        {{-- FALLBACK: no audit answers exist yet                        --}}
        {{-- =========================================================== --}}
        @if(! $hasReport)
            <section class="page page-inner" aria-label="No answers">
                <img class="header" src="{{ $headerImg }}" alt="" />
                <div class="meta-top">
                    <div>IPM Audit Report: <b>{{ $shipName }}</b></div>
                    <div>Audit Date: <b>{{ $submittedShort }}</b></div>
                </div>
                <div class="content-area">
                    <div class="section-title">
                        <span class="section-num">2.</span>
                        <span class="section-text">No Answers Recorded</span>
                    </div>
                    <div class="section-rule"></div>
                    <p style="margin-top: 24px; color:#666;">
                        No audit answers have been submitted for this audit yet.
                    </p>
                </div>
                <img class="footer-wedge" src="{{ $footerImg }}" alt="" />
                <div class="page-no">Page {{ $pageNum }}</div>
            </section>
        @endif

    </main>
</body>

</html>
