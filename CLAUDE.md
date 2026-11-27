# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Overview

iAudit is a Laravel 12 (PHP ^8.2) system for shipboard IPM (Integrated Pest Management) inspections. It has two consumers:

- **Mobile app**: a Sanctum bearer-token JSON API (`routes/api.php` → `ApiController`). The app fetches question and location trees and submits completed audits.
- **Admin panel**: Breeze session auth with Blade views (`routes/web.php`). Admins list audits, view and download PDF reports, and upload the question-bank workbook.

Area rules load on their own when a matching file is read or edited. When planning work in an area before opening any file there, read its rule file first:

- [.claude/rules/backend.md](.claude/rules/backend.md): PHP in `app/`, `routes/`, `database/`, `config/`, `bootstrap/`, `tests/`.
- [.claude/rules/frontend.md](.claude/rules/frontend.md): Blade views in `resources/` and CSS in `public/css/`.

Reference docs:

- [AGENTS.md](AGENTS.md): route, model and migration tables, as of 2026-08-06. Where it disagrees with the code, the code is right.
- [schema-update-minimal-approach.md](schema-update-minimal-approach.md): the question-bank schema change that was implemented.
- [refactor-schema.md](refactor-schema.md): the fuller redesign, the planned roles work and the deployment checklist. Read it before touching `questions_iaudit`.

## Commands

```bash
composer install
php artisan serve                                # local server; there is no root .htaccess, so under WAMP use /iaudit/public or a vhost
php artisan route:list                           # run after touching routes/
composer test                                    # clears the config cache, then runs all tests (Pest 3, in-memory SQLite)
php artisan test --filter="user can delete"      # one test by name
vendor/bin/pest tests/Feature/ProfileTest.php    # one file
vendor/bin/pint --test app/Models/Audit.php      # style check of one file, changes nothing
php artisan db:seed --class=IpmTrapIAuditSeeder  # one reference-data seeder; read "Security and data safety" first
php artisan storage:link                         # uploaded audit files 404 without public/storage
```

- There is no frontend build step. Nothing to compile, no npm scripts.
- **The test suite is not a usable signal yet.** 24 of 25 tests fail (checked 2026-11-28). Every Feature test dies in `RefreshDatabase` because `2025_09_05_204143_create_questions_iaudit_table.php` declares a foreign key on `department_id`, a column it never creates. The tests are stock Breeze tests; none covers project code.
- Clear the config cache before running tests (`composer test` does). A cached config would point `RefreshDatabase` at the real MySQL database.

## Architecture

### Shared legacy database

- `users`, `ships`, `fleets`, `cruise_companies`, `personal_access_tokens`, `sessions`, `cache` and `jobs` belong to a larger database shared with another system. No migration here creates them, so `migrate` alone does not build a working schema.
- `users` has `first_name`, `last_name` and `title`, and no `name`. The Breeze register and profile code and `User::$fillable` still assume `name`.

### Two table families

- **Reference tables** (`departments_iaudit`, `headings_iaudit`, `sub_headings_iaudit`, `categories_iaudit`, `questions_iaudit`, `criteria_iaudit`, `text_boxes_iaudit`, `templates_iaudit`, `template_refs_iaudit`): hand-assigned, non-incrementing primary keys and no timestamps.
- **Transactional and location tables** (`audits`, `answers_iaudit`, the six trap/EFK tables): auto-increment `id` and timestamps.
- `question_ncs_iaudit` holds each question's non-compliance and report text. Keeping it apart from `questions_iaudit` is intentional.

### Two generations of questions

`questions_iaudit` holds two kinds of rows that are reached differently:

| | Legacy rows | Imported rows |
|---|---|---|
| Marker | `template IS NULL` | `template` set |
| Written by | `ExcelSeeder` → `WorkbookImport` | Admin upload: `ExcelImportController` → `QuestionDataSheetImport` |
| Reached via | `departments_iaudit` → `templates_iaudit` → `questions_iaudit.reference_id` | `questions_iaudit.department_id` |
| Natural key | none | `[template, short_code]` (unique) |

- `QuestionHierarchyService::buildTree($mapper)` merges both into a department → heading → subheading → category → questions tree. `/api/v2/questions` and the PDF report use it.
- `GET /api/questions` (v1) is frozen: the deployed mobile app depends on its exact response. Changes go to `/api/v2/questions`.

### Audit submission

- `POST /api/answers` (`ApiController::submitAudit`) creates one `Audit` and one `AnswerIaudit` per answer in a single transaction. Files go to the `public` disk under `audits/{audit_id}/`.
- Answers point at questions with `ON DELETE RESTRICT`. A question that has answers is retired (`is_active = false`), never deleted.

### PDF report

- `AuditReportController`: `buildAuditData($id)` → render `audit-pdf-report.blade.php` to HTML → `renderPdf()`. PDFShift renders it when `PDFSHIFT_API_KEY` is set (production has no Chromium); otherwise Browsershot renders it locally.
- The 1241×1755 viewport in `renderPdf()` must match `--page-w` / `--page-h` in `public/css/pdf-style.css`. Change both together.
- The report body is hardcoded HTML. Each answer cell is matched to a database question by normalized question text; a text that no longer matches renders "—" with no error.

## Development principles

These apply to every change, in every area.

1. **Root cause first.** Reproduce and trace the problem before editing. Fix the cause, not the symptom, and say what the cause was.
2. **Minimal changes.** Change only what the request needs. No unrelated refactoring, renaming, reformatting or dependency changes. Existing duplication and the known issues below stay unless the task is to fix them.
3. **Preserve functionality.** Don't change business logic, workflows, validations, permissions or UI behaviour as a side effect. The mobile API response shapes are a contract with a deployed app.
4. **Reusability (DRY, SOLID, KISS).** Before writing logic, look for an existing model scope, service method, importer or Blade component, and reuse or extend it. Logic needed in two places lives in one shared place. Keep methods small and single-purpose. Don't add layers the project doesn't have.
5. **Consistency.** Follow the patterns of the file and area you are in, as the rule files describe them, over general Laravel habits.
6. **Performance.** Eager-load relationships, keep queries out of loops, and don't reload or recompute what is already in hand. Not at the cost of readability.
7. **Regression prevention.** Before changing shared code, find its callers. Check both consumers (API and admin panel) and the PDF report, which depends on question text.
8. **No assumptions.** Read the code, the migration comments and the docs before deciding. A table split or double write that looks odd is usually intentional (see `questions_iaudit` and `question_ncs_iaudit`).
9. **Explain simply.** After a change, say what changed, why it was needed, and which files were affected.
10. **Verify.** Run the checks in "Verification" and report what was run, what was not, and any remaining risk.

## Conventions

- **Style follows the file.** Older code (most of `ApiController`, most models) is untyped with short comments. Newer code (`QuestionHierarchyService`, `QuestionDataSheetImport`, `ExcelImportController`) has typed signatures and docblocks that explain why. Match the file you edit; write new files in the newer style.
- **Don't reformat.** The code uses 4 spaces, PSR-12 braces and `=>` aligned in arrays. Pint's default preset undoes that alignment in most of `app/`, so never run Pint across the repo or over a whole existing file.
- **Comments say why**: a decision, a constraint, a gotcha. Not what the code already says.
- **No hardcoded config or data.** Table names, template identifiers, thresholds, URLs and limits come from constants, `config/` or the database. Call `env()` only inside `config/`.
- **Design for growth**: more rows, templates and ships than today's data.

## Security and data safety

- New web routes go inside the `['auth', 'verified']` group. New API routes go inside the `auth:sanctum` group.
- Never run `migrate:fresh`, `migrate:refresh`, `db:wipe` or `db:seed` against a database with real data unless asked. Six location seeders truncate their tables, and the legacy tables cannot be rebuilt from this repo.
- Don't alter or bulk-modify the legacy tables; another system uses them.
- `public/sql/iaudit.sql` is a full database dump inside the web root. Don't read its data rows, don't quote from it, and don't add more dumps.
- Known open issues. Don't copy them, don't fix them unless asked, and say so when a task touches them:
  - `GET /audits` is registered outside the auth group in `routes/web.php`.
  - Registration is open and there are no roles, so any logged-in user can run the question import.
  - `POST /api/login` has no rate limit, and Sanctum tokens never expire.

## Verification

The test suite cannot confirm a change today, so check directly and report which checks you ran:

- `php -l <file>` on every edited PHP file, and `php artisan route:list` after route changes.
- API change: call the endpoint with `Accept: application/json` and compare the JSON before and after.
- Admin change: load the page as a logged-in user and exercise the changed action.
- Report change: open `/audit/{id}/report` (HTML) and `/audit/{id}/pdf`.
- New tests are Pest feature tests under `tests/Feature`. If the migration failure above stops them running, say so.

## Git

- Commit and push only when asked.
- One logical change per commit. The subject is imperative and says what changed, as the recent commits do ("Add question import functionality with upsert logic…"), not "fixes".
- Don't commit `.env`, database dumps, or uploads under `storage/`. `package.json` and `package-lock.json` are gitignored on purpose: they only install Puppeteer for the local Browsershot fallback.
- There is no CI. Nothing checks a commit except the checks you run.
