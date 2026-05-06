<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <title>Integrated Pest Management Audit Report</title>

   

    <style>
        {!! file_get_contents(public_path('css/pdf-style.css')) !!}

        body {
            font-family: Arial, Helvetica, sans-serif;
        }
    </style>
</head>
<body>
    <main class="stage">
        <section class="page" aria-label="Cover page">
     <img class="header"
     src="data:image/png;base64,{{ base64_encode(file_get_contents(public_path('assets/header.png'))) }}" />
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
            <img class="footer-wedge"
     src="data:image/png;base64,{{ base64_encode(file_get_contents(public_path('assets/footer.png'))) }}" />
        </section>

        <section class="page page-inner" aria-label="Inner page template">
     <img class="header"
     src="data:image/png;base64,{{ base64_encode(file_get_contents(public_path('assets/header.png'))) }}" />

            <div class="meta-top">
                <div>IPM Audit Report: <b>Viking Mars (VOCX-MARS)</b></div>
                <div>Audit Date: <b>Sat, 7 Jun 2025</b> to <b>Sat, 14 Jun 2025</b></div>
            </div>

            <div class="content-area">
                <!-- Put your page content here -->
            </div>

            <img class="footer-wedge"
     src="data:image/png;base64,{{ base64_encode(file_get_contents(public_path('assets/footer.png'))) }}" />
            <div class="page-no">Page 2 of 12</div>
        </section>

    </main>
</body>

</html>