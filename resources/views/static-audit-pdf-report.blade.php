<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <title>Integrated Pest Management Audit Report</title>
    <style>
        {!! file_get_contents(public_path('css/pdf-style.css')) !!}

        body {
            font-family: 'Montserrat', Arial, Helvetica, sans-serif;
        }
    </style>
</head>

<body>
    <main class="stage">
        <section class="page" aria-label="Cover page">
            <img class="header" src="data:image/png;base64,{{ base64_encode(file_get_contents(public_path('assets/header.png'))) }}" alt="" />
            <div class="stamp" aria-label="Internal use only stamp">
                <div>Ship &amp; Head Office</div>
                <div>Internal Use ONLY</div>
            </div>
            <div class="block title">
                <div class="h2">Integrated Pest Management</div>
                <div class="h2">Audit Report</div>
            </div>
            <div class="block vessel">Viking Mars (VOCX-MARS)</div>
            <div class="block consultant">
                <div class="label">IPM Consultant</div>
                <div class="value">IPMConsultantName, IPMConsultantPosition</div>
            </div>
            <div class="block onbehalf"><em>For and on behalf of Nutra Stat (UK) Limited</em></div>
            <div class="block period">
                <div class="label">Audit Period</div>
                <div class="value">Saturday, 7 Jun 2025 to Saturday, 14 Jun 2025</div>
            </div>
            <div class="block ports">
                <div class="label">Audit Ports</div>
                <div class="value">PortFromHere to PortToHere</div>
            </div>
            <div class="block prepared">
                <div class="label">Prepared For</div>
                <div class="value">PreparedForNameHere, PreparedForPositionHere</div>
            </div>
            <div class="footer-text" aria-label="Company details">
                <div class="ft-row"><span class="k">Office Address:</span> Nutra Stat (UK) Limited, Hangar 7, Cecil Pashley Way, Shoreham Airport, Shoreham-by-Sea, West Sussex, BN43 5??</div>
                <div class="ft-row"><span class="k">Contact Details:</span> Telephone: +44 (0)7774 014896 &nbsp;&nbsp; Email: admin@nutrastat.com &nbsp;&nbsp; Web: www.nuttrastat.com</div>
                <div class="ft-row"><span class="k">Incorporated:</span> Companies House, Crown Way, Maindy, Cardiff, CF14 3UZ, United Kingdom (DX 33050) &nbsp;&nbsp; Registration No: 2894963</div>
                <div class="ft-row"><span class="k">Registered office:</span> c/o Ayres Bright Vickers, Chartered Accountants, Bishopstoke, 36 Crescent Road, Worthing, West Sussex, BN11 1RL, UK</div>
                <div class="ft-row"><span class="k">Prof. Association:</span> National Pest Technicians Association, NPTA House, Hall Lane, Knoulton, Nottingham, NG12 3EF &nbsp;&nbsp; Member: 504</div>
            </div>
            <img class="footer-wedge" src="data:image/png;base64,{{ base64_encode(file_get_contents(public_path('assets/footer.png'))) }}" alt="" />
        </section>

        <section class="page page-inner" aria-label="Inner page template">
            <img class="header" src="data:image/png;base64,{{ base64_encode(file_get_contents(public_path('assets/header.png'))) }}" alt="" />

            <div class="meta-top">
                <div>IPM Audit Report: <b>Viking Mars (VOCX-MARS)</b></div>
                <div>Audit Date: <b>Sat, 7 Jun 2025</b> to <b>Sat, 14 Jun 2025</b></div>
            </div>

            <div class="content-area">
                <!-- Put your page content here -->
            </div>

            <img class="footer-wedge" src="data:image/png;base64,{{ base64_encode(file_get_contents(public_path('assets/footer.png'))) }}" alt="" />
            <div class="page-no">Page 2 of 12</div>
        </section>

        <section class="page page-inner" aria-label="Audit details page">

            <!-- Header graphic -->
            <img class="header" src="data:image/png;base64,{{ base64_encode(file_get_contents(public_path('assets/header.png'))) }}" alt="" />

            <!-- Top-right meta -->
            <div class="meta-top">
                <div>IPM Audit Report: <b>Viking Mars (VOCX-MARS)</b></div>
                <div>Audit Date: <b>Sat, 7 Jun 2025</b> to <b>Sat, 14 Jun 2025</b></div>
            </div>

            <!-- Page content -->
            <div class="content-area">

                <!-- Section title + rule -->
                <div class="section-title">
                    <span class="section-num">1.</span>
                    <span class="section-text">Audit Details</span>
                </div>
                <div class="section-rule"></div>

                <!-- Subheading -->
                <div class="subheading">Audit and Ship Contact Details</div>

                <!-- Table 1 -->
                <table class="report-table contact-table">
                    <thead>
                        <tr>
                            <th colspan="4">Contact Details</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="cell-label">Ship Name:</td>
                            <td class="cell-value">Viking Mars</td>
                            <td class="cell-label">Mnemonic:</td>
                            <td class="cell-value">VOCX-MARS</td>
                        </tr>
                        <tr>
                            <td class="cell-label">IPM Consultant Name:</td>
                            <td class="cell-value">IPMConsultantName</td>
                            <td class="cell-label">Position:</td>
                            <td class="cell-value">IPMConsultantPosition</td>
                        </tr>
                        <tr>
                            <td class="cell-label">Prepared for:</td>
                            <td class="cell-value">PreparedForNameHere</td>
                            <td class="cell-label">Position:</td>
                            <td class="cell-value">PreparedForPositionHere</td>
                        </tr>
                        <tr>
                            <td class="cell-label">Pest Control<br>Responsible Officer:</td>
                            <td class="cell-value">EnterPCRONameHere</td>
                            <td class="cell-label">Position:</td>
                            <td class="cell-value">EnterPCROPositionHere</td>
                        </tr>
                        <tr>
                            <td class="cell-label">Pest Control Officer:</td>
                            <td class="cell-value">EnterPCONameHere</td>
                            <td class="cell-label">Position:</td>
                            <td class="cell-value">EnterPCOPositionHere</td>
                        </tr>
                        <tr>
                            <td class="cell-label">Audit Date From:</td>
                            <td class="cell-value">Saturday, 7 Jun 2025</td>
                            <td class="cell-label">Date To:</td>
                            <td class="cell-value">Saturday, 14 Jun 2025</td>
                        </tr>
                        <tr>
                            <td class="cell-label">Port From:</td>
                            <td class="cell-value">PortFromHere</td>
                            <td class="cell-label">Port To:</td>
                            <td class="cell-value">PortToHere</td>
                        </tr>
                    </tbody>
                </table>

                <div class="spacer-xl"></div>

                <!-- Section 2 -->
                <div class="subheading subheading-lg">Report Approval and Contact Details</div>

                <!-- Table 2 -->
                <table class="report-table approval-table">
                    <thead>
                        <tr>
                            <th colspan="6">Approval and Contact Details</th>
                        </tr>
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

            </div>

            <!-- Footer wedge + page number -->
            <img class="footer-wedge" src="data:image/png;base64,{{ base64_encode(file_get_contents(public_path('assets/footer.png'))) }}" alt="" />
            <div class="page-no">Page 3 of 12</div>

        </section>

        <section class="page page-inner" aria-label="Responsibilities and documentation page">

            <img class="header" src="data:image/png;base64,{{ base64_encode(file_get_contents(public_path('assets/header.png'))) }}" alt="" />

            <div class="meta-top">
                <div>IPM Audit Report: <b>Viking Mars (VOCX-MARS)</b></div>
                <div>Audit Date: <b>Sat, 7 Jun 2025</b> to <b>Sat, 14 Jun 2025</b></div>
            </div>

            <div class="content-area">

                <div class="section-title">
                    <span class="section-num">2.</span>
                    <span class="section-text">IPM Responsibilities: PCRO and PCO</span>
                </div>
                <div class="section-rule"></div>

                <div class="subheading">IPM Responsibilities</div>

                <!-- Criteria box 1 -->
                <div class="criteria-box">
                    <table>
                        <colgroup>
                            <col style="width: 14%">
                            <col style="width: 86%">
                        </colgroup>
                        <tbody>
                            <tr>
                                <td class="criteria-label">Criteria:</td>
                                <td class="criteria-body">
                                    <p>
                                        The Pest Control Responsible Officer (PCRO) takes responsibility on behalf of the Captain/Master for the Integrated Pest
                                        Management standards on the ship, liaising with the Pest Control Officer (PCO) who undertakes the day-to-day management of
                                        the IPM Plan. The PCO also liaises with the Department Heads to ensure that they follow the IPM Plan, undertaking:
                                    </p>
                                    <ul>
                                        <li>Cockroach Trap Monitoring</li>
                                        <li>Electric Fly Killer Weekly Count</li>
                                        <li>Active Weekly Pest Inspections</li>
                                        <li>Deliveries Inspections</li>
                                    </ul>
                                </td>
                            </tr>
                            <tr>
                                <td class="criteria-label">IPM Plan:</td>
                                <td class="criteria-body criteria-body-tight">XXXXXXXXXXXXXX</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="red-x">XXXXXXXXXXXXXXXXXX</div>

                <div class="subheading">IPM Documentation</div>

                <!-- Criteria box 2 -->
                <div class="criteria-box">
                    <table>
                        <colgroup>
                            <col style="width: 14%">
                            <col style="width: 86%">
                        </colgroup>
                        <tbody>
                            <tr>
                                <td class="criteria-label">Criteria:</td>
                                <td class="criteria-body">
                                    <p>
                                        Public Health Authorities require IPM Documentation to be complete and available on request. The PCRO or PCO should be able to provide
                                        documentation for the last 12 months, except for IPM training, which should include all training completed in the previous 3 years.
                                    </p>
                                    <p>
                                        Whilst Worldwide Public Health now accepts electronic filing, Nutrast still recommends hardcopy folders due to the frequency of some of our
                                        contract ships losing valuable files, often resulting from server and/or backup failures, or the IT Department's purging of files to recover network
                                        drive storage space. However, if the ship only wishes to undertake electronic filing, this is acceptable and physical documents will not be audited.
                                    </p>
                                    <p>
                                        However, if hardcopy folders are maintained, it is recommended that all hardcopy folders be stored in one location for easy access to show visiting
                                        Public Health Authorities, and that all electronic files be available at the same time.
                                    </p>
                                </td>
                            </tr>
                            <tr>
                                <td class="criteria-label">IPM Plan:</td>
                                <td class="criteria-body criteria-body-tight">XXXXXXXXXXXX</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Big documentation checklist table -->
                <table class="doc-table">
                    <colgroup>
                        <col style="width: 18%">
                        <col style="width: 72%">
                        <col style="width: 10%">
                    </colgroup>
                    <thead>
                        <tr>
                            <th class="doc-head" colspan="3">IPM Documentation</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="doc-section">
                            <td colspan="3">Documentation Availability</td>
                        </tr>
                        <tr class="doc-row">
                            <td class="doc-item">Hardcopy</td>
                            <td class="doc-q">Are all folders available for inspection?</td>
                            <td class="doc-yes">Yes</td>
                        </tr>
                        <tr class="doc-row">
                            <td class="doc-item">Electronic</td>
                            <td class="doc-q">Are all folders available for inspection?</td>
                            <td class="doc-yes">Yes</td>
                        </tr>

                        <tr class="doc-section">
                            <td colspan="3">Hardcopy Folder: Completed Logs</td>
                        </tr>
                        <tr class="doc-row">
                            <td class="doc-item">Completed<br>Logs</td>
                            <td class="doc-q">Is the folder complete &amp; available?</td>
                            <td class="doc-yes">Yes</td>
                        </tr>
                        <tr class="doc-row">
                            <td class="doc-item">Active Ins. Time</td>
                            <td class="doc-q">Are the log inspection times accurate and not rounded?</td>
                            <td class="doc-yes">Yes</td>
                        </tr>
                        <tr class="doc-row">
                            <td class="doc-item">Active Ins. Pest</td>
                            <td class="doc-q">Do the inspections contain reported pests and raising issues?</td>
                            <td class="doc-yes">Yes</td>
                        </tr>
                        <tr class="doc-row">
                            <td class="doc-item">Deliveries Time</td>
                            <td class="doc-q">Are the inspection times accurate?</td>
                            <td class="doc-yes">Yes</td>
                        </tr>
                        <tr class="doc-row">
                            <td class="doc-item">Delvry. Generic</td>
                            <td class="doc-q">Are the inspection details free from ‘generic’ keywords</td>
                            <td class="doc-yes">Yes</td>
                        </tr>
                        <tr class="doc-row">
                            <td class="doc-item">Treatment Time</td>
                            <td class="doc-q">Are the inspection times accurate?</td>
                            <td class="doc-yes">Yes</td>
                        </tr>
                        <tr class="doc-row">
                            <td class="doc-item">Treat. Follow Up</td>
                            <td class="doc-q">Are all details being accurately entered, and are treatments being followed up?</td>
                            <td class="doc-yes">Yes</td>
                        </tr>

                        <tr class="doc-section">
                            <td colspan="3">Hardcopy Folder: Training</td>
                        </tr>
                        <tr class="doc-row">
                            <td class="doc-item">Training</td>
                            <td class="doc-q">Is the folder complete &amp; available?</td>
                            <td class="doc-yes">Yes</td>
                        </tr>

                        <tr class="doc-section">
                            <td colspan="3">Hardcopy Folder: Bedbug Logs</td>
                        </tr>
                        <tr class="doc-row">
                            <td class="doc-item">Bedbug Logs</td>
                            <td class="doc-q">Are the folders complete &amp; available?</td>
                            <td class="doc-yes">Yes</td>
                        </tr>

                        <tr class="doc-section">
                            <td colspan="3">Hardcopy Folder: Management</td>
                        </tr>
                        <tr class="doc-row">
                            <td class="doc-item">Management</td>
                            <td class="doc-q">Is the folder complete &amp; available?</td>
                            <td class="doc-yes">Yes</td>
                        </tr>
                    </tbody>
                </table>

            </div>

            <img class="footer-wedge" src="data:image/png;base64,{{ base64_encode(file_get_contents(public_path('assets/footer.png'))) }}" alt="" />
            <div class="page-no">Page 4 of 12</div>

        </section>

        <section class="page page-inner" aria-label="Documentation and pest control locker page">

            <img class="header" src="data:image/png;base64,{{ base64_encode(file_get_contents(public_path('assets/header.png'))) }}" alt="" />

            <div class="meta-top">
                <div>IPM Audit Report: <b>Viking Mars (VOCX-MARS)</b></div>
                <div>Audit Date: <b>Sat, 7 Jun 2025</b> to <b>Sat, 14 Jun 2025</b></div>
            </div>

            <div class="content-area">

                <!-- Continuation of IPM Documentation checklist -->
                <table class="doc-table doc-table-compact">
                    <colgroup>
                        <col style="width: 18%">
                        <col style="width: 72%">
                        <col style="width: 10%">
                    </colgroup>
                    <tbody>
                        <tr class="doc-row">
                            <td class="doc-item">MSDS</td>
                            <td class="doc-q">Are the Safety Data Sheets complete and available?</td>
                            <td class="doc-yes">Yes</td>
                        </tr>

                        <tr class="doc-section">
                            <td colspan="3">Electronic Folder: Current IPM Log</td>
                        </tr>
                        <tr class="doc-row">
                            <td class="doc-item">Log Folder</td>
                            <td class="doc-q">Is the folder and the current IPM Log available for inspection?</td>
                            <td class="doc-yes">Yes</td>
                        </tr>
                        <tr class="doc-row">
                            <td class="doc-item">Current Log</td>
                            <td class="doc-q">Is the current IPM Log available for inspection?</td>
                            <td class="doc-yes">Yes</td>
                        </tr>
                        <tr class="doc-row">
                            <td class="doc-item">Log Maintained</td>
                            <td class="doc-q">Is the IPM Log being maintained, and is it not post-dated?</td>
                            <td class="doc-yes">Yes</td>
                        </tr>

                        <tr class="doc-section">
                            <td colspan="3">Electronic Folder: IPM Plan</td>
                        </tr>
                        <tr class="doc-row">
                            <td class="doc-item">IPM Plan Folder</td>
                            <td class="doc-q">Is the folder available for inspection?</td>
                            <td class="doc-yes">Yes</td>
                        </tr>
                        <tr class="doc-row">
                            <td class="doc-item">Maintained</td>
                            <td class="doc-q">Is the folder being maintained with no additional files or folders?</td>
                            <td class="doc-yes">Yes</td>
                        </tr>

                        <tr class="doc-section">
                            <td colspan="3">Electronic Folder: Public Health</td>
                        </tr>
                        <tr class="doc-row">
                            <td class="doc-item">Public Health</td>
                            <td class="doc-q">Is the folder available for inspection?</td>
                            <td class="doc-yes">Yes</td>
                        </tr>
                        <tr class="doc-row">
                            <td class="doc-item">Maintained</td>
                            <td class="doc-q">Is the folder being maintained with no additional files or folders?</td>
                            <td class="doc-yes">Yes</td>
                        </tr>

                        <tr class="doc-section">
                            <td colspan="3">Electronic Folder: IPM Management</td>
                        </tr>
                        <tr class="doc-row">
                            <td class="doc-item">Advisories</td>
                            <td class="doc-q">Are all advisories filed &amp; available?</td>
                            <td class="doc-yes">Yes</td>
                        </tr>
                        <tr class="doc-row">
                            <td class="doc-item">Audit Reports</td>
                            <td class="doc-q">Are all audit reports filed &amp; available?</td>
                            <td class="doc-yes">Yes</td>
                        </tr>
                        <tr class="doc-row">
                            <td class="doc-item">Log Responses</td>
                            <td class="doc-q">Are log responses filed &amp; available?</td>
                            <td class="doc-yes">Yes</td>
                        </tr>

                        <!-- Blank rows (as in PDF) -->
                        <tr class="doc-blank">
                            <td></td>
                            <td></td>
                            <td></td>
                        </tr>
                        <tr class="doc-blank">
                            <td></td>
                            <td></td>
                            <td></td>
                        </tr>
                        <tr class="doc-blank">
                            <td></td>
                            <td></td>
                            <td></td>
                        </tr>
                    </tbody>
                </table>

                <div class="spacer-xl"></div>

                <!-- Pest Control Locker -->
                <div class="subheading">Pest Control Locker</div>

                <div class="criteria-box criteria-box-tight">
                    <table>
                        <colgroup>
                            <col style="width: 14%">
                            <col style="width: 86%">
                        </colgroup>
                        <tbody>
                            <tr>
                                <td class="criteria-label">Criteria:</td>
                                <td class="criteria-body">
                                    Public Health and IPM Best Practice require a pest control locker to be available for the storage of professional IPM
                                    chemicals and equipment, which should have signed, restricted access, always be locked, and not be situated in a food area.
                                </td>
                            </tr>
                            <tr>
                                <td class="criteria-label">IPM Plan:</td>
                                <td class="criteria-body criteria-body-tight">XXXXXXXXXXXXX</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <table class="doc-table doc-table-pcl">
                    <colgroup>
                        <col style="width: 18%">
                        <col style="width: 72%">
                        <col style="width: 10%">
                    </colgroup>
                    <thead>
                        <tr>
                            <th class="doc-head" colspan="3">Pest Control Locker</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="doc-section">
                            <td colspan="3">Locker</td>
                        </tr>
                        <tr class="doc-row">
                            <td class="doc-item">Specific</td>
                            <td class="doc-q">Is there a designated pest locker only for pest control items?</td>
                            <td class="doc-yes">Yes</td>
                        </tr>
                        <tr class="doc-row">
                            <td class="doc-item">Location</td>
                            <td class="doc-q">Is the pest locker not situated within a food storage or food production area?</td>
                            <td class="doc-yes">Yes</td>
                        </tr>
                        <tr class="doc-row">
                            <td class="doc-item">Locked</td>
                            <td class="doc-q">Was the pest control locker secure and locked at the time of the audit?</td>
                            <td class="doc-yes">Yes</td>
                        </tr>
                        <tr class="doc-row">
                            <td class="doc-item">Access</td>
                            <td class="doc-q">Is there restricted access to the pest control locker key?</td>
                            <td class="doc-yes">Yes</td>
                        </tr>

                        <tr class="doc-subsection">
                            <td colspan="3">Signage</td>
                        </tr>
                        <tr class="doc-row">
                            <td class="doc-item">Outside</td>
                            <td class="doc-q">Is the pest locker correctly signed as a pest control store?</td>
                            <td class="doc-yes">Yes</td>
                        </tr>
                        <tr class="doc-row">
                            <td class="doc-item">Hazardous</td>
                            <td class="doc-q">Is there a hazardous sign outside the pest control store?</td>
                            <td class="doc-yes">Yes</td>
                        </tr>
                        <tr class="doc-row">
                            <td class="doc-item">Mixing Notice</td>
                            <td class="doc-q">Does the pest locker have a mixing area notice?</td>
                            <td class="doc-yes">Yes</td>
                        </tr>
                        <tr class="doc-row">
                            <td class="doc-item">Mixing Sign</td>
                            <td class="doc-q">Is there a mixing sign in the designated mixing area?</td>
                            <td class="doc-yes">Yes</td>
                        </tr>
                        <tr class="doc-row">
                            <td class="doc-item">Restriction</td>
                            <td class="doc-q">Are chemical application notices ready and available?</td>
                            <td class="doc-yes">Yes</td>
                        </tr>
                        <tr class="doc-row">
                            <td class="doc-item">Oa2ki Fact</td>
                            <td class="doc-q">Is the Oa2ki Powder/ Diatomaceous Earth Fact Sheet displayed?</td>
                            <td class="doc-yes">Yes</td>
                        </tr>
                        <tr class="doc-row">
                            <td class="doc-item">Matrix</td>
                            <td class="doc-q">Is the chemical/insect notice available?</td>
                            <td class="doc-yes">Yes</td>
                        </tr>

                        <tr class="doc-section">
                            <td colspan="3">Safety Data Sheet Availability</td>
                        </tr>
                        <tr class="doc-row">
                            <td class="doc-item">Out/In Locker</td>
                            <td class="doc-q">Are all available SDS sheets available inside or outside the pest control locker?</td>
                            <td class="doc-yes">Yes</td>
                        </tr>
                        <tr class="doc-row">
                            <td class="doc-item">Hardcopy<br>Folder</td>
                            <td class="doc-q">Are all available SDS sheets available inside the hardcopy folder?</td>
                            <td class="doc-yes">Yes</td>
                        </tr>
                    </tbody>
                </table>

            </div>

            <img class="footer-wedge" src="data:image/png;base64,{{ base64_encode(file_get_contents(public_path('assets/footer.png'))) }}" alt="" />
            <div class="page-no">Page 5 of 12</div>

        </section>

        <section class="page page-inner" aria-label="Pest control equipment checklist page">

            <img class="header" src="data:image/png;base64,{{ base64_encode(file_get_contents(public_path('assets/header.png'))) }}" alt="" />

            <div class="meta-top">
                <div>IPM Audit Report: <b>Viking Mars (VOCX-MARS)</b></div>
                <div>Audit Date: <b>Sat, 7 Jun 2025</b> to <b>Sat, 14 Jun 2025</b></div>
            </div>

            <div class="content-area">

                <!-- Checklist table -->
                <table class="doc-table doc-table-tight">
                    <colgroup>
                        <col style="width: 18%">
                        <col style="width: 72%">
                        <col style="width: 10%">
                    </colgroup>
                    <tbody>

                        <tr class="doc-row">
                            <td class="doc-item">Medical</td>
                            <td class="doc-q">Are all available SDS sheets available inside the medical department?</td>
                            <td class="doc-yes">Yes</td>
                        </tr>

                        <tr class="doc-section">
                            <td colspan="3">Large Sprayer</td>
                        </tr>
                        <tr class="doc-row">
                            <td class="doc-item">Levels</td>
                            <td class="doc-q">Are the equipment levels being accurately detailed in the IPM log?</td>
                            <td class="doc-yes">Yes</td>
                        </tr>
                        <tr class="doc-row">
                            <td class="doc-item">Maintained</td>
                            <td class="doc-q">Is the large sprayer being correctly maintained?</td>
                            <td class="doc-yes">Yes</td>
                        </tr>
                        <tr class="doc-row">
                            <td class="doc-item">Yellow Fan Jet</td>
                            <td class="doc-q">Is there a yellow fan jet tip available to service the sprayer?</td>
                            <td class="doc-yes">Yes</td>
                        </tr>
                        <tr class="doc-row">
                            <td class="doc-item">Pre-Filter</td>
                            <td class="doc-q">Is there a pre-filter for the lance-end available to service the sprayer?</td>
                            <td class="doc-yes">Yes</td>
                        </tr>
                        <tr class="doc-row">
                            <td class="doc-item">Seal Kit</td>
                            <td class="doc-q">Is there a seal kit available for the sprayer?</td>
                            <td class="doc-yes">Yes</td>
                        </tr>
                        <tr class="doc-row">
                            <td class="doc-item">Empty</td>
                            <td class="doc-q">Is the sprayer empty?</td>
                            <td class="doc-yes">Yes</td>
                        </tr>
                        <tr class="doc-row">
                            <td class="doc-item">Decompressed</td>
                            <td class="doc-q">Is the sprayer depressurised?</td>
                            <td class="doc-yes">Yes</td>
                        </tr>

                        <tr class="doc-section">
                            <td colspan="3">Small Sprayer</td>
                        </tr>
                        <tr class="doc-row">
                            <td class="doc-item">Levels</td>
                            <td class="doc-q">Are the equipment levels being accurately detailed in the IPM log?</td>
                            <td class="doc-yes">Yes</td>
                        </tr>
                        <tr class="doc-row">
                            <td class="doc-item">Maintenance</td>
                            <td class="doc-q">Is the small sprayer being correctly maintained?</td>
                            <td class="doc-yes">Yes</td>
                        </tr>
                        <tr class="doc-row">
                            <td class="doc-item">Empty</td>
                            <td class="doc-q">Is the sprayer empty?</td>
                            <td class="doc-yes">Yes</td>
                        </tr>
                        <tr class="doc-row">
                            <td class="doc-item">Decompressed</td>
                            <td class="doc-q">Is the sprayer depressurised?</td>
                            <td class="doc-yes">Yes</td>
                        </tr>

                        <tr class="doc-section">
                            <td colspan="3">ULV Foggers</td>
                        </tr>
                        <tr class="doc-row">
                            <td class="doc-item">Levels</td>
                            <td class="doc-q">Are the equipment levels being accurately detailed in the IPM log?</td>
                            <td class="doc-yes">Yes</td>
                        </tr>
                        <tr class="doc-row">
                            <td class="doc-item">Maintenance</td>
                            <td class="doc-q">Is the fogger being maintained?</td>
                            <td class="doc-yes">Yes</td>
                        </tr>
                        <tr class="doc-row">
                            <td class="doc-item">Empty</td>
                            <td class="doc-q">Is the fogger empty?</td>
                            <td class="doc-yes">Yes</td>
                        </tr>
                        <tr class="doc-row">
                            <td class="doc-item">Base</td>
                            <td class="doc-q">Is the base connected?</td>
                            <td class="doc-yes">Yes</td>
                        </tr>

                        <tr class="doc-section">
                            <td colspan="3">Gel Gun</td>
                        </tr>
                        <tr class="doc-row">
                            <td class="doc-item">Levels</td>
                            <td class="doc-q">Are the equipment levels being accurately detailed in the IPM log?</td>
                            <td class="doc-yes">Yes</td>
                        </tr>
                        <tr class="doc-row">
                            <td class="doc-item">Maintenance</td>
                            <td class="doc-q">Is the gel gun being maintained?</td>
                            <td class="doc-yes">Yes</td>
                        </tr>
                        <tr class="doc-row">
                            <td class="doc-item">Needles</td>
                            <td class="doc-q">Are there sufficient replacement needles for the gel gun?</td>
                            <td class="doc-yes">Yes</td>
                        </tr>

                        <tr class="doc-section">
                            <td colspan="3">Birchmier DR5 Duster</td>
                        </tr>
                        <tr class="doc-row">
                            <td class="doc-item">Maintained</td>
                            <td class="doc-q">Is the compression duster being well-maintained?</td>
                            <td class="doc-yes">Yes</td>
                        </tr>
                        <tr class="doc-row">
                            <td class="doc-item">Wet</td>
                            <td class="doc-q">Is the compression duster dry?</td>
                            <td class="doc-yes">Yes</td>
                        </tr>

                        <tr class="doc-section">
                            <td colspan="3">Electric Duster</td>
                        </tr>
                        <tr class="doc-row">
                            <td class="doc-item">Maintained</td>
                            <td class="doc-q">Is the compression duster being well-maintained?</td>
                            <td class="doc-yes">Yes</td>
                        </tr>
                        <tr class="doc-row">
                            <td class="doc-item">Storage Cap</td>
                            <td class="doc-q">Is the storage cap on the duster?</td>
                            <td class="doc-yes">Yes</td>
                        </tr>

                        <tr class="doc-section">
                            <td colspan="3">Personal Protective Equipment</td>
                        </tr>
                        <tr class="doc-row">
                            <td class="doc-item">Levels</td>
                            <td class="doc-q">Are adequate stock levels of personal protective equipment and filters available?</td>
                            <td class="doc-yes">Yes</td>
                        </tr>
                        <tr class="doc-row">
                            <td class="doc-item">Compliance</td>
                            <td class="doc-q">Is the PPE compliant with the Nutrastat IPM Plan?</td>
                            <td class="doc-yes">Yes</td>
                        </tr>
                        <tr class="doc-row">
                            <td class="doc-item">Maintained</td>
                            <td class="doc-q">Is PPE clean and well-maintained?</td>
                            <td class="doc-yes">Yes</td>
                        </tr>
                        <tr class="doc-row">
                            <td class="doc-item">Separate</td>
                            <td class="doc-q">Is the PPE being stored separately from the chemicals?</td>
                            <td class="doc-yes">Yes</td>
                        </tr>

                        <tr class="doc-section">
                            <td colspan="3">Rodent Traps and Attractant</td>
                        </tr>
                        <tr class="doc-row">
                            <td class="doc-item">Levels: Rat</td>
                            <td class="doc-q">Are adequate stock levels available?</td>
                            <td class="doc-yes">Yes</td>
                        </tr>
                        <tr class="doc-row">
                            <td class="doc-item">Levels: Mouse</td>
                            <td class="doc-q">Are adequate stock levels available?</td>
                            <td class="doc-yes">Yes</td>
                        </tr>
                        <tr class="doc-row">
                            <td class="doc-item">Attractant</td>
                            <td class="doc-q">Is there a tube of rodent attractant in the pest locker, and is it in date?</td>
                            <td class="doc-yes">Yes</td>
                        </tr>

                        <tr class="doc-section">
                            <td colspan="3">Stock Levels</td>
                        </tr>
                        <tr class="doc-row">
                            <td class="doc-item">Chemicals</td>
                            <td class="doc-q">Are reasonable chemical stock levels being maintained as per the IPM Log?</td>
                            <td class="doc-yes">Yes</td>
                        </tr>
                        <tr class="doc-row">
                            <td class="doc-item">Compliance</td>
                            <td class="doc-q">Are there no differing chemicals compared with the IPM Plan?</td>
                            <td class="doc-yes">Yes</td>
                        </tr>
                        <tr class="doc-row">
                            <td class="doc-item">Expired</td>
                            <td class="doc-q">Are all chemicals up-to-date and not expired?</td>
                            <td class="doc-yes">Yes</td>
                        </tr>

                    </tbody>
                </table>

            </div>

            <img class="footer-wedge" src="data:image/png;base64,{{ base64_encode(file_get_contents(public_path('assets/footer.png'))) }}" alt="" />
            <div class="page-no">Page 6 of 12</div>

        </section>

        <section class="page page-inner" aria-label="Chemical transportation page">

            <img class="header" src="data:image/png;base64,{{ base64_encode(file_get_contents(public_path('assets/header.png'))) }}" alt="" />

            <div class="meta-top">
                <div>IPM Audit Report: <b>Viking Mars (VOCX-MARS)</b></div>
                <div>Audit Date: <b>Sat, 7 Jun 2025</b> to <b>Sat, 14 Jun 2025</b></div>
            </div>

            <div class="content-area">

                <!-- Continuation checklist (top of page 7) -->
                <table class="doc-table doc-table-tight doc-table-page7">
                    <colgroup>
                        <col style="width: 18%">
                        <col style="width: 72%">
                        <col style="width: 10%">
                    </colgroup>
                    <tbody>
                        <tr class="doc-row">
                            <td class="doc-item">UV Tubes</td>
                            <td class="doc-q">Are there sufficient Electric Fly Killer Ultraviolet tubes for all fly killers?</td>
                            <td class="doc-yes">Yes</td>
                        </tr>
                        <tr class="doc-row">
                            <td class="doc-item">Glue Boards</td>
                            <td class="doc-q">Are there sufficient Electric Fly Killer replacement glue boards for all fly killers?</td>
                            <td class="doc-yes">Yes</td>
                        </tr>
                        <tr class="doc-row">
                            <td class="doc-item">Silent Sentry</td>
                            <td class="doc-q">Are most Silent Sentry boxes deployed around the departments?</td>
                            <td class="doc-yes">Yes</td>
                        </tr>
                        <tr class="doc-row">
                            <td class="doc-item">Standard Trap</td>
                            <td class="doc-q">Are there sufficient standard cockroach traps?</td>
                            <td class="doc-yes">Yes</td>
                        </tr>
                        <tr class="doc-row">
                            <td class="doc-item">Monitor Trap</td>
                            <td class="doc-q">Are there sufficient monitor cockroach traps?</td>
                            <td class="doc-yes">Yes</td>
                        </tr>
                        <tr class="doc-row">
                            <td class="doc-item">Bedbug Trap</td>
                            <td class="doc-q">Are there sufficient bedbug traps?</td>
                            <td class="doc-yes">Yes</td>
                        </tr>

                        <tr class="doc-section">
                            <td colspan="3">Chemical Transportation</td>
                        </tr>
                        <tr class="doc-row">
                            <td class="doc-item">Transportation</td>
                            <td class="doc-q">Is a portable, coloured container labelled ‘Poison’ available?</td>
                            <td class="doc-yes">Yes</td>
                        </tr>
                    </tbody>
                </table>

                <!-- large blank area in original PDF intentionally left empty -->

            </div>

            <img class="footer-wedge" src="data:image/png;base64,{{ base64_encode(file_get_contents(public_path('assets/footer.png'))) }}" alt="" />
            <div class="page-no">Page 7 of 12</div>

        </section>

        <section class="page page-inner" aria-label="Galley pest observations and traps page">

            <img class="header" src="data:image/png;base64,{{ base64_encode(file_get_contents(public_path('assets/header.png'))) }}" alt="" />

            <div class="meta-top">
                <div>IPM Audit Report: <b>{{ $auditData['ship_name'] ?? 'Viking Mars' }} ({{ $auditData['ship_mnemonic'] ?? 'VOCX-MARS' }})</b></div>
                <div>Audit Date: <b>Sat, 7 Jun 2025</b> to <b>Sat, 14 Jun 2025</b></div>
            </div>

            <div class="content-area">

                <div class="section-title">
                    <span class="section-num">3.</span>
                    <span class="section-text">Galley</span>
                </div>
                <div class="section-rule"></div>

                <div class="subheading">Pest Observations</div>

                <table class="doc-table doc-table-tight doc-table-page8">
                    <colgroup>
                        <col style="width: 18%">
                        <col style="width: 72%">
                        <col style="width: 10%">
                    </colgroup>
                    <thead>
                        <tr>
                            <th class="doc-head" colspan="3">Pest Observations</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="doc-section">
                            <td colspan="3">Pests Observed in the Department</td>
                        </tr>
                        <tr class="doc-row">
                            <td class="doc-item">Cockroaches</td>
                            <td class="doc-q">No cockroaches were seen in this department?</td>
                            <td class="doc-yes">Yes</td>
                        </tr>
                        <tr class="doc-row">
                            <td class="doc-item">Flies</td>
                            <td class="doc-q">No flies were seen in this department?</td>
                            <td class="doc-yes">Yes</td>
                        </tr>
                        <tr class="doc-row">
                            <td class="doc-item">Bedbugs</td>
                            <td class="doc-q">No bedbugs were seen in this department?</td>
                            <td class="doc-yes">Yes</td>
                        </tr>
                        <tr class="doc-row">
                            <td class="doc-item">SPI</td>
                            <td class="doc-q">No Stored Product Insects (SPI) were seen in this department?</td>
                            <td class="doc-yes">Yes</td>
                        </tr>
                        <tr class="doc-row">
                            <td class="doc-item">Rodents</td>
                            <td class="doc-q">No rodents (rats or mice) were seen in this department?</td>
                            <td class="doc-yes">Yes</td>
                        </tr>
                        <tr class="doc-row">
                            <td class="doc-item">Other pests</td>
                            <td class="doc-q">No other pest types were seen in this department?</td>
                            <td class="doc-yes">Yes</td>
                        </tr>
                    </tbody>
                </table>

                <div class="spacer-lg"></div>

                <div class="subheading">Cockroach Trap Monitoring Standards</div>

                <div class="criteria-box criteria-box-tight">
                    <table>
                        <colgroup>
                            <col style="width: 14%">
                            <col style="width: 86%">
                        </colgroup>
                        <tbody>
                            <tr>
                                <td class="criteria-label">Criteria:</td>
                                <td class="criteria-body">
                                    It is a public health requirement to monitor any high-risk area for insect pests using sticky monitoring traps,
                                    although not all departments will have these traps, especially if there is no food present.
                                </td>
                            </tr>
                            <tr>
                                <td class="criteria-label">IPM Plan:</td>
                                <td class="criteria-body criteria-body-tight">XXXXXXXXXXXXXX 40/1/8.1.1.4</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <table class="doc-table doc-table-tight doc-table-page8">
                    <colgroup>
                        <col style="width: 18%">
                        <col style="width: 72%">
                        <col style="width: 10%">
                    </colgroup>
                    <thead>
                        <tr>
                            <th class="doc-head" colspan="3">Cockroach Traps</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="doc-section">
                            <td colspan="3">Standard and Monitor Traps</td>
                        </tr>
                        <tr class="doc-row">
                            <td class="doc-item">Assembly</td>
                            <td class="doc-q">The traps assembled correctly?</td>
                            <td class="doc-yes">Yes</td>
                        </tr>
                        <tr class="doc-row">
                            <td class="doc-item">Ramps</td>
                            <td class="doc-q">The ramps folded in and were not pulled out?</td>
                            <td class="doc-yes">Yes</td>
                        </tr>
                        <tr class="doc-row">
                            <td class="doc-item">Location</td>
                            <td class="doc-q">Are the monitoring traps correctly placed according to the IPM Log?</td>
                            <td class="doc-yes">Yes</td>
                        </tr>
                        <tr class="doc-row">
                            <td class="doc-item">Type</td>
                            <td class="doc-q">Are the correct type of traps used (i.e. standard or monitor) as per the IPM Log?</td>
                            <td class="doc-yes">Yes</td>
                        </tr>
                        <tr class="doc-row">
                            <td class="doc-item">Silicon Paper</td>
                            <td class="doc-q">Is the silicon release paper on the traps being removed?</td>
                            <td class="doc-yes">Yes</td>
                        </tr>
                        <tr class="doc-row">
                            <td class="doc-item">Condition</td>
                            <td class="doc-q">Are the traps in good condition, not wet, not soiled, and not crushed?</td>
                            <td class="doc-yes">Yes</td>
                        </tr>
                        <tr class="doc-row">
                            <td class="doc-item">Deck</td>
                            <td class="doc-q">Are the traps correctly placed on the deck?</td>
                            <td class="doc-yes">Yes</td>
                        </tr>
                        <tr class="doc-row">
                            <td class="doc-item">Washdown</td>
                            <td class="doc-q">Are they being lifted before washdown, and the traps remaining dry?</td>
                            <td class="doc-yes">Yes</td>
                        </tr>
                        <tr class="doc-row">
                            <td class="doc-item">Vertical</td>
                            <td class="doc-q">No traps were positioned vertically?</td>
                            <td class="doc-yes">Yes</td>
                        </tr>
                        <tr class="doc-row">
                            <td class="doc-item">Equipment</td>
                            <td class="doc-q">No traps were positioned inside equipment, such as the cleaning locker?</td>
                            <td class="doc-yes">Yes</td>
                        </tr>
                        <tr class="doc-row">
                            <td class="doc-item">Reusing Traps</td>
                            <td class="doc-q">Not using old traps</td>
                            <td class="doc-yes">Yes</td>
                        </tr>
                        <tr class="doc-row">
                            <td class="doc-item">Nutrasat Traps</td>
                            <td class="doc-q">All traps are Nutrasat branded, not any other types?</td>
                            <td class="doc-yes">Yes</td>
                        </tr>

                        <tr class="doc-section">
                            <td colspan="3">Silent Sentries</td>
                        </tr>
                        <tr class="doc-row">
                            <td class="doc-item">Labels</td>
                            <td class="doc-q">Are the traps correctly labelled?</td>
                            <td class="doc-yes">Yes</td>
                        </tr>
                        <tr class="doc-row">
                            <td class="doc-item">Clean</td>
                            <td class="doc-q">Are they clean and well-maintained?</td>
                            <td class="doc-yes">Yes</td>
                        </tr>
                        <tr class="doc-row">
                            <td class="doc-item">Damaged</td>
                            <td class="doc-q">Are they all undamaged?</td>
                            <td class="doc-yes">Yes</td>
                        </tr>
                        <tr class="doc-row">
                            <td class="doc-item">Trap Type</td>
                            <td class="doc-q">Are standard monitoring traps always being used in the Silent Sentry?</td>
                            <td class="doc-yes">Yes</td>
                        </tr>
                        <tr class="doc-row">
                            <td class="doc-item">Plastic Film</td>
                            <td class="doc-q">Has all the manufacturer’s plastic film wrap been removed from all Silent Sentries?</td>
                            <td class="doc-yes">Yes</td>
                        </tr>
                        <tr class="doc-row">
                            <td class="doc-item">Base Plate</td>
                            <td class="doc-q">Are all internal base plates correctly oriented?</td>
                            <td class="doc-yes">Yes</td>
                        </tr>
                        <tr class="doc-row">
                            <td class="doc-item">Soiled/Dusty</td>
                            <td class="doc-q">Were there no soiled or dusty traps?</td>
                            <td class="doc-yes">Yes</td>
                        </tr>
                        <tr class="doc-row">
                            <td class="doc-item">Hinge Pins</td>
                            <td class="doc-q">The baseplates were not on the hinge pins</td>
                            <td class="doc-yes">Yes</td>
                        </tr>

                        <tr class="doc-section">
                            <td colspan="3">Inspections</td>
                        </tr>
                        <tr class="doc-row">
                            <td class="doc-item">Weekly Inspect</td>
                            <td class="doc-q">Were all traps inspected weekly as per the current IPM Log?</td>
                            <td class="doc-yes">Yes</td>
                        </tr>
                    </tbody>
                </table>

            </div>

            <img class="footer-wedge" src="data:image/png;base64,{{ base64_encode(file_get_contents(public_path('assets/footer.png'))) }}" alt="" />
            <div class="page-no">Page 8 of 12</div>

        </section>

        <section class="page page-inner" aria-label="Non-compliance and electric fly killers page">

            <img class="header" src="data:image/png;base64,{{ base64_encode(file_get_contents(public_path('assets/header.png'))) }}" alt="" />

            <div class="meta-top">
                <div>IPM Audit Report: <b>Viking Mars (VOCX-MARS)</b></div>
                <div>Audit Date: <b>Sat, 7 Jun 2025</b> to <b>Sat, 14 Jun 2025</b></div>
            </div>

            <div class="content-area">

                <!-- Continuation from previous section -->
                <table class="doc-table doc-table-tight doc-table-page9-top">
                    <colgroup>
                        <col style="width: 18%">
                        <col style="width: 72%">
                        <col style="width: 10%">
                    </colgroup>
                    <tbody>
                        <tr class="doc-row">
                            <td class="doc-item">IPM Period Start</td>
                            <td class="doc-q">It is not the start of the IPM Period</td>
                            <td class="doc-yes">Yes</td>
                        </tr>
                        <tr class="doc-row">
                            <td class="doc-item">Inspect Dates</td>
                            <td class="doc-q">The correct Inspection dates were detailed correctly, following the IPM Log?</td>
                            <td class="doc-yes">Yes</td>
                        </tr>
                        <tr class="doc-row">
                            <td class="doc-item">Missing Traps</td>
                            <td class="doc-q">Were there no traps that were missing?</td>
                            <td class="doc-yes">Yes</td>
                        </tr>
                        <tr class="doc-row">
                            <td class="doc-item">Placement Date</td>
                            <td class="doc-q">Were the placement dates correct and detailed</td>
                            <td class="doc-yes">Yes</td>
                        </tr>
                        <tr class="doc-row">
                            <td class="doc-item">Month 3-Chars</td>
                            <td class="doc-q">Were the months written in 3-character format (i.e. Apr, May, Dec)?</td>
                            <td class="doc-yes">Yes</td>
                        </tr>
                        <tr class="doc-row">
                            <td class="doc-item">Accountability</td>
                            <td class="doc-q">Is the person’s initials/signature being applied during inspection?</td>
                            <td class="doc-yes">Yes</td>
                        </tr>
                        <tr class="doc-row">
                            <td class="doc-item">Weekly Count</td>
                            <td class="doc-q">The traps are inspected weekly and completed with Adults, Nymphs, and Other?</td>
                            <td class="doc-yes">Yes</td>
                        </tr>
                        <tr class="doc-row">
                            <td class="doc-item">Post-Dated</td>
                            <td class="doc-q">There were no post-dated traps?</td>
                            <td class="doc-yes">Yes</td>
                        </tr>
                    </tbody>
                </table>

                <div class="spacer-lg"></div>

                <div class="noncompliance-title">Non-Compliance: Cockroach Trap Monitoring Standards</div>

                <div class="subheading">Electric Fly Killers</div>

                <div class="criteria-box criteria-box-tight">
                    <table>
                        <colgroup>
                            <col style="width: 14%">
                            <col style="width: 86%">
                        </colgroup>
                        <tbody>
                            <tr>
                                <td class="criteria-label">Criteria:</td>
                                <td class="criteria-body">
                                    It is a public health requirement that electric fly killers installed in a department are correctly serviced &amp; maintained.
                                    With weekly fly count, UV tubes and glue boards replaced on the correct schedule as per the IPM Plan.
                                </td>
                            </tr>
                            <tr>
                                <td class="criteria-label">IPM Plan:</td>
                                <td class="criteria-body criteria-body-tight">XXXXX</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="future-box">
                    <div class="future-left">
                        Electric Fly<br />
                        Killer<br />
                        Future<br />
                        Changes:
                    </div>
                    <div class="future-body">
                        <p>
                            In 2017, the electrical industry mandated the phase-out of Phosphor UV lamps by 2027, requiring all consumers to use
                            Light Emitting Diode (LED) lamps instead. Phosphor Ultraviolet (UV) lamp production will cease as follows:
                        </p>

                        <div class="future-timeline">
                            <div class="future-bullets">
                                <div class="dot"></div>
                                <div>Single-ended Phosphor</div>
                                <div class="dot"></div>
                                <div>Double-ended Phosphor</div>
                            </div>
                            <div class="future-dates">
                                <div>February 2025 (Already ceased production)</div>
                                <div>February 2027</div>
                            </div>
                        </div>

                        <p class="future-note">
                            Although there may be stockpiling of the UV tubes, consider starting to replace all the current fluorescent models,
                            consider replacing them with the following benefits:
                        </p>

                        <ul class="future-list">
                            <li>LED UV lamps, although appearing dimmer, are, on average, 20% more attractive to flies.</li>
                            <li>Average phosphor UV lamps go from 15 watts to 4 watts LED, about 70% power saving.</li>
                            <li>LED UV lamps are changed every two years rather than six months; this is a significant cost and time-saving for the electrician.</li>
                            <li>The ballast is in the UV tubes rather than the fly killer. Every UV lamp change, you effectively get a new Electric Fly Killer.</li>
                        </ul>
                    </div>
                    <div class="future-bottom">
                        <div class="criteria-label">IPM Plan:</div>
                        <div class="criteria-body criteria-body-tight">XXXXX</div>
                    </div>
                </div>

                <table class="doc-table doc-table-tight">
                    <colgroup>
                        <col style="width: 18%">
                        <col style="width: 72%">
                        <col style="width: 10%">
                    </colgroup>
                    <thead>
                        <tr>
                            <th class="doc-head" colspan="3">Electric Fly Killers</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="doc-section">
                            <td colspan="3">Fly Killer Quantity</td>
                        </tr>
                        <tr class="doc-row">
                            <td class="doc-item">Quantity</td>
                            <td class="doc-q">Are there sufficient fly killers in the area to prevent flying insect issues?</td>
                            <td class="doc-yes">Yes</td>
                        </tr>

                        <tr class="doc-section">
                            <td colspan="3">Zapper Fly Killers</td>
                        </tr>
                        <tr class="doc-row">
                            <td class="doc-item">Type</td>
                            <td class="doc-q">There are no zapper-type fly killers installed on the ship.</td>
                            <td class="doc-yes">Yes</td>
                        </tr>
                        <tr class="doc-row">
                            <td class="doc-item">Food Area</td>
                            <td class="doc-q">Are there no zapper-type fly killers installed in food areas?</td>
                            <td class="doc-yes">Yes</td>
                        </tr>
                        <tr class="doc-row">
                            <td class="doc-item">Substitute</td>
                            <td class="doc-q">Are all zappers acceptable?</td>
                            <td class="doc-yes">Yes</td>
                        </tr>

                        <tr class="doc-section">
                            <td colspan="3">Power and Wiring</td>
                        </tr>
                        <tr class="doc-row">
                            <td class="doc-item">Power</td>
                            <td class="doc-q">Are all fly killers always powered and working?</td>
                            <td class="doc-yes">Yes</td>
                        </tr>
                        <tr class="doc-row">
                            <td class="doc-item">Permanent</td>
                            <td class="doc-q">Are all fly killers not working from the electric light switch circuit?</td>
                            <td class="doc-yes">Yes</td>
                        </tr>
                        <tr class="doc-row">
                            <td class="doc-item">Wiring</td>
                            <td class="doc-q">Are the wires powering the fly killer correctly installed and in conduit?</td>
                            <td class="doc-yes">Yes</td>
                        </tr>

                        <tr class="doc-section">
                            <td colspan="3">Clean and Well Maintained</td>
                        </tr>
                        <tr class="doc-row">
                            <td class="doc-item">Clean</td>
                            <td class="doc-q">Are the fly killers clean?</td>
                            <td class="doc-yes">Yes</td>
                        </tr>
                        <tr class="doc-row">
                            <td class="doc-item">Damaged</td>
                            <td class="doc-q">Are all fly killers in good condition and not damaged?</td>
                            <td class="doc-yes">Yes</td>
                        </tr>
                        <tr class="doc-row">
                            <td class="doc-item">Missing Parts</td>
                            <td class="doc-q">Are there any missing parts on the fly killers?</td>
                            <td class="doc-yes">Yes</td>
                        </tr>
                    </tbody>
                </table>

            </div>

            <img class="footer-wedge" src="data:image/png;base64,{{ base64_encode(file_get_contents(public_path('assets/footer.png'))) }}" alt="" />
            <div class="page-no">Page 9 of 12</div>

        </section>

        <section class="page page-inner" aria-label="Electric Fly Killers maintenance continuation">

            <img class="header" src="data:image/png;base64,{{ base64_encode(file_get_contents(public_path('assets/header.png'))) }}" alt="" />

            <div class="meta-top">
                <div>IPM Audit Report: <b>Viking Mars (VOCX-MARS)</b></div>
                <div>Audit Date: <b>Sat, 7 Jun 2025</b> to <b>Sat, 14 Jun 2025</b></div>
            </div>

            <div class="content-area">

                <!-- Continuation of "Electric Fly Killers" checklist -->
                <table class="doc-table doc-table-tight doc-table-page10">
                    <colgroup>
                        <col style="width: 18%">
                        <col style="width: 72%">
                        <col style="width: 10%">
                    </colgroup>
                    <tbody>

                        <tr class="doc-subsection">
                            <td colspan="3">UV Tube Maintenance</td>
                        </tr>
                        <tr class="doc-row">
                            <td class="doc-item">Changed</td>
                            <td class="doc-q">Are all UV tubes being changed as per the IPM Plan?</td>
                            <td class="doc-yes">Yes</td>
                        </tr>
                        <tr class="doc-row">
                            <td class="doc-item">Working</td>
                            <td class="doc-q">Are all UV Tubes working when the power is on?</td>
                            <td class="doc-yes">Yes</td>
                        </tr>
                        <tr class="doc-row">
                            <td class="doc-item">Shatterproof</td>
                            <td class="doc-q">Are the UV tubes shatter-resistant, covered in special plastic?</td>
                            <td class="doc-yes">Yes</td>
                        </tr>
                        <tr class="doc-row">
                            <td class="doc-item">LED Orientation</td>
                            <td class="doc-q">Are the UV tubes installed such that they point out to maximise attraction?</td>
                            <td class="doc-yes">Yes</td>
                        </tr>

                        <tr class="doc-subsection">
                            <td colspan="3">Glue Board Maintenance</td>
                        </tr>
                        <tr class="doc-row">
                            <td class="doc-item">Changed</td>
                            <td class="doc-q">Are all the glue boards being changed as per the IPM Plan?</td>
                            <td class="doc-yes">Yes</td>
                        </tr>
                        <tr class="doc-row">
                            <td class="doc-item">Type</td>
                            <td class="doc-q">Are the glue boards of the correct type for each fly killer?</td>
                            <td class="doc-yes">Yes</td>
                        </tr>
                        <tr class="doc-row">
                            <td class="doc-item">Expiry Date</td>
                            <td class="doc-q">Is the next expiry date being written on the back of the glue boards?</td>
                            <td class="doc-yes">Yes</td>
                        </tr>
                        <tr class="doc-row">
                            <td class="doc-item">Missing</td>
                            <td class="doc-q">Do all fly killers have glue boards installed?</td>
                            <td class="doc-yes">Yes</td>
                        </tr>

                        <tr class="doc-subsection">
                            <td colspan="3">Maintenance Labels</td>
                        </tr>
                        <tr class="doc-row">
                            <td class="doc-item">Labels</td>
                            <td class="doc-q">Are the maintenance labels in place on each fly killer?</td>
                            <td class="doc-yes">Yes</td>
                        </tr>
                        <tr class="doc-row">
                            <td class="doc-item">Completed</td>
                            <td class="doc-q">Are the labels being completed as per the IPM Plan?</td>
                            <td class="doc-yes">Yes</td>
                        </tr>
                        <tr class="doc-row">
                            <td class="doc-item">Future Dates</td>
                            <td class="doc-q">Are the maintenance labels correctly completed with no futuristic dates?</td>
                            <td class="doc-yes">Yes</td>
                        </tr>

                        <tr class="doc-subsection">
                            <td colspan="3">Correct Location</td>
                        </tr>
                        <tr class="doc-row">
                            <td class="doc-item">Food</td>
                            <td class="doc-q">Not located above food?</td>
                            <td class="doc-yes">Yes</td>
                        </tr>
                        <tr class="doc-row">
                            <td class="doc-item">Equipment</td>
                            <td class="doc-q">Not located above clean equipment, utensils, serviettes, and single-use items?</td>
                            <td class="doc-yes">Yes</td>
                        </tr>
                        <tr class="doc-row">
                            <td class="doc-item">Clean Surface</td>
                            <td class="doc-q">Not located over food or clean surfaces within 150mm of either side of the fly killers?</td>
                            <td class="doc-yes">Yes</td>
                        </tr>
                        <tr class="doc-row">
                            <td class="doc-item">Siting</td>
                            <td class="doc-q">Are all fly killers in the correct siting location as required by Public Health?</td>
                            <td class="doc-yes">Yes</td>
                        </tr>

                    </tbody>
                </table>

                <!-- Remaining page is intentionally blank (matches PDF) -->

            </div>

            <img class="footer-wedge" src="data:image/png;base64,{{ base64_encode(file_get_contents(public_path('assets/footer.png'))) }}" alt="" />
            <div class="page-no">Page 10 of 12</div>

        </section>

        <section class="page page-inner" aria-label="Deck Department and Rat Guards">

            <img class="header" src="data:image/png;base64,{{ base64_encode(file_get_contents(public_path('assets/header.png'))) }}" alt="" />

            <div class="meta-top">
                <div>IPM Audit Report: <b>Viking Mars (VOCX-MARS)</b></div>
                <div>Audit Date: <b>Sat, 7 Jun 2025</b> to <b>Sat, 14 Jun 2025</b></div>
            </div>

            <div class="content-area">

                <div class="section-title">
                    <span class="section-num">4.</span>
                    <span class="section-text">Deck Department</span>
                </div>
                <div class="section-rule"></div>

                <div class="section-lead">The Integrated Pest Management observations for this department.</div>

                <table class="doc-table doc-table-tight">
                    <colgroup>
                        <col style="width: 18%">
                        <col style="width: 72%">
                        <col style="width: 10%">
                    </colgroup>
                    <thead>
                        <tr>
                            <th class="doc-head" colspan="3">Pest Observations</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="doc-section">
                            <td colspan="3">Pests Observed in the Department</td>
                        </tr>
                        <tr class="doc-row">
                            <td class="doc-item">Cockroaches</td>
                            <td class="doc-q">Were there no cockroaches seen in this department?</td>
                            <td class="doc-yes">Yes</td>
                        </tr>
                        <tr class="doc-row">
                            <td class="doc-item">Flies</td>
                            <td class="doc-q">Were there no flies seen in this department?</td>
                            <td class="doc-yes">Yes</td>
                        </tr>
                        <tr class="doc-row">
                            <td class="doc-item">Bedbugs</td>
                            <td class="doc-q">Were there no bedbugs seen in this department?</td>
                            <td class="doc-yes">Yes</td>
                        </tr>
                        <tr class="doc-row">
                            <td class="doc-item">SPI</td>
                            <td class="doc-q">Were there no Stored Product Insects (SPI) seen in this department?</td>
                            <td class="doc-yes">Yes</td>
                        </tr>
                        <tr class="doc-row">
                            <td class="doc-item">Rodents</td>
                            <td class="doc-q">Were there no rodents (rats or mice) seen in this department?</td>
                            <td class="doc-yes">Yes</td>
                        </tr>
                        <tr class="doc-row">
                            <td class="doc-item">Other pests</td>
                            <td class="doc-q">Were there no other pest types seen in this department?</td>
                            <td class="doc-yes">Yes</td>
                        </tr>
                    </tbody>
                </table>

                <div class="spacer-lg"></div>

                <div class="subheading">Rat Guards</div>
                <div class="italic-title">Rat Guard Criteria</div>

                <div class="criteria-box criteria-box-tight">
                    <table>
                        <colgroup>
                            <col style="width: 14%">
                            <col style="width: 86%">
                        </colgroup>
                        <tbody>
                            <tr>
                                <td class="criteria-label">Criteria:</td>
                                <td class="criteria-body">
                                    Exemplary installation of correctly sized rat guards on forward, aft, and bunker lines is essential, as if a rodent (rat or mouse)
                                    comes on board, this immediately compromises the Ship Sanitation Control Exemption Certificate (formerly known as a De-Rat Certificate).
                                    Public Health Authorities require being informed about such infestations, and in some countries, ships may be fined if they do not protect
                                    all mooring and spring lines with rat guards.
                                </td>
                            </tr>
                            <tr>
                                <td class="criteria-label">IPM Plan:</td>
                                <td class="criteria-body criteria-body-tight">XXXXXXXXXXXXXX</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="note-box">
                    <div class="note-head">Rat Guards Not Inspected</div>
                    <div class="note-body">
                        During this audit, it was not possible to inspect the mooring and spring lines forward and aft.
                    </div>
                    <div class="note-comment">
                        <div class="note-comment-left">Consultant Comment:</div>
                        <div class="note-comment-right">No further comment.</div>
                    </div>
                </div>

                <table class="doc-table doc-table-tight">
                    <colgroup>
                        <col style="width: 18%">
                        <col style="width: 72%">
                        <col style="width: 10%">
                    </colgroup>
                    <thead>
                        <tr>
                            <th class="doc-head" colspan="3">Mooring Lines FWD</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="doc-section">
                            <td colspan="3">Construction</td>
                        </tr>
                        <tr class="doc-row">
                            <td class="doc-item">Construction</td>
                            <td class="doc-q">Do all rat guards meet the minimum construction requirements?</td>
                            <td class="doc-no">No</td>
                        </tr>

                        <tr class="doc-section">
                            <td colspan="3">Number Per Line</td>
                        </tr>
                        <tr class="doc-row">
                            <td class="doc-item">Count</td>
                            <td class="doc-q">Is there a minimum of one rat guard installed per line?</td>
                            <td class="doc-yes">Yes</td>
                        </tr>

                        <tr class="doc-section">
                            <td colspan="3">Installation: Timing</td>
                        </tr>
                        <tr class="doc-row">
                            <td class="doc-item">Arrival</td>
                            <td class="doc-q">All rat guards installed within an hour of arrival?</td>
                            <td class="doc-yes">Yes</td>
                        </tr>
                        <tr class="doc-row">
                            <td class="doc-item">Departure</td>
                            <td class="doc-q">All rat guards not removed earlier than an hour before departure?</td>
                            <td class="doc-yes">Yes</td>
                        </tr>

                        <tr class="doc-section">
                            <td colspan="3">Installation: Dockside</td>
                        </tr>
                        <tr class="doc-row">
                            <td class="doc-item">Gaps</td>
                            <td class="doc-q">All rat guards placed with no gaps to allow rodent ingress?</td>
                            <td class="doc-yes">Yes</td>
                        </tr>
                        <tr class="doc-row">
                            <td class="doc-item">Distance</td>
                            <td class="doc-q">Rat guards not placed within 2 metres of the dockside, considering personnel safety?</td>
                            <td class="doc-yes">Yes</td>
                        </tr>
                        <tr class="doc-row">
                            <td class="doc-item">Angled</td>
                            <td class="doc-q">Rat guards not placed at too shallow an angle?</td>
                            <td class="doc-yes">Yes</td>
                        </tr>
                        <tr class="doc-row">
                            <td class="doc-item">Hanging</td>
                            <td class="doc-q">Are all rat guards securely tied and not hanging?</td>
                            <td class="doc-yes">Yes</td>
                        </tr>
                        <tr class="doc-row">
                            <td class="doc-item">Orientation</td>
                            <td class="doc-q">Are all the oriented rat guards not deployed in the correct direction?</td>
                            <td class="doc-yes">Yes</td>
                        </tr>
                        <tr class="doc-row">
                            <td class="doc-item">Ground Height</td>
                            <td class="doc-q">Are all rat guards not installed too low on the deck?</td>
                            <td class="doc-yes">Yes</td>
                        </tr>
                        <tr class="doc-row">
                            <td class="doc-item">Securing Line</td>
                            <td class="doc-q">Are all rat guards securing lines installed at the back of the rat guard?</td>
                            <td class="doc-yes">Yes</td>
                        </tr>
                    </tbody>
                </table>

            </div>

            <img class="footer-wedge" src="data:image/png;base64,{{ base64_encode(file_get_contents(public_path('assets/footer.png'))) }}" alt="" />
            <div class="page-no">Page 11 of 12</div>

        </section>

        <section class="page page-inner" aria-label="Rat Guards - Non Compliance Construction">

            <img class="header" src="data:image/png;base64,{{ base64_encode(file_get_contents(public_path('assets/header.png'))) }}" alt="" />

            <div class="meta-top">
                <div>IPM Audit Report: <b>Viking Mars (VOCX-MARS)</b></div>
                <div>Audit Date: <b>Sat, 7 Jun 2025</b> to <b>Sat, 14 Jun 2025</b></div>
            </div>

            <div class="content-area">

                <!-- Continuation table -->
                <table class="doc-table doc-table-tight doc-table-page12-top">
                    <colgroup>
                        <col style="width: 18%">
                        <col style="width: 72%">
                        <col style="width: 10%">
                    </colgroup>
                    <tbody>
                        <tr class="doc-subsection">
                            <td colspan="3">Installation: Shipside</td>
                        </tr>
                        <tr class="doc-row">
                            <td class="doc-item">Weighted</td>
                            <td class="doc-q">Are all shipside rat guards weighted as per the IPM Plan?</td>
                            <td class="doc-yes">Yes</td>
                        </tr>
                        <tr class="doc-row">
                            <td class="doc-item">Distance</td>
                            <td class="doc-q">Are all shipside rat guards not placed too close to the ship?</td>
                            <td class="doc-yes">Yes</td>
                        </tr>
                        <tr class="doc-row">
                            <td class="doc-item">Openings</td>
                            <td class="doc-q">Are all shipside rat guards installed before the ship rope openings?</td>
                            <td class="doc-yes">Yes</td>
                        </tr>
                    </tbody>
                </table>

                <div class="spacer-lg"></div>

                <!-- Non-Compliance: Construction -->
                <div class="nc-box">
                    <div class="nc-head">Non-Compliance: Construction</div>

                    <div class="nc-desc">
                        Rat guards have been installed that do not meet the minimum height or width construction requirements, which are:
                        <ul>
                            <li>200mm high from the top of the mooring line</li>
                            <li>200 mm wide on either side of the mooring line</li>
                        </ul>
                    </div>

                    <div class="nc-rows">
                        <div class="nc-lab">Remedial Action:</div>
                        <div class="nc-val">The Pest Control Responsible Officer, Pest Control Officer and Bosun should address this failure.</div>

                        <div class="nc-lab">Consultant Comment:</div>
                        <div class="nc-val">No further comment.</div>

                        <div class="nc-lab">USPH/VSP Reference:</div>
                        <div class="nc-val">XXXXXXXXXX</div>

                        <div class="nc-lab">IPM Plan Reference:</div>
                        <div class="nc-val">XXXXXXXXXXXXXXX</div>
                    </div>

                    <!-- Picture heading grid -->
                    <div class="pic-grid">
                        <div class="pic-row">
                            <div class="pic-cell">
                                <div class="pic-head">APictureHeading</div>
                                <div class="pic-body"></div>
                            </div>
                            <div class="pic-cell">
                                <div class="pic-head">APictureHeading</div>
                                <div class="pic-body"></div>
                            </div>
                        </div>

                        <div class="pic-row pic-row-2">
                            <div class="pic-cell">
                                <div class="pic-head">APictureHeading</div>
                                <div class="pic-body"></div>
                            </div>
                            <div class="pic-cell">
                                <div class="pic-head">APictureHeading</div>
                                <div class="pic-body"></div>
                            </div>
                        </div>

                        <div class="pic-row pic-row-3">
                            <div class="pic-cell">
                                <div class="pic-head">APictureHeading</div>
                                <div class="pic-body"></div>
                            </div>
                            <div class="pic-cell">
                                <div class="pic-head">APictureHeading</div>
                                <div class="pic-body pic-body-wide"></div>
                            </div>
                            <div class="pic-cell">
                                <div class="pic-head">APictureHeading</div>
                                <div class="pic-body"></div>
                            </div>
                        </div>

                        <div class="pic-row pic-row-4">
                            <div class="pic-cell">
                                <div class="pic-head">APictureHeading</div>
                                <div class="pic-body"></div>
                            </div>
                            <div class="pic-cell">
                                <div class="pic-head">APictureHeading</div>
                                <div class="pic-body"></div>
                            </div>
                            <div class="pic-cell">
                                <div class="pic-head">APictureHeading</div>
                                <div class="pic-body"></div>
                            </div>
                        </div>

                        <div class="pic-row pic-row-5">
                            <div class="pic-cell pic-cell-full">
                                <div class="pic-head">APictureHeading</div>
                            </div>
                        </div>
                    </div>

                </div>

            </div>

            <img class="footer-wedge" src="data:image/png;base64,{{ base64_encode(file_get_contents(public_path('assets/footer.png'))) }}" alt="" />
            <div class="page-no">Page 12 of 12</div>

        </section>
    </main>
</body>

</html>