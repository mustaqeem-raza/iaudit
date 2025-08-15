{{-- resources/views/reports/vessel-sanitation-inspection.blade.php --}}
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Vessel Sanitation Inspection Report (Bootstrap)</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">

  {{-- Bootstrap 5 --}}
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

  <style>
    /* Minimal custom CSS */
    .w-48{width:48px}
    .score-vertical{writing-mode:vertical-rl; transform:rotate(180deg); letter-spacing:.8px}
    .logo-circle{width:118px;height:118px}
    .logo-inner{width:84%;height:84%}
    .bg-highlight{background:#fff1a8}            /* yellow highlight for 39–40 */
    .critical::after{content:"*"; font-weight:700}
    .report-table col.item{width:88px}
    .report-table col.points{width:96px}
    .section-heading{letter-spacing:.2px}
  </style>
</head>
<body class="bg-light text-dark">

  <div class="container my-3">
    <div class="border border-2 border-dark bg-white">

      {{-- Title bar --}}
      <div class="bg-dark text-white text-center fw-bold py-2 border-bottom border-2 border-dark">
        VESSEL SANITATION INSPECTION REPORT
      </div>

      {{-- Header band: Logo | Meta | Score stub --}}
      <div class="row g-0 border-bottom border-2 border-dark">
        {{-- Logo --}}
        <div class="col-auto border-end border-2 border-dark d-flex align-items-center justify-content-center p-2" style="width:156px">
          <div class="position-relative rounded-circle border border-2 border-dark d-flex flex-column align-items-center justify-content-center text-center logo-circle">
            <div class="position-absolute rounded-circle border border-1 border-dark logo-inner"></div>
            <div class="fw-bold">EAVICES</div>
            <div class="small mt-1">Public Health<br>Service</div>
          </div>
        </div>

        {{-- Meta fields --}}
        <div class="col border-end border-2 border-dark">
          <div class="container-fluid py-2">
            <div class="row g-3">
              <div class="col-12 col-md-3">
                <div class="fw-bold small">Vessel:</div>
                <div class="border-bottom border-2 border-dark pb-1">____________________________</div>
              </div>
              <div class="col-12 col-md-3">
                <div class="fw-bold small">Date:</div>
                <div class="border-bottom border-2 border-dark pb-1">________________</div>
              </div>
              <div class="col-12 col-md-3">
                <div class="fw-bold small">Cruise Line:</div>
                <div class="border-bottom border-2 border-dark pb-1">________________________</div>
              </div>
              <div class="col-12 col-md-3">
                <div class="fw-bold small">Type:</div>
                <div class="border-bottom border-2 border-dark pb-1">Periodic</div>
              </div>
              <div class="col-12">
                <div class="fw-bold small">Port:</div>
                <div class="border-bottom border-2 border-dark pb-1">____________________________</div>
              </div>
            </div>
          </div>
        </div>

        {{-- Score stub (aligned with future rail) --}}
        <div class="col-auto w-48 d-flex align-items-center justify-content-center">
          <div class="fw-bold score-vertical">Score</div>
        </div>
      </div>

      {{-- Body: Left pane | Right pane | Score rail --}}
      <div class="row g-0">

        {{-- LEFT PANE --}}
        <div class="col-12 col-lg-6 border-end border-2 border-dark">
          {{-- Legend --}}
          <div class="bg-light border-bottom border-2 border-dark px-2 py-1 fw-bold">
            Item No. / Point Value / Description
          </div>
          <div class="border-bottom border-2 border-dark px-2 py-1 small">
            * = Critical Item
          </div>

          {{-- Table --}}
          <table class="table table-sm table-bordered border-dark m-0 report-table">
            <colgroup>
              <col class="item"><col class="points"><col>
            </colgroup>
            <tbody>
              {{-- Section: Disease Reporting --}}
              <tr class="table-dark">
                <td colspan="3" class="fw-bold text-uppercase section-heading">Disease Reporting</td>
              </tr>
              <tr>
                <td class="critical">01</td><td>4</td><td>Disease reporting</td>
              </tr>
              <tr>
                <td>02</td><td>1</td><td>Medical logs maintained</td>
              </tr>

              {{-- Section: Potable Water --}}
              <tr class="table-dark">
                <td colspan="3" class="fw-bold text-uppercase section-heading">Potable Water</td>
              </tr>
              <tr>
                <td class="critical">03</td><td>5</td><td>Bunker / production source; Halogen residual</td>
              </tr>
              <tr>
                <td class="critical">04</td><td>5</td><td>Distribution system halogen residual</td>
              </tr>
              <tr>
                <td>05</td><td>2</td><td>Distribution system halogen analyzer calibrated</td>
              </tr>
              <tr>
                <td>06</td><td>2</td><td>Halogen analyzer chart recorder maintenance, operation, records; Micro sampling, records</td>
              </tr>
              <tr>
                <td class="critical">07</td><td>3</td><td>System protection: cross-connections, backflow; Disinfection</td>
              </tr>
              <tr>
                <td>08</td><td>1</td><td>Filling hoses, caps, connections, procedures; Sample records, valves; System constructed, maintenance</td>
              </tr>

              {{-- Section: Recreational Water Facilities --}}
              <tr class="table-dark">
                <td colspan="3" class="fw-bold text-uppercase section-heading">Recreational Water Facilities</td>
              </tr>
              <tr>
                <td class="critical">09</td><td>3</td><td>RWF halogen residuals</td>
              </tr>
              <tr>
                <td>10</td><td>2</td><td>RWF maintenance, safety equipment</td>
              </tr>

              {{-- Section: Food Safety — Personnel --}}
              <tr class="table-dark">
                <td colspan="3" class="fw-bold text-uppercase section-heading">Food Safety — Personnel</td>
              </tr>
              <tr>
                <td class="critical">11</td><td>5</td><td>Food handler's infections, communicable diseases</td>
              </tr>
              <tr>
                <td class="critical">12</td><td>4</td><td>Hands washed; Hygienic practices</td>
              </tr>
              <tr>
                <td>13</td><td>3</td><td>Management, knowledge, monitoring</td>
              </tr>
              <tr>
                <td>14</td><td>1</td><td>Outer clothing clean; Jewelry, Hair, Hand Sanitizers</td>
              </tr>

              {{-- Section: Food Safety — Food --}}
              <tr class="table-dark">
                <td colspan="3" class="fw-bold text-uppercase section-heading">Food Safety — Food</td>
              </tr>
              <tr>
                <td class="critical">15</td><td>5</td><td>Food source, sound condition; Food re-service</td>
              </tr>
              <tr>
                <td class="critical">16</td><td>5</td><td>Potentially hazardous food temperatures</td>
              </tr>
              <tr>
                <td>17</td><td>2</td><td>Temperature practices; Thawing</td>
              </tr>
              <tr>
                <td>18</td><td>3</td><td>Cross-contamination</td>
              </tr>
              <tr>
                <td>19</td><td>2</td><td>Food protected; Original containers, labeling; In-use food dispensing, preparation utensils</td>
              </tr>
            </tbody>
          </table>
        </div>

        {{-- RIGHT PANE --}}
        <div class="col-12 col-lg-6 border-end border-2 border-dark">
          {{-- Legend --}}
          <div class="bg-light border-bottom border-2 border-dark px-2 py-1 fw-bold">
            Item No. / Point Value / Description
          </div>
          <div class="border-bottom border-2 border-dark px-2 py-1 small">
            * = Critical Item
          </div>

          {{-- Table --}}
          <table class="table table-sm table-bordered border-dark m-0 report-table">
            <colgroup>
              <col class="item"><col class="points"><col>
            </colgroup>
            <tbody>
              {{-- Section: Toilet & Handwashing Facilities --}}
              <tr class="table-dark">
                <td colspan="3" class="fw-bold text-uppercase section-heading">Toilet and Handwashing Facilities</td>
              </tr>
              <tr>
                <td>28</td><td>2</td><td>Equipment / utensil / linen / single / service storage, handling, dispensing/cleaning frequency</td>
              </tr>
              <tr>
                <td class="critical">29</td><td>3</td><td>Facilities convenient, accessible, design, installation</td>
              </tr>
              <tr>
                <td>30</td><td>1</td><td>Hand cleanser, sanitary towels, waste receptacles, handwash signs; Maintenance</td>
              </tr>

              {{-- Section: Toxic Substances --}}
              <tr class="table-dark">
                <td colspan="3" class="fw-bold text-uppercase section-heading">Toxic Substances</td>
              </tr>
              <tr>
                <td class="critical">31</td><td>3</td><td>Toxic items</td>
              </tr>

              {{-- Section: Facilities --}}
              <tr class="table-dark">
                <td colspan="3" class="fw-bold text-uppercase section-heading">Facilities</td>
              </tr>
              <tr>
                <td>32</td><td>1</td><td>Solid waste containers</td>
              </tr>
              <tr>
                <td>33</td><td>1</td><td>Decks / bulkheads / deckheads</td>
              </tr>
              <tr>
                <td>34</td><td>1</td><td>Plumbing fixtures / supply line / drain lines / drains</td>
              </tr>
              <tr>
                <td>35</td><td>2</td><td>Liquid waste disposal</td>
              </tr>
              <tr>
                <td>36</td><td>1</td><td>Lighting</td>
              </tr>
              <tr>
                <td>37</td><td>1</td><td>Rooms / equipment venting</td>
              </tr>
              <tr>
                <td>38</td><td>1</td><td>Unnecessary articles, cleaning equipment; Unauthorized personnel</td>
              </tr>

              {{-- Section: Environmental Health --}}
              <tr class="table-dark">
                <td colspan="3" class="fw-bold text-uppercase section-heading">Environmental Health</td>
              </tr>
              <tr class="bg-highlight">
                <td>39</td><td>3</td><td>IPM program effective; Approved pesticide application</td>
              </tr>
              <tr class="bg-highlight">
                <td>40</td><td>1</td><td>IPM procedures; Outer openings protection</td>
              </tr>
              <tr>
                <td>41</td><td>2</td><td>Housekeeping</td>
              </tr>
              <tr>
                <td>42</td><td>1</td><td>Child activity centers</td>
              </tr>
              <tr>
                <td>43</td><td>1</td><td>Ventilation</td>
              </tr>

              {{-- Section: Knowledge --}}
              <tr class="table-dark">
                <td colspan="3" class="fw-bold text-uppercase section-heading">Knowledge</td>
              </tr>
              <tr>
                <td>44</td><td>2</td><td>Person in charge, Knowledge</td>
              </tr>
            </tbody>
          </table>
        </div>

        {{-- SCORE RAIL --}}
        <div class="col-auto w-48 d-flex align-items-center justify-content-center border-start border-2 border-dark">
          <div class="fw-bold score-vertical">Score</div>
        </div>
      </div>
    </div>
  </div>

  {{-- Optional Bootstrap JS (not required for static) --}}
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
