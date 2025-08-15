{{-- resources/views/reports/vessel-sanitation-inspection.blade.php --}}
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Vessel Sanitation Inspection Report (Static Mockup)</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <style>
    :root{
      --ink:#000;
      --bg:#fff;
      --soft:#f6f6f6;
      --line:#000;
      --warn:#fff1a8; /* subtle yellow like the mock */
    }
    *{box-sizing:border-box}
    html,body{margin:0;padding:0;background:#fafafa;color:var(--ink);}

    /* Page frame */
    .report{
      width: 100%;
      max-width: 1120px;      /* tuned for A4/Letter scale */
      margin: 20px auto;
      background: var(--bg);
      border: 2px solid var(--line);
    }

    .legend-bar{display:flex;justify-content:space-between;align-items:center}

    /* Top black title bar */
    .title-bar{
      height: 48px;
      background: #000;
      color:#fff;
      display:flex;
      align-items:center;
      justify-content:center;
      font-weight:700;
      letter-spacing:.3px;
      border-bottom:2px solid var(--line);
    }

    /* Header band below title: logo + meta grid + type/score stub alignment */
    .header-band{
      display:grid;
      grid-template-columns: 156px 1fr 48px; /* logo | meta | score rail width */
      border-bottom:2px solid var(--line);
      min-height:130px;
    }

    /* Left: faux circular logo (no image) */
    .logo-wrap{
      border-right:2px solid var(--line);
      display:flex; align-items:center; justify-content:center;
      padding:10px;
    }
    .logo{
      width:118px; height:118px;
      border:2px solid var(--line);
      border-radius:50%;
      position:relative;
      display:grid; place-items:center;
      font-size:12px; text-align:center;
      line-height:1.1;
    }
    .logo:before{
      content:""; position:absolute; inset:12px;
      border:1px solid var(--line); border-radius:50%;
    }
    .logo .seal{
      font-weight:700; font-size:13px; letter-spacing:.2px;
    }
    .logo .dept{
      margin-top:4px; font-size:11px;
    }

    /* Middle: meta fields grid */
    .meta{
      display:grid;
      grid-template-columns: 1fr 1fr 1fr 1fr;
      grid-auto-rows: minmax(30px,auto);
      column-gap:12px; row-gap:8px;
      padding:10px 12px;
      border-right:2px solid var(--line);
    }
    .meta .field{
      display:grid;
      grid-template-rows:auto 26px;
    }
    .meta .label{
      font-weight:700; font-size:13px;
    }
    .meta .val{
      border-bottom:1.5px solid var(--line);
      padding:2px 6px; font-size:13px;
    }
    /* make Port span full width like the mock */
    .field.port{ grid-column: 1 / -1; }

    /* Right stub cell aligns with future score rail */
    .score-stub{
      display:flex; align-items:center; justify-content:center;
      font-weight:700; transform:rotate(180deg);
      writing-mode:vertical-rl;
      letter-spacing:.8px;
    }

    /* Body grid: two main panes + score rail */
    .body{
      display:grid;
      grid-template-columns: 1fr 1fr 48px;
      min-height: 1120px;
    }
    .pane{
      border-right:2px solid var(--line);
    }
    .pane:last-child{ border-right:0; }

    /* Column header (legend) row present on BOTH panes */
    .legend{
      display:grid;
      grid-template-columns: 88px 96px 1fr; /* Item / Point / Description */
      border-bottom:2px solid var(--line);
      background:#f2f2f2;
      font-weight:700;
      font-size:14px;
    }
    .legend > div{
      padding:8px 10px; border-right:2px solid var(--line);
    }
    .legend > div:last-child{ border-right:0; }

    .critical-note{
      height: 24px;
      border-bottom:2px solid var(--line);
      padding:6px 10px;
      font-size:13px;
    }

    /* Section title bars */
    .section-title{
      color: #000;
      font-weight: bold;
      /* background:#000; color:#fff; font-weight:700; */
      padding:6px 10px; border-top:2px solid var(--line);
      border-bottom:2px solid var(--line);
      text-transform:uppercase; font-size:13px;
      letter-spacing:.2px;
    }

    /* Rows (table-like without <table> to fine-tune borders) */
    .row{
      display:grid;
      grid-template-columns: 88px 96px 1fr;
      border-bottom:1.5px solid var(--line);
      font-size:13px;
    }
    .cell{
      padding:6px 10px; border-right:2px solid var(--line);
      align-self:stretch;
    }
    .cell:last-child{ border-right:0; }
    .item.critical:after{ content:"*"; font-weight:700; }

    .highlight{ background: var(--warn); }

    /* Score rail */
    .score-rail{
      border-left:2px solid var(--line);
      display:flex; align-items:center; justify-content:center;
      position:relative;
    }
    .score-rail .text{
      writing-mode:vertical-rl; transform:rotate(180deg);
      font-weight:700; letter-spacing:1px;
    }

    /* Print tweaks */
    @media print{
      body{ background:#fff; }
      .report{ margin:0; border:2px solid var(--line); }
      @page{ margin:8mm; }
    }
  </style>
</head>
<body>
  <div class="report">
    <!-- Title -->
    <div class="title-bar">VESSEL SANITATION INSPECTION REPORT</div>

    <!-- Header band: Logo | Meta grid | Score stub -->
    <div class="header-band">
      <div class="logo-wrap">
        <div class="logo" aria-label="Agency seal (mock)">
          <div class="seal">EAVICES</div>
          <div class="dept">Public Health<br>Service</div>
        </div>
      </div>

      <div class="meta">
        <div class="field">
          <div class="label">Vessel:</div>
          <div class="val"><span></span></div>
        </div>
        <div class="field">
          <div class="label">Date:</div>
          <div class="val"><span></span></div>
        </div>
        <div class="field">
          <div class="label">Cruise Line:</div>
          <div class="val"><span></span></div>
        </div>
        <div class="field">
          <div class="label">Type:</div>
          <div class="val"><span>Periodic</span></div>
        </div>
        <div class="field port">
          <div class="label">Port:</div>
          <div class="val"><span></span></div>
        </div>
      </div>

      <div class="score-stub"><span>Score</span></div>
    </div>

    <!-- Body: Left pane / Right pane / Score rail -->
    <div class="body">
      <!-- LEFT PANE -->
      <div class="pane">
        <div class="legend">
          <div>Item No.</div>
          <div>Point Value</div>
          <div class="legend-bar">
            <span>Description</span>
            <span>* = Critical Item</span>
          </div>        </div>
        <div class="critical-note"></div>

        <!-- Disease Reporting -->
        <div class="section-title">Disease Reporting</div>
        <div class="row">
          <div class="cell item critical">01</div>
          <div class="cell pts">4</div>
          <div class="cell desc">Disease reporting</div>
        </div>
        <div class="row">
          <div class="cell item">02</div>
          <div class="cell pts">1</div>
          <div class="cell desc">Medical logs maintained</div>
        </div>

        <!-- Potable Water -->
        <div class="section-title">Potable Water</div>
        <div class="row">
          <div class="cell item critical">03</div>
          <div class="cell pts">5</div>
          <div class="cell desc">Bunker / production source; Halogen residual</div>
        </div>
        <div class="row">
          <div class="cell item critical">04</div>
          <div class="cell pts">5</div>
          <div class="cell desc">Distribution system halogen residual</div>
        </div>
        <div class="row">
          <div class="cell item">05</div>
          <div class="cell pts">2</div>
          <div class="cell desc">Distribution system halogen analyzer calibrated</div>
        </div>
        <div class="row">
          <div class="cell item">06</div>
          <div class="cell pts">2</div>
          <div class="cell desc">Halogen analyzer chart recorder maintenance, operation, records; Micro sampling, records</div>
        </div>
        <div class="row">
          <div class="cell item critical">07</div>
          <div class="cell pts">3</div>
          <div class="cell desc">System protection: cross-connections, backflow; Disinfection</div>
        </div>
        <div class="row">
          <div class="cell item">08</div>
          <div class="cell pts">1</div>
          <div class="cell desc">Filling hoses, caps, connections, procedures; Sample records, valves; System constructed, maintenance</div>
        </div>

        <!-- Recreational Water Facilities -->
        <div class="section-title">Recreational Water Facilities</div>
        <div class="row">
          <div class="cell item critical">09</div>
          <div class="cell pts">3</div>
          <div class="cell desc">RWF halogen residuals</div>
        </div>
        <div class="row">
          <div class="cell item">10</div>
          <div class="cell pts">2</div>
          <div class="cell desc">RWF maintenance, safety equipment</div>
        </div>

        <!-- Food Safety - Personnel -->
        <div class="section-title">Food Safety — Personnel</div>
        <div class="row">
          <div class="cell item critical">11</div>
          <div class="cell pts">5</div>
          <div class="cell desc">Food handler's infections, communicable diseases</div>
        </div>
        <div class="row">
          <div class="cell item critical">12</div>
          <div class="cell pts">4</div>
          <div class="cell desc">Hands washed; Hygienic practices</div>
        </div>
        <div class="row">
          <div class="cell item">13</div>
          <div class="cell pts">3</div>
          <div class="cell desc">Management, knowledge, monitoring</div>
        </div>
        <div class="row">
          <div class="cell item">14</div>
          <div class="cell pts">1</div>
          <div class="cell desc">Outer clothing clean; Jewelry, Hair, Hand Sanitizers</div>
        </div>

        <!-- Food Safety — Food -->
        <div class="section-title">Food Safety — Food</div>
        <div class="row">
          <div class="cell item critical">15</div>
          <div class="cell pts">5</div>
          <div class="cell desc">Food source, sound condition; Food re-service</div>
        </div>
        <div class="row">
          <div class="cell item critical">16</div>
          <div class="cell pts">5</div>
          <div class="cell desc">Potentially hazardous food temperatures</div>
        </div>
        <div class="row">
          <div class="cell item">17</div>
          <div class="cell pts">2</div>
          <div class="cell desc">Temperature practices; Thawing</div>
        </div>
        <div class="row">
          <div class="cell item">18</div>
          <div class="cell pts">3</div>
          <div class="cell desc">Cross-contamination</div>
        </div>
        <div class="row" style="border-bottom:2px solid var(--line)">
          <div class="cell item">19</div>
          <div class="cell pts">2</div>
          <div class="cell desc">Food protected; Original containers, labeling; In-use food dispensing, preparation utensils</div>
        </div>
      </div>

      <!-- RIGHT PANE -->
      <div class="pane">
        <div class="legend">
          <div>Item No.</div>
          <div>Point Value</div>
          <div class="legend-bar">
            <span>Description</span>
            <span>* = Critical Item</span>
          </div>
        </div>

        <!-- Toilet & Handwashing -->
        <div class="section-title">Toilet and Handwashing Facilities</div>
        <div class="row">
          <div class="cell item">28</div>
          <div class="cell pts">2</div>
          <div class="cell desc">Equipment / utensil / linen / single / service storage, handling, dispensing/cleaning frequency</div>
        </div>
        <div class="row">
          <div class="cell item critical">29</div>
          <div class="cell pts">3</div>
          <div class="cell desc">Facilities convenient, accessible, design, installation</div>
        </div>
        <div class="row">
          <div class="cell item">30</div>
          <div class="cell pts">1</div>
          <div class="cell desc">Hand cleanser, sanitary towels, waste receptacles, handwash signs; Maintenance</div>
        </div>

        <!-- Toxic Substances -->
        <div class="section-title">Toxic Substances</div>
        <div class="row">
          <div class="cell item critical">31</div>
          <div class="cell pts">3</div>
          <div class="cell desc">Toxic items</div>
        </div>

        <!-- Facilities -->
        <div class="section-title">Facilities</div>
        <div class="row">
          <div class="cell item">32</div>
          <div class="cell pts">1</div>
          <div class="cell desc">Solid waste containers</div>
        </div>
        <div class="row">
          <div class="cell item">33</div>
          <div class="cell pts">1</div>
          <div class="cell desc">Decks / bulkheads / deckheads</div>
        </div>
        <div class="row">
          <div class="cell item">34</div>
          <div class="cell pts">1</div>
          <div class="cell desc">Plumbing fixtures / supply line / drain lines / drains</div>
        </div>
        <div class="row">
          <div class="cell item">35</div>
          <div class="cell pts">2</div>
          <div class="cell desc">Liquid waste disposal</div>
        </div>
        <div class="row">
          <div class="cell item">36</div>
          <div class="cell pts">1</div>
          <div class="cell desc">Lighting</div>
        </div>
        <div class="row">
          <div class="cell item">37</div>
          <div class="cell pts">1</div>
          <div class="cell desc">Rooms / equipment venting</div>
        </div>
        <div class="row">
          <div class="cell item">38</div>
          <div class="cell pts">1</div>
          <div class="cell desc">Unnecessary articles, cleaning equipment; Unauthorized personnel</div>
        </div>

        <!-- Environmental Health -->
        <div class="section-title">Environmental Health</div>
        <div class="row highlight">
          <div class="cell item">39</div>
          <div class="cell pts">3</div>
          <div class="cell desc">IPM program effective; Approved pesticide application</div>
        </div>
        <div class="row highlight">
          <div class="cell item">40</div>
          <div class="cell pts">1</div>
          <div class="cell desc">IPM procedures; Outer openings protection</div>
        </div>
        <div class="row">
          <div class="cell item">41</div>
          <div class="cell pts">2</div>
          <div class="cell desc">Housekeeping</div>
        </div>
        <div class="row">
          <div class="cell item">42</div>
          <div class="cell pts">1</div>
          <div class="cell desc">Child activity centers</div>
        </div>
        <div class="row">
          <div class="cell item">43</div>
          <div class="cell pts">1</div>
          <div class="cell desc">Ventilation</div>
        </div>

        <!-- Knowledge -->
        <div class="section-title">Knowledge</div>
        <div class="row" style="border-bottom:2px solid var(--line)">
          <div class="cell item">44</div>
          <div class="cell pts">2</div>
          <div class="cell desc">Person in charge, Knowledge</div>
        </div>
      </div>

      <!-- SCORE RAIL -->
      <div class="score-rail">
        <div class="text">Score</div>
      </div>
    </div>
  </div>
</body>
</html>
