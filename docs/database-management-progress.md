# Database management layer: progress

Tracks delivery of the four-week plan for the database management module (Option A of the proposal).
Last updated: 2026-11-28.

## How to keep this file up to date

1. When a task is done, tick its box and add a dated line to the [Changelog](#changelog).
2. When a week is signed off, raise `weeks_done` in `config/database-management.php`. The Overview page (`/overview`) then marks that week's capabilities as Done.
3. When a "Coming soon" section gets its real screen, point its route in `routes/web.php` at the controller and remove its entry from `coming_soon` in `config/database-management.php`.
4. Record every decision in the [Decisions log](#decisions-log), and anything the client must answer under [Open questions](#open-questions-for-the-client).

## Sources

Outside the repo, in `C:\Users\musta\OneDrive\Desktop\iAudit\Revamp\`:

- `iAudit_Database_Management_Estimate.pdf`: the four-week delivery plan (22 November 2026, prepared for Graham Hobson).
- `iAudit Database Management.html`: the clickable prototype. The admin panel's look comes from it.
- `iAudit_Database_Management.docx`: the proposal (Option A integrated module, Option B standalone portal).

## Summary

| Week | Scope | Status | End-of-week check |
|---|---|---|---|
| 1 | Administration module and question management | **Done** (2026-11-28), not yet committed in full | Questions are created, edited and retired on screen ✔ |
| 2 | Import and export engine | Not started | A file is uploaded, mapped, validated and exported |
| 3 | Import preview, version control and rollback | Not started | An import is previewed, committed and rolled back |
| 4 | Audit trail, access control, testing and release | Not started | The complete module is live, tested and handed over |

## Week 1: administration module and question management ✔

From the plan:

- [x] **Database management section in the admin panel, as in the prototype.** The sidebar has a "Database management" group: Overview, Questions, Import & export, and Coming soon pages for Versions, Audit trail and Access.
- [x] **Question list with search, section filter and status filter.** At `/questions`, 15 per page. The filters stay in the URL and in the page links.
- [x] **Add and edit questions: section, wording, answer options, priority.** At `/questions/create` and `/questions/{id}/edit`, sharing one form.
  - Section is department, heading, subheading and category. Category is free text with suggestions; a new name creates a category.
  - New questions get the codes `NEW001`, `NEW002`…
- [x] **Retire, reactivate and permanently delete questions.** Delete is refused for a question that has answers. Otherwise it hides the question (`deleted_at`) instead of removing the row; see the [Decisions log](#decisions-log).
- [x] **End of week check: questions are created, edited and retired on screen.** Verified by an end-to-end run (28 checks) on 2026-11-28.

Scope notes from the plan:

- [x] **Submitted audits are protected.** Answered questions are retired, not deleted. Each answer also stores the wording it was answered against, so rewording a question never blanks a past report.
- [x] **On-screen editing covers audit questions only.** Templates, criteria and report text stay in the workbook import.

Built alongside Week 1:

- [x] The admin panel was rebuilt to the prototype's look: plain Blade HTML and one stylesheet, `public/css/admin.css`. Tailwind, Alpine, jQuery and DataTables were removed.
- [x] Every visible page uses the iAudit look, including login, password pages and error pages (404, 419, 500…). `/` redirects to the panel.
- [x] Overview page (`/overview`): live question-bank numbers and each capability's status.
- [x] Audits list: server-side search, status filter and paging over all audits. It used to show only the newest 15.
- [x] `/api/v2/questions` adds `answer_options` and `priority`, as additions only. `/api/questions` (v1) is unchanged, byte for byte.
- [x] `App\Services\QuestionReferenceData`: one place that looks up or creates departments, headings, subheadings and categories, and assigns new question ids. The workbook import and the question form both use it.
- [x] Database changes (migrations `2026_11_28_000001` and `2026_11_28_000002`, both additive):
  - `questions_iaudit` gains `answer_options`, `priority` and `deleted_at`.
  - `answers_iaudit` gains `question_text`, backfilled for existing answers.

Known limits carried into later weeks:

- **The workbook stays the master copy until Week 3.** Uploading it again brings back retired questions and hides questions that are not in it, including ones added on screen. The Questions page warns about this.
- **The mobile app doesn't use answer options or priority yet.** They are stored and sent by v2; app changes are out of scope.
- **Questions added on screen have no report (non-compliance) text** and don't appear in the hand-built PDF report.

## Week 2: import and export engine (not started)

- [ ] **Upload of CSV and Excel files, including the current workbook format.**
  - Exists today: the `.xlsx` workbook upload at `/questions/import` (`ExcelImportController` → `QuestionDataSheetImport`).
  - To do: CSV files, and files that aren't laid out like the workbook.
- [ ] **Field mapping:** columns matched automatically, adjustable by hand.
- [ ] **Row validation with clear error messages; invalid rows are skipped.**
  - Exists today: the workbook import lists skipped rows with a reason.
  - To do: the same for mapped CSV and Excel files.
- [ ] **Export of the live question set to CSV and Excel.**
- [ ] **End of week check:** a file is uploaded, mapped, validated and exported.

Notes:

- `maatwebsite/excel` is already installed.
- New rows must get ids from `QuestionReferenceData`, as the workbook import and the form do.
- The Import & export page shows a "Coming in Week 2" card.

## Week 3: import preview, version control and rollback (not started)

- [ ] **Dry run before saving,** with each row's status and before and after values.
- [ ] **Missing questions kept or retired; conflicts resolved row by row.**
  - Today the import decides on its own: missing questions with answers are retired, and missing questions without answers are hidden.
- [ ] **Every save and every import stored as a version.**
- [ ] **Version history:** compare any version with the live one and restore it.
- [ ] **End of week check:** an import is previewed, committed and rolled back.

Notes:

- This week ends the workbook overwrite described under Week 1.
- Restoring a version creates a new version and never overwrites history, as in the prototype.
- `/versions` shows Coming soon until then.

## Week 4: audit trail, access control, testing and release (not started)

- [ ] **Audit trail:** user, time, action, record, and before and after values.
- [ ] **Three roles (Administrator, Director, IPM Consultant), assigned per user.**
  - Use the permission table on the prototype's Access screen.
  - The shared database already has `roles` and `permissions` tables used by the other system. Don't install over them (see `refactor-schema.md` §8).
- [ ] **API updates: the mobile app receives only active questions.**
  - `/api/v2/questions` already returns active questions only.
  - `/api/questions` (v1) doesn't filter and can't see imported questions.
  - Depends on open question 1.
- [ ] **Regression testing, client testing, production release, admin guide.**
  - The automated tests can't run until the `department_id` migration bug is fixed.
- [ ] **End of week check:** the complete module is live, tested and handed over.

## Decisions log

| Date | Decision | Decided by |
|---|---|---|
| 2026-11-28 | **Whole admin panel restyled to the prototype:** teal, left sidebar, cards. | Project owner |
| 2026-11-28 | **Frontend is plain Blade HTML in the prototype's style, with one stylesheet.** No Tailwind, Alpine or Blade components. Repetition is handled by the layouts, `@include` and short loops. | Project owner |
| 2026-11-28 | **New questions get generated codes:** `NEW001`, `NEW002`… | Project owner |
| 2026-11-28 | **"Delete" hides a question (`deleted_at`) instead of removing the row.** Removing it would reject audits from phones that cached it, and could hand its id to a new question. | Developer, approved by project owner |
| 2026-11-28 | **The workbook may overwrite on-screen changes until Week 3.** A warning is shown instead of changing the importer. | Project owner |
| 2026-11-28 | **The question screens are open to any logged-in user until Week 4 brings roles.** | Project owner |
| 2026-11-28 | **Answer options and priority are stored and sent in v2.** The mobile app is unchanged. | Project owner |
| 2026-11-28 | **Each answer stores the wording it was answered against,** so past reports keep their answers. | Project owner |
| 2026-11-28 | **Category is free text with suggestions** and creates a new category if needed. Heading and subheading are picked from existing ones. | Developer |
| 2026-11-28 | **`/` redirects to the admin panel;** the Laravel welcome page was removed. | Developer |
| 2026-11-28 | **The Overview page keeps the prototype's layout with live content,** and is listed first under "Database management". | Project owner |
| 2026-11-28 | **An earlier, unfinished Week 1 attempt (git stash) was discarded** and its local migrations rolled back. Recoverable from commit `43c118a` while git keeps it. | Project owner |

## Open questions for the client

1. **Which question endpoint does the live mobile app call, v1 or v2?**
   - v1 can't see imported questions and ignores retired ones.
   - Week 4's "the app receives only active questions" depends on the answer.
2. **Is hiding a deleted question acceptable as "permanently delete"?** Removing the row would reject audits from phones that cached it.
3. **Should questions added on screen appear in the PDF report?**
   - The report is hand-built HTML, so new questions don't show in it.
   - Changing that is outside the four-week plan.
4. **When should `APP_NAME=iAudit` be set on the server?**
   - It sets the browser tab title.
   - Changing it signs every user out once.

## Known issues (not fixed; see CLAUDE.md)

- `GET /audits` works without logging in.
- Registration is open, and there are no roles until Week 4.
- `POST /api/login` has no rate limit, and tokens never expire.
- The Profile "Name" field is empty, because `users` has `first_name` and `last_name` but no `name`.
- The test suite fails at migration time: `2025_09_05_204143` declares a foreign key on a column it never creates.

## Release state

- **Branch:** `refactor-schema`.
- **Committed:** the admin panel redesign (commit `d2a61c9`).
- **Not committed yet:**
  - Week 1 question management
  - the plain-HTML cleanup
  - error pages and the `/` redirect
  - the Overview page
  - this file
- **Deploying Week 1:**
  1. Run `php artisan migrate`. There are two additive migrations; the answer-wording backfill is included.
  2. Run `php artisan view:clear` and `php artisan config:clear`.
  3. Set `APP_NAME=iAudit`.

## Changelog

- **2026-11-28** Admin panel rebuilt to the prototype: plain HTML and `admin.css`. Coming soon pages added for later sections (commit `d2a61c9`).
- **2026-11-28** Every page branded, including error pages and login. `/` redirects to the panel. Unused Breeze components removed.
- **2026-11-28** Week 1 done: question list, add, edit, retire, reactivate and hidden delete. Two migrations. v2 gains `answer_options` and `priority`. Answers keep their wording for reports.
- **2026-11-28** Blade components replaced with plain HTML. Repetition moved into the layouts, `@include` files and loops.
- **2026-11-28** Overview page added. `config/coming-soon.php` merged into `config/database-management.php`. This tracker created.
