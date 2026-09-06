# Schema Update — Minimal-Refactor Approach

> Companion to [refactor-schema.md](refactor-schema.md), not a replacement for it.
> That file lays out the full redesign (collapse 9 tables into one, new
> surrogate PK, full rewrite of both API grouping methods and the importer
> pipeline). This file asks a narrower question: **how much of that is
> actually required to get the new sheet live**, and proposes the smallest
> change set that still fixes the real problems. Everything in refactor-
> schema.md's §6 (open risks) and §1/§8/§9/§10 (roles, mobile auto-update,
> deployment) applies unchanged here — this file only re-scopes §3/§4/§5.

**Verified directly against the repo before writing this** — not assumed:
`app/Models/QuestionIaudit.php`, `app/Models/TemplateIaudit.php`,
`app/Models/DepartmentIaudit.php`, `app/Models/TemplateRefIaudit.php`,
`app/Models/CriteriaIaudit.php`, `app/Models/TextBoxIaudit.php`,
`app/Models/QuestionNcIaudit.php`, every `*_iaudit` migration file, and
`ApiController::questions()`/`submitAudit()`.

---

## 1. The key finding that makes minimalism possible

`ApiController::questions()` doesn't group questions by a `department_id`
column on `questions_iaudit` — **it can't, that column doesn't exist** (this
is the broken-migration bug refactor-schema.md §2 already flagged). The real
path today is:

```
DepartmentIaudit
 └─ hasMany templates_iaudit (department_id)
     └─ hasMany questions_iaudit  ← matched on reference_id, not template_id
         └─ heading / subHeading / category / ncs (all separate lookups)
```

`templates_iaudit` exists purely to bridge department → `reference_id` →
question. That's the one piece of the old schema that's genuinely
load-bearing scaffolding rather than useful structure — and the new sheet
makes it unnecessary, because every row already carries its own
`Departments` value directly (§3.1 col B). Give `questions_iaudit` a real
`department_id` at last, and the bridge table has no job left.

Everything else the mobile app currently receives — `heading`, `subHeading`,
`category`, grouped as department → heading → subheading → category — can
be produced from the exact same lookup tables the app already understands,
just populated by **get-or-create-by-name** from the flat sheet's `Title` /
`Heading` / `Category` text columns instead of a dedicated tab. Same tables,
same relations, same JSON shape out — the sheet just fills them differently.

---

## 2. What actually changes

### 2.1 `questions_iaudit` — additive migration, not a new table

| Change | Detail |
|---|---|
| **Add `department_id`** | Fixes the pre-existing broken FK as a side effect — free win, not extra scope. Populated by get-or-create against `departments_iaudit` during import. |
| **Add `short_code`** | `string`, indexed, unique **per `template`** (composite `[template, short_code]`) — the stable natural key the sheet gives us; also becomes the join key the PDF report matches against, so `AuditReportController::normalize()`'s fuzzy text-matching shim can be deleted, not carried forward. |
| **Add `template`** | `string`, indexed — same purpose as refactor-schema.md §3.4, unchanged reasoning. |
| **Add `row_type`** | `enum('template','text','criteria','question')` — only meaningful if criteria/text rows get folded in here too (see §2.3); otherwise this importer never needs to write anything but `question` into this table. |
| **Add `ordinal`, `text_icon`, `block_ref`** | Same as refactor-schema.md §3.1, nullable, straight columns off the sheet. |
| **Add `is_active`** | Same soft-retire display flag as the full plan — needed regardless of which path is taken. |
| **Flip the PK** | `question_id` stops being a hand-assigned `bigInteger` (`$incrementing = false`) and becomes a normal auto-increment (`$incrementing = true`, migrate the column to `bigIncrements`/`autoIncrement()`). It was only ever a stand-in for "the spreadsheet's row number" — now that `short_code` is the sheet's real stable identity, there's no reason left to hand-assign the PK. This is a column-type ALTER, not a parallel `_v2` table + cutover. |

**Not changed:** `heading_id`, `subheading_id`, `category_id` stay exactly
as they are — same FK columns, same lookup tables, same relations.

### 2.2 `question_ncs_iaudit` — reused, not replaced

This table already plays "extra report text per question" — that's exactly
what `NCT`, `Responsibility`, `Consultant`, and the four `VSP_*` columns are.
Add six nullable columns here instead of growing `questions_iaudit` further
or inventing a new table:

`responsibility`, `consultant_remark`, `vsp_item_no`, `point_loss`,
`vsp_reference`, `vsp_description`.

`nc_text` already covers `NCT`. The existing `ncs()` relation on
`QuestionIaudit` needs no change at all.

### 2.3 `criteria_iaudit` / `text_boxes_iaudit` — kept, re-scoped

Both currently key off `reference_id` → `template_refs_iaudit`. Add a
`template` string column to each (same value the new sheet's `template`
column carries) and import `row_type = 'criteria'` / `'text'` rows straight
into them by that column instead of by `reference_id`. Nothing else about
either table changes — same models, same fields, same consumers in
`AuditReportController`.

### 2.4 Tables retired: **2**, not 9

| Table | Why it can go |
|---|---|
| `templates_iaudit` | Its only job — bridging department to question — is replaced by the direct `department_id` on `questions_iaudit` (§2.1). |
| `template_refs_iaudit` | Every remaining consumer (`templates_iaudit`, `criteria_iaudit`, `text_boxes_iaudit`, and `questions_iaudit`'s already-dead `reference_id` FK) is replaced above. |

`departments_iaudit`, `headings_iaudit`, `sub_headings_iaudit`,
`categories_iaudit`, `question_ncs_iaudit`, `criteria_iaudit`,
`text_boxes_iaudit` — **all 7 kept, schema unchanged**, just populated by
name-lookup from the flat sheet instead of their own tab.

### 2.5 `answers_iaudit` — same fix either way

`onDelete('cascade')` → `onDelete('restrict')` on the `question_id` FK.
Already the right call in the full plan; unaffected by which schema
approach is chosen, still needs doing.

---

## 3. The importer — one new class, reusing what exists

`App\Imports\QuestionDataSheetImport` reads the single `Data` sheet, one
pass, branching on `row_type` (column C):

- **`question` rows (the 338):** get-or-create `department` / `heading` /
  `subheading` / `category` by name against the existing four lookup
  tables; upsert `questions_iaudit` by `[template, short_code]`; upsert the
  matching `question_ncs_iaudit` row for `NCT`/`Responsibility`/VSP fields.
- **`criteria` rows:** upsert into `criteria_iaudit`, scoped by `template`.
- **`text` rows:** upsert into `text_boxes_iaudit`, scoped by `template`.
- **`template` row:** read for metadata only if needed; nothing currently
  consumes it.
- **`Report` sheet:** skipped entirely — formula helper tab, not source data
  (unchanged from refactor-schema.md §3).

This replaces the 10-class `WorkbookImport` pipeline with one class, but
that class calls into the same four lookup models the old importers already
used — it's a new entry point, not a rewrite of what "department" or
"heading" mean.

**Re-import semantics, stated plainly:** this design defaults to
**upsert-by-`[template, short_code]`**, not delete-and-replace. That is a
real divergence from what the client has confirmed twice in
refactor-schema.md §3.2/§3.4 ("DELETE all existing questions and responses
... at the start of the import"). Flagging it here rather than quietly
picking one: upsert is less code, non-destructive, and safe by default;
delete-and-replace is what's actually been asked for. If delete-per-template
is confirmed as a hard requirement, it's a small wrapper around this same
importer (delete `questions_iaudit`/`criteria_iaudit`/`text_boxes_iaudit`
rows scoped to `template` before the upsert loop), not a different
architecture — so this choice doesn't need to block starting the migration
work, only the final import behaviour.

---

## 4. Mobile API impact — the part that matters most here

### 4.1 `GET /api/questions`

**No response shape change.** Same
`department → heading → subheading → category → questions[]` nesting the
app already parses. The only difference: the query path gets *simpler*, not
more complex — a single `QuestionIaudit::with(['heading','subHeading',
'category','ncs'])->where('is_active', true)->get()->groupBy('department_id')`
replaces the current `department → templates → questions (via reference_id)`
hop, fixing the roundabout join as a side effect.

Each question object gains new **optional** keys: `block_ref`, `text_icon`,
`vsp_item_no`, `point_loss`, `vsp_reference`, `vsp_description`,
`responsibility`, `consultant_remark`. Purely additive — an app build that
ignores unknown JSON keys needs zero changes to keep working.

### 4.2 `POST /api/answers`

**Zero change.** The validation rule (`exists:questions_iaudit,question_id`)
still points at the same column; only the *values* it validates against
change identity if the PK flips to auto-increment (§2.1), which only
matters at the one-time reseed the live-data check already gates
(refactor-schema.md §6).

This is the single biggest practical win of this approach over the full
redesign: **the mobile team reviews new optional fields, not a new
contract.**

---

## 5. Report controller impact

`AuditReportController::buildAuditData()` keeps the same grouping shape;
the `templates_iaudit` hop drops out the same way it does in
`ApiController::questions()`. New report sections read the six new
`question_ncs_iaudit` columns directly. `normalize()` — the fuzzy
text-matching shim that exists only because there was never a stable join
key between an answer and its question's report wording — can be deleted
outright rather than carried forward, because `short_code` is a real key
from day one under this plan too.

---

## 6. Effort, side by side

| | Full redesign (refactor-schema.md) | Minimal (this doc) |
|---|---|---|
| Tables retired | 9 | 2 |
| PK strategy | New surrogate table (`questions_iaudit_v2`) + rename/swap cutover + remap every historic answer | `question_id` column altered to auto-increment in place; remap only needed if live answers exist |
| Importer | 1 new class replacing 10, from scratch | 1 new class, calling 7 existing lookup models unchanged |
| `ApiController::questions()` | Full rewrite of the grouping logic | Same output; query path simplified |
| `AuditReportController` | Full rewrite | Same grouping reused; six new fields read in |
| Mobile app | New JSON shape — needs an app-side review pass regardless | Same JSON shape — additive fields only |
| Files meaningfully touched | ~12+ (migrations, 10 importer classes, 2 controllers, Blade, models) | ~6 (2 migrations, 1 model, 1 new importer, 2 controller query tweaks) |

---

## 7. What this approach deliberately leaves for later

- **Multi-template ship/audit mapping** — still open; only one template is
  live today, same as the full plan already accepts.
- **True single-flat-table ergonomics** — the DB still has 8 tables instead
  of 1. A future admin screen that edits one question as a single flat
  record still needs joins across `heading`/`subheading`/`category`/`ncs`.
  If that ever becomes a real requirement (rather than "the sheet looks
  flat"), that's the point to revisit the full collapse — not before.
- **Roles/permissions (§8) and mobile auto-update-on-submit (§9)** —
  untouched by either schema approach, unchanged from refactor-schema.md.

---

## 8. Still open regardless of which path is chosen

Carried over from refactor-schema.md §6, unchanged by this document:

- Live-data check — does `answers_iaudit` hold any real (non-seed) rows?
- Delete-vs-upsert re-import semantics (§3 above states the default; still
  needs client confirmation either way)
- How a template's name is identified in the file
- `Block_Ref` taxonomy: 11 values in the sheet vs. 7 in the client's own
  requirement notes
- Full meaning of `Responsibility`, `Consultant`, and the four `VSP_*`
  columns — `Point_Loss` in particular, since it drives the score

---

## 9. Recommendation

Start here. It fixes a real pre-existing bug (`department_id`) as a
byproduct rather than extra scope, gets `short_code` in place as the stable
join key immediately (letting `normalize()` be deleted rather than
maintained), and — most importantly for the current milestone — leaves the
mobile API contract intact aside from strictly additive fields. Escalate to
the full single-table redesign in refactor-schema.md only if a second
template or a genuine flat-record admin editor makes the remaining 7 tables
an actual problem, not a theoretical one.
