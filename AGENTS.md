# AGENTS.md — iAudit Agent Instructions & Technical Record

> This file has two parts: **Part 1** tells an agent (human or AI) how to work in this repo. **Part 2** is a ground-truth architecture snapshot, kept accurate as of **2026-08-06**, so future work starts from facts instead of assumptions. For active task tracking / in-progress work items, see [CLAUDE.md](CLAUDE.md) — that file drifts as tasks complete; this one documents what's actually in the code.

---

## Part 1 — Agent Instructions

### What this project is
iAudit is a Laravel 12 audit-management system for shipboard IPM (Integrated Pest Management) / pest-control inspections. It has two consumers:
- A **mobile app** that drives audit data entry via a token-authenticated JSON API (`/api/*`).
- An **admin web panel** (Blade + Breeze) for listing submitted audits and generating PDF reports.

Reference/lookup data (questions, departments, headings, trap/EFK locations, etc.) is bulk-imported from client-provided Excel/CSV files via seeders; transactional data (audits, answers) is written by the mobile app through the API.

### Tech stack
- **Backend**: PHP / Laravel 12
- **Auth**: Laravel Breeze (session, admin panel) + Laravel Sanctum (bearer tokens, mobile API)
- **DB**: MySQL (database name `iaudit`)
- **Excel/CSV import**: `maatwebsite/excel` (`app/Imports/*`, driven by seeders)
- **PDF generation**: PDFShift HTTP API (production) or Spatie Browsershot (local fallback) — see Part 2. `barryvdh/laravel-dompdf` is installed but **not actually used** (only referenced in dead/commented-out code).
- **Frontend**: **No Node/Vite/Mix build step.** Tailwind, Alpine.js, Axios, and DataTables are all loaded via CDN `<script>`/`<link>` tags directly in Blade layouts. The only local, hand-maintained asset is `public/css/pdf-style.css`.

### Running locally
1. `composer install`
2. Configure `.env` for MySQL (`DB_CONNECTION=mysql`, `DB_DATABASE=iaudit`, etc.)
3. `php artisan migrate` — **note**: this repo's migrations do NOT create `users`, `cruise_companies`, `fleets`, or `ships`. Those tables are assumed to pre-exist in the `iaudit` database (legacy/external schema) — source that schema separately before a fresh setup will work.
4. `php artisan db:seed` — runs `DatabaseSeeder`, which imports `database/seeders/data/*.xlsx|*.csv` into the `*_iaudit` lookup tables. Several seeders **truncate** their target table first — don't run against a DB with data you want to keep without checking the seeder first.
5. `php artisan serve`
6. Optional: set `PDFSHIFT_API_KEY` in `.env` to use the PDFShift API for PDF rendering. Without it, PDF generation falls back to Spatie Browsershot, which has **hardcoded Linux paths** (`/usr/bin/node`, `/usr/bin/npm`, `/usr/bin/google-chrome`) in `AuditReportController::renderPdf()` — this will not work on Windows/WAMP as-is without either setting `PDFSHIFT_API_KEY` or patching those paths.

### Conventions to follow
- **Two families of tables/models**, don't mix their patterns:
  - *Reference/lookup* (`departments_iaudit`, `categories_iaudit`, `headings_iaudit`, `sub_headings_iaudit`, `template_refs_iaudit`, `templates_iaudit`, `questions_iaudit`, `question_ncs_iaudit`, `criteria_iaudit`, `text_boxes_iaudit`): manually-assigned non-autoincrementing primary keys (matching source Excel row IDs), `$timestamps = false`, bulk-imported — don't add auto-increment or timestamps here.
  - *Transactional/operational* (`answers_iaudit`, `audits`, `crt_trap_location_iaudit`, `efk_iaudit`, `other_crt_iaudit`, `other_efk_iaudit`, `ipm_efk_iaudit`, `ipm_traps_iaudit`): normal auto-increment `id` + timestamps.
- **No API Resource classes.** `ApiController` and `AuditReportController` build JSON/view data as large hand-rolled nested arrays inline. Match this pattern for new endpoints rather than introducing `JsonResource` ad hoc, unless a broader refactor is explicitly agreed.
- **No Form Requests in the mobile API.** `ApiController` validates inline via `$request->validate([...])`. Form Requests (`app/Http/Requests/`) are only used by the Breeze web side (`LoginRequest`, `ProfileUpdateRequest`).
- Mobile API responses follow `{"success": true, "data": [...]}` (except `login`/`logout`).

---

## Part 2 — Architecture / Technical Record

### Routes

**`routes/web.php`** (session auth, `web` middleware group)

| Method | Path | Controller@Action | Middleware | Name |
|---|---|---|---|---|
| GET | `/audits` | `AuditReportController@index` | — | `audit.index` |
| GET | `/` | closure → `welcome` view | — | — |
| GET | `/static-report` | `AuditReportController@staticReport` | `auth`,`verified` | `audit.report.static` |
| GET | `/audit/{id}/report` | `AuditReportController@showReport` | `auth`,`verified` | `audit.report.show` |
| GET | `/audit/{id}/pdf` | `AuditReportController@showPDFReport` | `auth`,`verified` | `audit.report.pdf.show` |
| GET | `/audit/{id}/download` | `AuditReportController@downloadPdf` | `auth`,`verified` | `audit.download` |
| GET | `/dashboard` | `AuditReportController@index` | `auth`,`verified` | `dashboard` |
| GET/PATCH/DELETE | `/profile` | `ProfileController@edit/update/destroy` | `auth`,`verified` | `profile.*` |

`routes/auth.php` pulls in standard Breeze auth routes (register, login, password reset/confirm, email verification, logout).

**`routes/api.php`** (stateless, Sanctum bearer tokens)

| Method | Path | Controller@Action | Middleware |
|---|---|---|---|
| POST | `/api/login` | `ApiController@login` | none (public) |
| POST | `/api/logout` | `ApiController@logout` | `auth:sanctum` |
| GET | `/api/companies` | `ApiController@companies` | `auth:sanctum` |
| GET | `/api/questions` | `ApiController@questions` | `auth:sanctum` |
| POST | `/api/answers` | `ApiController@submitAudit` | `auth:sanctum` |
| GET | `/api/trap-locations` | `ApiController@trapLocations` | `auth:sanctum` |
| GET | `/api/efk-locations` | `ApiController@efkLocations` | `auth:sanctum` |
| GET | `/api/other-crt-locations` | `ApiController@otherCrtLocations` | `auth:sanctum` |
| GET | `/api/other-efk-locations` | `ApiController@otherEfkLocations` | `auth:sanctum` |
| GET | `/api/ipm-efk-locations` | `ApiController@ipmEfkLocations` | `auth:sanctum` |
| GET | `/api/ipm-trap-locations` | `ApiController@ipmTrapLocations` | `auth:sanctum` |

### Controllers

**`ApiController`** (`app/Http/Controllers/ApiController.php`) — mobile app backend, fully DB-driven, no hardcoded data:
- `login` — `Auth::attempt`, issues a Sanctum token named `mobile-{uuid}`.
- `companies` — Company → Fleet → Ship hierarchy (only entities that have ships).
- `logout` — deletes the current access token.
- `questions` — full questionnaire tree: Department → Heading → Subheading → Category → Questions (+ NC data), same shape `buildAuditData()` builds server-side for the web report.
- `submitAudit` — validates and persists a full audit: creates one `Audit` row + one `AnswerIaudit` row per answer inside a DB transaction, uploads files to `storage/app/public/audits/{audit_id}`.
- `trapLocations` / `efkLocations` — CRT trap / EFK unit reference locations, grouped hierarchically.
- `ipmEfkLocations` / `ipmTrapLocations` — per-ship IPM device inventories.
- `otherCrtLocations` / `otherEfkLocations` — supplementary compliance/criteria reference data.

**`AuditReportController`** (`app/Http/Controllers/AuditReportController.php`) — admin listing + PDF reports:
- `index` — paginated (15/page), filterable (`search`, `status`, `user_id`, `ship_id`, `date_from`, `date_to`) audit listing → `audit-listing` view. Powers both `/audits` and `/dashboard`.
- `staticReport` — renders `static-audit-pdf-report` with **no data passed at all** (pure design mockup).
- `showReport($id)` — plain HTML render of `audit-pdf-report` (no PDF conversion).
- `showPDFReport($id)` / `downloadPdf($id)` — same view rendered to HTML then converted via `renderPdf()`; differ only in `Content-Disposition` (`inline` vs `attachment`).
- `renderPdf()` (private) — see PDF pipeline below.
- `buildAuditData($id)` (private) — loads `Audit` with `user`, `ship.fleet.company`, `answers.question`; builds a full `departments` tree, `trap_locations`, `efk_locations`, and `answers_by_question_text` (question text normalized/lowercased as the lookup key). Returns one large flat array consumed by the Blade report.
- `emptyAuditData()` (private) — placeholder data shape for report preview with no `$id`.
- A `previewPdf()` method exists but is entirely commented out — dead code referencing DomPDF and a nonexistent view.

**`ProfileController`** — standard Breeze profile edit/update/delete.

**`ExcelImportController`** (`app/Http/Controllers/ExcelImportController.php`) — empty stub, no methods, not wired to any route. The real Excel import logic lives in `app/Imports/*`, driven by seeders (see below).

**`Auth/*`** — stock Breeze auth controllers (registration, login, password reset/confirm, email verification).

### Models & Relationships (`app/Models`)

| Model | Table | PK | Notes |
|---|---|---|---|
| `User` | `users` | `id` | Fillable `name,email,password`. `HasApiTokens` (Sanctum). **No migration in this repo.** |
| `Company` | `cruise_companies` | `id` | `hasMany` Fleet. No migration in this repo. |
| `Fleet` | `fleets` | `id` | `belongsTo` Company, `hasMany` Ship. No migration in this repo. |
| `Ship` | `ships` | `id` | `belongsTo` Fleet, `belongsTo` Company. No migration in this repo. |
| `Audit` | `audits` | `id` | Fillable includes reference/status/score/consultant/PCRO/PCO/PIC/ports/dates. `belongsTo` User & Ship, `hasMany` AnswerIaudit. `generateReferenceNumber()` builds `AUD-{uniqid}-{id}`. |
| `AnswerIaudit` | `answers_iaudit` | `id` | `files` cast to array. `belongsTo` Question, User, Ship, Audit. `answer` column is a MySQL ENUM('Yes','No','N/A'). |
| `DepartmentIaudit` | `departments_iaudit` | `department_id` (non-incrementing) | `hasMany` TemplateIaudit. |
| `CategoryIaudit` | `categories_iaudit` | `category_id` (non-incrementing) | `hasMany` QuestionIaudit. |
| `HeadingIaudit` | `headings_iaudit` | `heading_id` (non-incrementing) | `hasMany` SubHeadingIaudit, QuestionIaudit. |
| `SubHeadingIaudit` | `sub_headings_iaudit` | `subheading_id` (non-incrementing) | `belongsTo` HeadingIaudit; `hasMany` QuestionIaudit. |
| `TemplateRefIaudit` | `template_refs_iaudit` | `reference_id` (non-incrementing) | `hasMany` templates/questions/textBoxes. ⚠️ see Known Issues. |
| `TemplateIaudit` | `templates_iaudit` | `template_id` (non-incrementing) | `belongsTo` Department & TemplateRef; `hasMany` questions/textBoxes. |
| `QuestionIaudit` | `questions_iaudit` | `question_id` (non-incrementing) | `belongsTo` heading/subHeading/category/reference; `hasMany` ncs, criteria. |
| `QuestionNcIaudit` | `question_ncs_iaudit` | `id` (auto) | Non-compliance text fields (remediation/consequence/USPH/IPM). `belongsTo` Question. |
| `CriteriaIaudit` | `criteria_iaudit` | `criteria_id` (non-incrementing) | `belongsTo` TemplateRefIaudit. |
| `TextBoxIaudit` | `text_boxes_iaudit` | `text_box_id` (non-incrementing) | `belongsTo` TemplateRefIaudit. |
| `CrtTrapLocationIaudit` | `crt_trap_location_iaudit` | `id` (auto) | Department/deck/section trap locations. No relationships. |
| `EfkIAudit` | `efk_iaudit` | `id` (auto) | EFK unit locations by department/deck/area. No relationships. |
| `OtherCrtIAudit` | `other_crt_iaudit` | `id` (auto) | Supplementary CRT compliance/reference data (16 `other_crt_*` fields). |
| `OtherEfkIAudit` | `other_efk_iaudit` | `id` (auto) | Mirrors OtherCrtIAudit, `other_efk_*` fields. |
| `IpmEfkIAudit` | `ipm_efk_iaudit` | `id` (auto) | Per-ship IPM EFK device inventory. |
| `IpmTrapIAudit` | `ipm_traps_iaudit` | `id` (auto) | Per-ship IPM trap device inventory. |

### Database / Migrations (chronological)

| Date | Table | Notes |
|---|---|---|
| 2025-09-05 | `departments_iaudit`, `categories_iaudit`, `headings_iaudit`, `sub_headings_iaudit`, `template_refs_iaudit`, `templates_iaudit`, `questions_iaudit`, `question_ncs_iaudit` | Core questionnaire structure. `questions_iaudit` migration has a dead/broken FK declaration — see Known Issues. |
| 2025-09-05/06 | `text_boxes_iaudit`, `criteria_iaudit` | Supplementary content, FK → `template_refs_iaudit`. |
| 2025-09-18 | `answers_iaudit` | FKs → `users.id`, `ships.id` (cascade), `questions_iaudit.question_id` (cascade) — depends on pre-existing `users`/`ships` tables. |
| 2025-10-23 | `crt_trap_location_iaudit` | |
| 2025-11-11 | `efk_iaudit` | |
| 2025-12-15 | `other_crt_iaudit` | |
| 2026-04-14 | `other_efk_iaudit` | |
| 2026-05-07 | `audits` | Unique `reference_number`, `status` default `'completed'`, composite index `[user_id, ship_id]`. |
| 2026-05-07 | `answers_iaudit` (alter) | Adds nullable `audit_id` FK → `audits`, cascade on delete. |
| 2026-05-12 | `audits` (alter) | Adds report metadata columns: consultant/PCRO/PCO/PIC names & positions, ports, dates, notes. |
| 2026-06-27 | `ipm_efk_iaudit`, `ipm_traps_iaudit` | Per-ship IPM device inventories. |

**Not present in this repo's migrations**: `users`, `cruise_companies`, `fleets`, `ships` — assumed pre-existing in the `iaudit` MySQL schema.

### Seeders & Excel Import Pipeline (`database/seeders/`)

`DatabaseSeeder` runs, in order: `ExcelSeeder` → `EfkIAuditSeeder` → `CrtTrapLocationSeeder` → `OtherCrtIAuditSeeder` → `OtherEfkIAuditSeeder` → `IpmEfkIAuditSeeder` → `IpmTrapIAuditSeeder`.

- **`ExcelSeeder`** — source `database/seeders/data/DataBase_Structure.xlsx`, via `App\Imports\WorkbookImport` (`WithMultipleSheets`), which maps each Excel sheet to its own importer class (`DepartmentImport`, `CategoryImport`, `HeadingImport`, `SubHeadingImport`, `TemplateRefImport`, `TemplateImport`, `QuestionImport`, `QuestionNcImport`, `CriteriaImport`, `TextBoxImport`) → one lookup table each.
- **`EfkIAuditSeeder`**, **`CrtTrapLocationSeeder`**, **`OtherCrtIAuditSeeder`**, **`OtherEfkIAuditSeeder`** — each truncates its target table, then imports from a dedicated `.xlsx` file.
- **`IpmEfkIAuditSeeder`**, **`IpmTrapIAuditSeeder`** — same pattern, importing from `.csv` files.

All import classes are in `app/Imports/`, built on `maatwebsite/excel`.

### PDF Report Pipeline

Three report views, three different levels of "dynamic":
- **`static-audit-pdf-report.blade.php`** (route `/static-report`) — pure design mockup with placeholder data (e.g. "Viking Mars (VOCX-MARS)"); its controller method passes no data, so it's 100% static.
- **`audit-pdf-report.blade.php`** (routes `/audit/{id}/report`, `/pdf`, `/download`) — the real report. Structure (headings/subheadings/categories/question text) is **hardcoded HTML**, mirroring the static mockup. Only the answer cells are dynamic: a `$answerCell($qText)` closure normalizes the hardcoded question string and looks it up in `answers_by_question_text` to render a Yes/No/N/A cell, falling back to "—" if no match. Cover-page metadata (vessel, consultant, dates, ports) comes from `$auditData` with static fallbacks.
- Rendering flow: `buildAuditData($id)` → render Blade to HTML string → `renderPdf($html)`:
  - If `PDFSHIFT_API_KEY` is set: POST the HTML to the PDFShift HTTP API (Chromium-based), fixed viewport `1241x1755`. Used in production (Hostinger, no local Chromium available).
  - Otherwise: Spatie Browsershot locally, same fixed viewport, hardcoded Linux binary paths.
  - The 1241×1755 viewport is deliberately matched to `--page-w`/`--page-h` custom properties in `public/css/pdf-style.css` so its `clamp()` rules resolve correctly.
- `barryvdh/laravel-dompdf` is a listed dependency but effectively **dead** — its only reference is inside the fully-commented-out `previewPdf()` method.

### Frontend / Admin Panel

- No Node/Vite build — Tailwind, Alpine.js, Axios, jQuery + DataTables (only on the listing page) all loaded via CDN.
- Auth views under `resources/views/auth/` — unmodified Breeze markup.
- Layouts: `layouts/app.blade.php` (authenticated shell), `layouts/guest.blade.php` (auth pages), `layouts/navigation.blade.php` (nav bar — currently just a single "Dashboard" link, which routes to the audit listing).
- `audit-listing.blade.php` — the actual admin home screen: server-paginated + DataTables client-side search/sort table of audits, with a per-row Actions dropdown linking to Plain Report / PDF Report / Download.
- No admin panel package (Nova/Filament/etc.) — hand-rolled Breeze + Blade.

### Known Issues / Tech Debt

- **`TemplateRefIaudit::criteriaTables()`** (`app/Models/TemplateRefIaudit.php:32-35`) references a nonexistent `CriteriaTableIaudit` class — the real model is `CriteriaIaudit`. Calling this relationship throws a class-not-found error. *(Verified directly.)*
- **`questions_iaudit` migration** (`database/migrations/2025_09_05_204143_create_questions_iaudit_table.php`) declares a foreign key on `department_id`, a column that is never added to the table in that migration — dead/broken code.
- **`buildAuditData()`** (`AuditReportController.php:147-329`) computes a full `departments`/`trap_locations`/`efk_locations` tree that costs real DB queries but is **never rendered** by `audit-pdf-report.blade.php` — only `answers_by_question_text` is actually consumed via `$answerCell()`. Wasted work every report render.
- **Fragile text-matching design**: the report body's question text is hardcoded HTML, matched against DB `question_text` via lowercase/whitespace normalization. If a DB question's text is edited and no longer matches the hardcoded string, that row silently falls back to "—" instead of erroring.
- **`resources/views/dashboard.blade.php`** is orphaned — its one action button links to `route('audit.report')`, which is commented out in `routes/web.php`; the live `/dashboard` route now points to `AuditReportController::index` (the listing view), not this file.
- **`static-audit-pdf-report.blade.php`**'s one "dynamic" binding (`$auditData['ship_name'] ?? 'Viking Mars'`) is never fed real data — `staticReport()` passes nothing to the view — so it always shows the placeholder.
- **`barryvdh/laravel-dompdf`** dependency installed but unused in any live code path.
- **Browsershot Linux paths are hardcoded** (`/usr/bin/node`, `/usr/bin/npm`, `/usr/bin/google-chrome`) — local PDF generation on Windows/WAMP will fail unless `PDFSHIFT_API_KEY` is set or the paths are patched for the local environment.
- **No migrations for `users`, `cruise_companies`, `fleets`, `ships`** — a fresh environment needs that schema sourced from elsewhere; nothing in this repo creates them.
- `OtherEfkIAuditSeeder` imports both `OtherCrtIAuditImport` and `OtherEfkIAuditImport` but only uses the latter — a minor unused-import cleanliness issue.
