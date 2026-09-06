# Schema & API Refactor — IPM Question Bank (Old 12-tab → New `Data` sheet)

> Engineering reference, not a status report. Update this file as decisions land;
> it's the thing to open before touching `questions_iaudit` or anything downstream
> of it. Companion doc for stakeholders/timeline: [IPM Schema Update — Delivery
> Commitment](https://claude.ai/code/artifact/37d369b1-8452-41a3-8e22-fb4e4951be56)
> — the client-facing summary, confirmed 5-week window. This file is the source of
> truth for what's actually being built; if the two ever disagree, this file wins
> and the artifact needs updating, not the other way round.

**Last verified against:** `database/seeders/data/DataBase_Structure.xlsx` (in-repo,
currently imported) and `Audit Report Non-Compliance Tables (2026-07-15) 02.xlsx`
(client-supplied target). Row/column counts below were read directly out of both
files, not estimated.

**Since last edit:** a round of client correspondence (Aug 2026) confirmed several
open items below, surfaced one genuinely new requirement (multi-template support,
§3.4) and one new out-of-repo requirement (mobile auto-update-on-submit, §9) — and
exposed a real conflict between §3.2's soft-retire design and what the client has
now twice confirmed about how re-import should behave. Read §3.2 and §6 before
writing the importer.

**Also this pass:** cross-checked this whole file against an independent,
from-scratch schema/ripple-effect walkthrough run in a parallel conversation
thread — same repo, same two workbooks, no client correspondence involved. It
landed on the same 9-table retirement list, the same cascade-delete finding, and
the same §6 report-section flags without having read this file first, which is a
decent confidence signal. Two things it caught that hadn't made it in yet: a
numeric slip in §3.1 (now fixed) and the note on `answers_iaudit.files` in §5.
It also surfaced a still-open gap — see the new bullet in §6 on the untranscribed
video walkthrough.

This pass is a self-audit, not new client input: adding §3.4 after §3.1/§3.2/§5/§7
were already written left three things quietly out of date with each other —
`short_code`'s uniqueness scope, one stale QA checklist item still describing the
abandoned upsert approach, and a gap where neither consumer in §5 accounts for
`template` scoping. Fixed in place below, each marked where it happened so it's
clear these are internal-consistency catches, not new facts from Nutrastat.

**This edit:** the commercial proposal committed "live on Contabo" as a named,
priced deliverable, but nothing in this file said a word about deployment —
checked the codebase against that gap and it's real: two divergent PDF-rendering
paths with very different server requirements, no deployment docs anywhere in the
repo, and `public/storage` isn't even linked yet. See §10, new.

For broader architecture context that isn't specific to this refactor (routes,
controllers, PDF pipeline, other known bugs), see [AGENTS.md](AGENTS.md) — this
file stays scoped to the question-bank schema change and its direct ripple
effects; AGENTS.md covers the rest of the app.

---

## 1. Why this exists

Nutrastat replaced their 10-sheet question workbook with one flat `Data` sheet.
Every table in this codebase that mirrors the old 12-tab shape has to collapse
into something that matches the new sheet, and every consumer of that data
(2 API endpoints, 1 PDF report, 1 importer pipeline) has to move with it.

This is a **schema redesign**, not a column-add. Treat it that way — don't bolt
new columns onto the old 10-table structure and call it done.

Scope has widened once since this doc was first written: the Excel import is
moving from "artisan command a developer runs" to an **admin-side action gated
to specific users**, which means a roles/permissions layer has to exist first —
see §8. There's currently none in the codebase to build on.

Two more things confirmed directly with the client since that scope-widening
note — one softens it, one adds to it:

- **Softens it:** the client's actual ask is that import stop being a CLI
  command and become a website screen — not that it be restricted to named
  individuals. A full multi-role permission system is being offered as a
  bonus, built time-permitting, not required to hit the committed delivery
  date. See §8.1.
- **Adds to it:** the client re-imports per **template**, not globally, and
  wants the previous template's questions **hard-deleted**, not soft-retired,
  before the new ones load — a direct contradiction of §3.2 as originally
  written. See §3.4 and §3.2.

---

## 2. Current schema (as-is)

| Table | PK | Purpose | Rows (seeded) |
|---|---|---|---|
| `departments_iaudit` | `department_id` (natural, not auto-inc) | Department names | 11 |
| `categories_iaudit` | `category_id` (natural) | Line labels | 127 |
| `headings_iaudit` | `heading_id` (natural) | Section headings | 7 |
| `sub_headings_iaudit` | `subheading_id` (natural) → FK `heading_id` | Sub-headings | 40 |
| `template_refs_iaudit` | `reference_id` (natural) | Cross-reference codes | 43 |
| `templates_iaudit` | `template_id` (natural) → FK `department_id`, `reference_id` | Report templates | 133 |
| `questions_iaudit` | `question_id` (natural) → FK `reference_id`, `heading_id`, `subheading_id`, `category_id`, `department_id` | The question bank | 157 |
| `question_ncs_iaudit` | `id` (auto-inc) → FK `question_id` | Non-compliance text, **10 columns**: `nc_heading`, `nc_text`, `nc_rem_hd`, `nc_rem_text`, `nc_con_hd`, `nc_con_text`, `nc_usph_hd`, `nc_usph_text`, `nc_ipm_hd`, `nc_ipm_ref` | 151 |
| `criteria_iaudit` | `criteria_id` (natural) → FK `reference_id` | Criteria box text | 39 |
| `text_boxes_iaudit` | `text_box_id` (natural) → FK `reference_id` | Narrative paragraphs | 17 |
| `answers_iaudit` | `id` (auto-inc) → FK `question_id` **`onDelete('cascade')`** | Submitted audit answers | live data — **verify before touching anything below** |

**Key fact that drives the whole migration plan:** `QuestionIaudit::$incrementing = false`
(`app/Models/QuestionIaudit.php:17`) — `question_id` is not a surrogate key, it's
the row ID from the old spreadsheet. Combined with the `cascade` delete on
`answers_iaudit`, **deleting/truncating `questions_iaudit` deletes every answer
row pointing at it.** A naive "wipe and reseed" for the new sheet is a data-loss
bug, not a migration.

> **Correction to the table above, verified directly against the migration
> file:** the `department_id` FK listed for `questions_iaudit` doesn't actually
> work. `database/migrations/2025_09_05_204143_create_questions_iaudit_table.php`
> declares `$table->foreign('department_id')->references('department_id')->on('departments_iaudit')`
> but never adds a `department_id` column to the table in the same migration —
> confirmed by `QuestionIaudit::$fillable` (`app/Models/QuestionIaudit.php:11-14`),
> which has no `department_id` either. Running this migration against a fresh
> database throws (`SQLSTATE... doesn't exist in table`) — it's currently only
> not blocking anyone because no one has run `migrate:fresh` from a clean DB
> since it was written. **Two implications:**
> - If step 1 of §4 ever involves standing up a fresh environment (not just
>   snapshotting the live one), this migration has to be fixed or dropped
>   first, independent of the schema refactor itself.
> - It's moot for the target schema (§3.1) — `department` there is a plain
>   string column off `Data` sheet column B, not an FK — so no design change
>   needed, just don't carry this broken FK forward into `questions_iaudit_v2`.

Import pipeline today (`database/seeders/ExcelSeeder.php` → artisan seed only, no
web UI — `app/Http/Controllers/ExcelImportController.php` is an empty stub):

```
WorkbookImport (app/Imports/WorkbookImport.php)
 ├─ DepartmentImport   → departments_iaudit
 ├─ CategoryImport     → categories_iaudit
 ├─ HeadingImport      → headings_iaudit
 ├─ SubHeadingImport   → sub_headings_iaudit
 ├─ TemplateRefImport  → template_refs_iaudit
 ├─ TemplateImport     → templates_iaudit
 ├─ QuestionImport     → questions_iaudit
 ├─ QuestionNcImport   → question_ncs_iaudit
 ├─ CriteriaImport     → criteria_iaudit
 └─ TextBoxImport      → text_boxes_iaudit
```

---

## 3. Target schema (to-be)

New workbook has 3 sheets: `Config` (dropdown lists — informational only, not
imported), `Data` (**359 data rows × 19 columns** — the whole question bank),
`Report` (an Excel formula helper tab that re-derives `Data` via `INDEX`/`MATCH`
for the client's own use — **do not import this sheet**, it's not source data).

`Data` sheet row-type breakdown (column `C`, `Criteria`): 1 `Template` row, 12
`Text` rows, 8 `Criteria` rows, **338 blank rows = actual questions**.

### 3.1 Column map — `Data` sheet → proposed table

| Sheet col | Sheet header | New column | Type | Notes |
|---|---|---|---|---|
| A | `Short_Code` | `short_code` | `string`, unique **per `template`** — composite index `[template, short_code]`, not a bare unique column | Natural key from the sheet — **not** the DB PK, see §3.2. Scoped per-template now that §3.4 confirms multiple templates are coming; a bare global-unique column would reject a second template that happens to reuse a code the first one already has |
| B | `Departments` | `department` | `string`, nullable | Only 5 distinct values seen: IPM Plan Management, Provisions, Recycling Centre, Deck, General |
| C | `Criteria` | `row_type` | `enum('template','text','criteria','question')` | Blank cell → `question` |
| D | `Text_Icons` | `text_icon` | `string`, nullable | Only `CRT`/`EFK` populated (37 / 27 rows); rest presumably render as "Others" client-side |
| E | `Ordinal` | `ordinal` | `unsignedInteger`, nullable | Sort order within a block |
| F | `Block_Ref` | `block_ref` | `string`, nullable, **indexed** | 11 distinct values today: `BLOCK_DOC`(66) `BLOCK_STORE`(68) `BLOCK_OBS`(8) `BLOCK_CRT`(37) `BLOCK_EFK`(27) `BLOCK_STD`(27) `BLOCK_PROV`(15) `BLOCK_RECYCLE`(23) `BLOCK_RG`(26) `BLOCK_RG_BUNK`(13) `BLOCK_VSAC`(3) — client's requirement notes only list 7; confirm before the mobile app builds against this |
| G | `Title` | `title` | `string`, nullable | Old "heading" level |
| H | `Heading` | `heading` | `string`, nullable | Old "subheading" level |
| I | `Category` | `category` | `string`, nullable | Old "category" level |
| J | `QTN` | `qtn_text` | `text`, nullable | Compliant/"Yes" report sentence |
| K | `Yes` | *(drop)* | — | Redundant flag column, value is always literal "Yes" where `QTN` is filled |
| L | `NCT` | `nct_text` | `text`, nullable | Non-compliant/"No" report sentence — 89% filled (302/338) |
| M | `No` | *(drop)* | — | Same redundancy as `Yes` |
| N | `Responsibility` | `responsibility` | `text`, nullable | **97% of filled rows (304 of 313) are placeholder `"PCRO/PCO Test Text! N"` — genuine authored wording exists for ~9 of 338 rows total (2.7%). Content gap, not a schema issue, but sits on the critical path to UAT sign-off** |
| O | `Consultant` | `consultant_remark` | `string`, nullable | 93% filled, uniformly "No further comment." |
| P | `VSP_Item_No` | `vsp_item_no` | `string`, nullable | 78% filled |
| Q | `Point_Loss` | `point_loss` | `unsignedSmallInteger`, nullable | 73% filled — sum this per section/audit for the score |
| R | `VSP_Reference` | `vsp_reference` | `string`, nullable | 72% filled |
| S | `VSP_Description` | `vsp_description` | `string`, nullable | 72% filled |

Plus new bookkeeping columns not in the sheet:

| Column | Type | Why |
|---|---|---|
| `id` | `bigIncrements`, PK | Real surrogate key — see §3.2 |
| `is_active` | `boolean`, default `true` | Soft-retire a question instead of deleting it — see §3.2. **Provisional:** if §3.2's delete-and-replace reading is confirmed, this column may turn out unnecessary on question rows themselves and end up documenting intent rather than doing work — keep it in the migration, don't treat it as final until §3.2 resolves |
| `imported_batch` | `string`, nullable | Which import run last touched this row, for audit trail |
| `created_at` / `updated_at` | timestamps | Standard |

### 3.2 The key-stability fix

Old: `answers_iaudit.question_id` → `questions_iaudit.question_id` (natural key,
`onDelete('cascade')`).

New:
- `questions_iaudit.id` becomes a normal auto-incrementing PK.
- `short_code` is `unique` but **not** the FK target — it's how the importer
  upserts (`updateOrCreate(['short_code' => ...], [...])`), so re-running the
  importer updates existing rows in place instead of creating duplicates.
- `answers_iaudit.question_id` FK moves to `questions_iaudit.id`.
- **Change `onDelete('cascade')` to `onDelete('restrict')`.** Combined with
  `is_active`, a re-import that drops a question from the sheet should set
  `is_active = false` on the row, never delete it. `restrict` is then a safety
  net that throws instead of silently cascading if something *does* try to
  hard-delete a question with answers attached.
- Historical answers whose question no longer exists in the new sheet still
  render (read-only) against the retired row — `is_active = false` is a display
  flag, not a hide flag, for report/audit-history purposes.

> **⚠ Conflict, unresolved.** The design above exists specifically to avoid a
> hard delete. But the client has confirmed twice, independently, that
> re-import should **DELETE all existing questions and responses** for a
> template and replace them — "this removes legacy issues." Working
> interpretation, not yet client-confirmed in writing: their "questions and
> responses" means the question row and its own `qtn_text`/`nct_text` content,
> **not** the mobile-submitted rows in `answers_iaudit` — those stay app-only
> per every other answer the client has given (see §6). If that reading holds,
> `is_active` becomes unnecessary for the question-bank rows themselves (a
> template reimport really can delete-and-replace them), but `answers_iaudit`
> should still sit on `restrict`, not `cascade`, as a safety net in case a
> bulk delete for a template ever hits a question with live submitted answers
> attached. **Do not build the delete logic until this reading is confirmed —
> see §6.** Getting it wrong either loses real audit history or silently
> ignores the client's explicit "remove legacy issues" instruction.

### 3.3 Tables to retire

`departments_iaudit`, `categories_iaudit`, `headings_iaudit`, `sub_headings_iaudit`,
`templates_iaudit`, `template_refs_iaudit`, `criteria_iaudit`, `text_boxes_iaudit`,
`question_ncs_iaudit` — **9 tables**, all folded into the flat `questions_iaudit`
row shape above. Confirmed via grep: nothing outside `app/Models/*Iaudit.php` and
`app/Imports/*Import.php` references any of them (no Blade views, no console
commands, no other controllers) — safe to drop once the new table is live.

### 3.4 Multi-template support (confirmed, new — not in the original column map)

Client, in writing: *"the script will check the template name. Currently we are
just using the one, but there will be several."* And, on re-import: *"the
script will then DELETE all existing questions and responses and import the
new ones... at the start of the import, you DELETE existing data for that
template."*

Not in §3.1's 19-column map at all. Add:

| Column | Type | Why |
|---|---|---|
| `template` | `string`, nullable, **indexed** | Which template a row belongs to; scopes the delete-and-reimport (§3.2) so re-loading one template never touches another |

Today there is exactly one template, so this can default/backfill to a
constant for the current import without breaking anything — the field just
needs to exist from day one so a second template later is a data change, not
a migration.

**Open, blocking:** how does the importer identify a template's name from the
file itself — a cell/column in the `Data` sheet, the filename, a sheet name,
or something else? Nothing in the sheet as documented in §3.1 carries this
today. Needed before the template-check logic in `AuditDataSheetImport` can
be written.

---

## 4. Migration plan (do in this order)

1. **Snapshot first.** `php artisan db:table answers_iaudit` / export both
   `answers_iaudit` and `questions_iaudit` before anything else. Confirm with
   whoever owns the data whether any *real* (non-seed) audits exist yet — this
   single fact decides whether step 3 is "just reseed" or "careful remap."
2. Write the new `questions_iaudit` migration (§3.1/§3.2/§3.4 — don't build
   from §3.1 alone, the `template` column and its composite unique index live
   in §3.4) as a **new** table (e.g. `questions_iaudit_v2`) rather than
   altering in place, so both shapes exist side by side during cut-over.
3. Write `App\Imports\AuditDataSheetImport` — single sheet, row-type branching
   on column C, scoped by `template` (§3.4). Per the client-confirmed
   behavior: for the template being imported, delete its existing question
   rows and insert the sheet's rows fresh, rather than `updateOrCreate`
   upsert-by-`short_code` as originally planned — `short_code` is still
   useful as the natural key for logging/diffing, just not as an upsert
   target if every re-import is a clean replace. Confirm the open conflict in
   §3.2 before wiring the delete. Skip the `Report` sheet entirely (§3, it's
   a formula helper, not source data).
4. Migrate `answers_iaudit.question_id` to point at `questions_iaudit_v2.id`:
   - If no real answers exist: truncate and reseed, done.
   - If real answers exist: match old `questions_iaudit.question_text` against
     new `qtn_text`/`nct_text` (the existing `normalize()` shim in
     `AuditReportController::buildAuditData()` already does fuzzy text matching
     — reuse it as a one-off migration script, then delete it from the
     controller, it shouldn't live on as runtime code).
5. Swap the table (rename `questions_iaudit` → `questions_iaudit_old`,
   `questions_iaudit_v2` → `questions_iaudit`), drop the 9 retired tables and
   their model/import classes.
6. Update every call site in §5.
7. Run the QA checklist in §7 against the real 338-row sheet before UAT.

---

## 5. Call sites to update (ripple effects)

| File | What's there today | What changes |
|---|---|---|
| `app/Models/QuestionIaudit.php` | `$primaryKey='question_id'`, `$incrementing=false`, relations to `heading()`/`subHeading()`/`category()`/`reference()`/`ncs()`, plus a dead `criteria()` relation (never called anywhere) | New `$fillable`, drop the 5 relation methods, add scopes for `row_type` and `is_active` |
| `app/Http/Controllers/ApiController.php:101-187` (`questions()`) | Groups department → heading → subheading → category; emits `question_ncs` (10-field array) | Regroup department → title → heading → category; emit flat `nct_text`/`responsibility`/`consultant_remark`/VSP fields; add `block_ref`/`text_icon` for app nav |
| `app/Http/Controllers/ApiController.php:213` (`submitAudit()`) | Validation rule literal string `exists:questions_iaudit,question_id` | Update to the new PK column name — **this breaks silently (generic 422) if missed**, no compile-time check on a validation rule string |
| `app/Http/Controllers/AuditReportController.php` (`buildAuditData()`) | Builds the same dept/heading/subheading/category tree against the old schema; `normalize()` fuzzy-matches answers to hardcoded report wording by lower-cased question text | Rewire to flat schema; delete `normalize()` once `short_code` gives a real join key; add VSP point-loss aggregation |
| `resources/views/audit-pdf-report.blade.php` (1,912 lines) + `public/css/pdf-style.css` | Old grouping + teal/red palette only | New banner/TOC/criteria-box/statement-table/non-compliance photo-grid layout (up to 8 images per card, per the target PDF's `APictureHeading` placeholders) — separate workstream from the schema change, but reads from its output. `answers_iaudit.files` (JSON, already populated by mobile uploads) needs **no schema change** to support this grid; it's a template concern only |
| `database/seeders/ExcelSeeder.php` | Points at `DataBase_Structure.xlsx`, runs `WorkbookImport` | Point at the new workbook, run `AuditDataSheetImport` |
| Mobile app (separate repo) | Parses today's `/api/questions` shape, including `question_ncs` fields by name | Out of this repo's control — needs a contract review before #2 above ships |
| `answers_iaudit.files` (JSON, unchanged by this refactor) | Stores uploaded photo paths per answer | Not a schema change, but it's the data source for the PDF's non-compliance photo-grid cards (up to 8 images per failed check, per the target report mockup) — worth knowing about when building that Blade section, and it's exactly the kind of upload that 404s in production if `php artisan storage:link` was never run (§10.2) |

**Gap surfaced by §3.4, not reflected in the rows above:** both
`ApiController::questions()` and `AuditReportController::buildAuditData()`
currently assume one ship-wide question set. Now that `template` is a real
column, both need to know *which* template applies to a given ship/audit
before they can query correctly, and nothing in this doc defines that mapping
yet. Don't wire either endpoint to the new schema until it's answered — see
the matching open question in §6.

New call site, not yet built, **out of the current milestone** — see §9: a
version/timestamp check the app can poll after `SUBMIT AUDIT`, so it knows
whether to pull a fresh question bank. Nothing today exposes this.

---

## 6. Open risks / questions

**Resolved since last edit:**

- ~~`Yes`/`No` column purpose~~ — client-confirmed: they're for whoever edits
  the sheet by hand, not a stored value. Import parses past them; §3.1's
  `drop` call stands, now confirmed rather than assumed.
- ~~Import mechanism~~ — client-confirmed: a website screen, not a CLI
  command. Matches §8 as already planned.

**Still open, blocking:**

- **Live-data check (blocking §4 step 1).** Does `answers_iaudit` hold any real
  (non-seed) rows right now? Decides whether the migration is a reseed or a
  remap.
- **Delete-scope ambiguity (blocking §3.2/§3.4).** Does the client's "delete
  existing questions and responses" on re-import ever reach real submitted
  `answers_iaudit` rows, or only the question bank's own `qtn_text`/`nct_text`
  content? Decides the FK strategy outright — see the conflict callout in §3.2.
- **Full purpose of `NCT`, `Responsibility`, `Consultant`, `VSP_Item_No`,
  `Point_Loss`, `VSP_Reference`, `VSP_Description`.** Client has acknowledged
  the question and said a fuller answer is coming; nothing beyond §3.1's
  column-map guesses is confirmed yet. `Point_Loss` in particular drives the
  computed score — don't treat the current guess as final for scoring logic.
- **How a template's name is identified in the file (§3.4).** Column,
  filename, sheet name, or something else — needed before the template-check
  logic can be written.
- **How does a ship/audit know which template applies to it (surfaced by §3.4,
  see §5)?** Needed before `ApiController::questions()` or
  `AuditReportController::buildAuditData()` can be rewired to the new schema —
  neither can query "the right" question set without this.
- **The original client video walkthrough has no usable transcript.** It's
  audio-only with no captions, and this sandbox can't reach the
  speech-recognition hosts needed to generate one (confirmed twice — once for
  the original `luma-video-requirement-summary.md`, again when a direct
  file-host link was offered later in the same thread; downloading from an
  arbitrary external host isn't available here either). Everything sourced
  from the video in this doc is the visual-only summary someone produced by
  hand, not a transcript — treat spoken nuance from that recording as
  unverified until someone gets a real transcript (Word Dictate/Transcribe,
  Teams/Zoom captions, Otter/Rev all work) and it gets folded in here.
- **`Block_Ref` taxonomy mismatch.** Sheet has 11 distinct values; the client's
  own requirement notes describe 7 (`BLOCK_DOC`, `BLOCK_STORE`, `BLOCK_OBS`,
  `BLOCK_CRT`, `BLOCK_PROV`, `BLOCK_RECYCLE`, `BLOCK_RG`). Confirm which list is
  authoritative before the mobile app is built against it.
- **No test coverage.** `tests/` only has framework-scaffolded Auth/Profile
  tests — nothing exercises questions, answers, or report generation. Every
  change above needs manual verification; there's no regression net to lean on.
- **Report sections 3 ("Video Dialogue") and 8 ("IPM Training") in the target
  PDF are not finished spec** — section 3 is literal Microsoft Word demo copy,
  section 8 carries the client's own internal note ("we have not yet agreed on
  the training courses and content"). Don't build either to pixel-match yet.
- **Video walkthrough not yet transcribed.** The client offered direct access
  to the source recording via a file-transfer link. Nothing in this repo's
  build/dev environment can fetch an arbitrary external file host or run
  speech-to-text, so the audio track hasn't been reviewed beyond what
  `luma-video-requirement-summary.md` already extracted visually (screen
  content only, no spoken words). Not blocking any workstream today, but get
  a transcript (Word Dictate/Transcribe, Teams/Zoom captions, or Otter/Rev on
  the recording) before treating this doc as the complete picture — the
  audio may carry requirements nothing visual-only has caught.

---

## 7. Definition of done / QA checklist

- [ ] `questions_iaudit` matches §3.1 exactly; all 9 retired tables + their
      models/importers are deleted from the repo, not just unused
- [ ] Re-running the importer twice on the same sheet, for the same template,
      leaves the same end state both times — no duplicate rows, nothing
      orphaned from the previous run. (This item originally described
      verifying an upsert-by-`short_code` approach — superseded once
      delete-and-replace-per-template was confirmed, §4 step 3. Test the
      current behavior, not the old one.)
- [ ] Re-importing one template never deletes, alters, or duplicates another
      template's questions — or any answers tied to them — verified against a
      real two-template test fixture, not just reasoned about (§3.4)
- [ ] `answers_iaudit` FK is `restrict`, not `cascade`; confirmed by attempting
      to delete a question with an answer attached and seeing it fail
- [ ] Every pre-migration answer row still resolves to a question (or a
      `is_active=false` retired one) after cut-over — zero orphans
- [ ] `POST /api/answers` validation rule updated and covered by a manual
      Postman/curl check (no automated test exists yet — consider adding one
      here, it's cheap and this is exactly the kind of silent-breakage bug a
      single feature test would catch)
- [ ] `GET /api/questions` new shape signed off by whoever owns the mobile app,
      before merging, not after
- [ ] VSP point-loss totals spot-checked by hand against 2–3 sample audits
- [ ] PDF report renders sections 1, 2, 4–7 against real data; sections 3 and 8
      excluded/stubbed pending client content
- [ ] Excel import route is unreachable while logged out — verified by hitting
      it unauthenticated, not just reading the gate in code. This much is
      required for this milestone regardless of the roles bonus (§8.1)
- [ ] *(Only if the roles bonus from §8.1 ships in this window)* at least one
      non-admin role exists and has been used to confirm it genuinely can't
      reach the import screen or endpoint
- [ ] Production PDF path (§10.1) decided and configured, not left to fall
      through by default — if PDFShift, `PDFSHIFT_API_KEY` set on Contabo and
      verified live; if Browsershot, Node/npm/Chrome installed and a real PDF
      generated end-to-end on the box, not just locally
- [ ] `php artisan storage:link` run on Contabo; an uploaded audit photo
      actually loads over HTTPS, not just present in `storage/app/public`

---

## 8. Admin-side access control (new requirement)

Confirmed nothing like this exists today: no `spatie/*permission*` package in
`composer.json`, no `role` column or relation on `App\Models\User`, no
`app/Http/Middleware` directory at all. Every authenticated user is
equivalent right now. Restricting the Excel import to specific users needs
this built first, not bolted onto `ExcelImportController` after the fact.

> **Scope note, client-confirmed.** What's actually committed for the current
> delivery window is that import stops being a CLI command and becomes a
> website screen behind ordinary authentication — any logged-in admin user,
> nothing more granular required. The full role/permission system below
> (named individuals, `import-questions` permission, etc.) is being offered
> as a **bonus, built time-permitting, at no cost to the committed date** —
> not a blocker for sign-off. Build the authenticated screen first; layer in
> `spatie/laravel-permission` if and when time allows within the window.

### 8.1 Recommendation

Use `spatie/laravel-permission` rather than hand-rolling roles — it's the
de facto standard for Laravel, gives policy/middleware/Blade-directive
integration for free, and the package's own migration creates the tables
below.

### 8.2 Shape

| Table | Source | Purpose |
|---|---|---|
| `roles` | package | e.g. `admin`, `auditor` (day-to-day mobile/report users), `question-editor` (can trigger the import, can't necessarily manage users) |
| `permissions` | package | e.g. `import-questions`, `manage-users`, `view-audits` |
| `model_has_roles` / `model_has_permissions` / `role_has_permissions` | package | pivot tables, standard package shape |

Minimum viable role set for this phase: **Admin** (everything, including
`import-questions` and `manage-users`) and **Auditor** (existing app/report
access, no import). Add `question-editor` later only if Nutrastat actually
wants to delegate importing to someone who isn't a full admin — don't build
it speculatively.

### 8.3 Gate points

- `routes/web.php` — wrap whatever route(s) `ExcelImportController` ends up
  exposing in `->middleware('can:import-questions')`.
- `ExcelImportController` itself — authorize at the top of every method, not
  just at the route level, in case a route ever gets reused or a policy
  changes.
- Admin nav / audit-listing UI — hide the "Import questions" entry point for
  users without the permission, so it's not just enforced server-side while
  still visibly dangled in the UI.

### 8.4 What's explicitly out of scope unless asked for

A full user-management screen (invite/deactivate/edit users) is a bigger
build than "gate one action behind a role." Default to the smallest version:
seed the two roles, assign them via `php artisan tinker` or a seeder for the
first few admin users, and revisit a self-serve role-assignment UI only if
Nutrastat asks for one.

---

## 9. Mobile auto-update-on-submit (new requirement, out of current milestone)

Client, in writing: about 13 devices run this one template today. When it's
updated, those devices should get the update, **on one condition** — they're
not already partway through an audit. Suggested trigger, also from the
client: check for an update the moment `SUBMIT AUDIT` succeeds, and apply it
then, so an in-progress audit is never disrupted mid-way.

Confirmed alongside this: audit responses remain app-only regardless — this
mechanism only ever pulls a fresh **question bank**, never touches
`answers_iaudit`. Consistent with everything else in this doc.

**Not costed into the current 5-week window** (see the linked delivery-
commitment artifact) — flagged this round, scoped separately. Needs, at
minimum:

- Something on the template (§3.4) that changes when it's reimported — a
  version number or `updated_at` the app can compare against, exposed via the
  API.
- A cheap endpoint or field the app can check post-submit without pulling the
  whole question bank every time.
- App-side logic to make the check and apply the update — out of this repo's
  control, same caveat as the existing mobile-app row in §5.

Don't design the `template` column or the questions API around this yet; note
it here so the next round of schema work doesn't have to rediscover it.

---

## 10. Deployment — Contabo (committed deliverable, previously undocumented)

The commercial proposal lists "live on your Contabo server — provisioned,
configured, deployed and smoke-tested" as one of six named, priced
deliverables. Nothing about deployment existed anywhere in this repo before
this edit — no `Dockerfile`, no CI config, no deployment doc, not even a
README section. Checked the codebase directly for what provisioning actually
has to account for:

### 10.1 The PDF pipeline forks into two very different server requirements

`config/services.php` and `composer.json` confirm two independent rendering
paths, chosen at runtime by whether an env var is set:

- **`PDFSHIFT_API_KEY` set** → PDF rendering happens on PDFShift's cloud
  service. The Contabo box itself needs nothing beyond outbound HTTPS.
- **`PDFSHIFT_API_KEY` unset** → falls back to `spatie/browsershot`, which
  drives a **local headless Chrome via Node/npm** (`config('services.browsershot')`
  auto-detects `node`/`npm`/`chrome_path`, or takes explicit overrides). This
  means the server needs Node.js, npm, and a Chrome/Chromium binary installed
  and kept working — a materially heavier, more fragile provisioning job than
  "PHP + MySQL + Nginx."

**Decide which path is authoritative for production before provisioning, not
after.** Provisioning for both "just in case" is wasted effort and a wasted
attack surface; provisioning for neither and discovering the gap when the
first report fails to render in prod is worse. If PDFShift is the intended
path, get the API key into `.env` on the server as part of the deploy
checklist below — don't let it silently fall through to the Browsershot path
by omission.

### 10.2 Baseline provisioning checklist

- PHP 8.2+, matching the `composer.json` constraint, with the extensions
  Laravel + `maatwebsite/excel` + `barryvdh/laravel-dompdf` need (`ext-zip`,
  `ext-gd` or `ext-imagick`, `ext-mbstring`, `ext-xml` — verify against
  whatever PHP build Contabo provisions by default, don't assume).
- MySQL (matching `DB_CONNECTION=mysql` in `.env.example`) — confirm version
  compatibility with the migrations as written.
- Web server (Nginx is the conventional pairing for this stack; Apache works
  too — either way, document whichever gets chosen, since nothing in the repo
  currently says).
- `php artisan storage:link` — **`public/storage` does not exist in this
  repo's working tree.** Audit photo uploads (`AnswerIaudit.files`, JSON) and
  anything else written to `storage/app/public` will 404 in production until
  this runs. Cheap to miss, easy to catch with one QA-checklist line.
- Queue worker: `QUEUE_CONNECTION=database` in `.env.example`, but
  `app/Jobs/` is currently empty — nothing is actually queued yet. No
  supervisor/worker process needed for this milestone; revisit if the
  import or PDF-generation work ever moves onto the queue (a queued import
  would be a reasonable follow-up once the single-sheet importer exists, but
  isn't in scope now — don't provision for it speculatively).
- `APP_ENV=production`, `APP_DEBUG=false`, a real `APP_KEY`, SSL/domain
  pointed at the box — standard Laravel go-live items, listed here only so
  they're on the same checklist as the app-specific ones above rather than
  assumed "obvious and therefore skippable."

### 10.3 Open, blocking

- **Which PDF path is production-authoritative — PDFShift or Browsershot?**
  Drives §10.1 directly; not decided anywhere in this doc or the proposal.
- **Is this a fresh Contabo VPS, or is there an existing one already serving
  something?** Decides whether step 1 is "provision from scratch" or "audit
  what's already there."
- **Who holds the Contabo credentials/access today**, and when do they get
  handed over — needed before any provisioning step can start, independent
  of the code being ready.
- **Does live data (per §6's blocking live-data check) need to move to this
  server, or is this a fresh install with fresh data?** Same fact that gates
  §4 step 1 also gates the deployment cutover — one migration event, not two
  independent ones.
