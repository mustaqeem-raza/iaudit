---
paths:
  - "app/**/*.php"
  - "routes/**/*.php"
  - "database/**/*.php"
  - "config/**/*.php"
  - "bootstrap/**/*.php"
  - "tests/**/*.php"
---

# Backend rules

These add backend specifics to the principles in `CLAUDE.md`. They describe what the code does today. Follow them over general Laravel habits.

## Structure

- Laravel 12 skeleton: `bootstrap/app.php` registers routing, middleware and exception handling, and both of those closures are empty. There is no Kernel and no custom middleware.
- `app/` contains `Http/Controllers`, `Http/Requests` (Breeze only), `Imports`, `Models`, `Services`, `Providers` and `View/Components`, and nothing else. Don't add repositories, DTOs, Actions, API Resources, observers or enums unless the task asks for them.
- Routes are explicit (`Route::get(...)`; no `Route::resource`). Web routes are named `resource.action` (`audit.report.show`, `questions.import.store`); API routes are unnamed. Route parameters are a raw `{id}` resolved with `findOrFail`.
- Where logic goes:
  - Used once: a private method on the controller (`buildAuditData`, `renderPdf`).
  - Used in two places: a class in `app/Services`, resolved as `app(QuestionHierarchyService::class)`, or a model scope.
- `ApiController` holds six near-identical location-tree builders. Don't add a seventh copy, and don't merge the existing ones unless asked.

## Models

- Set `$table` on every model.
- Reference models (the `*_iaudit` lookups) set a custom `$primaryKey`, `$incrementing = false` and `$timestamps = false`. Transactional models use the default `id` and timestamps.
- New models declare `$fillable` only. Several existing models also carry `$guarded = []`; don't copy that.
- Name a new model after its neighbours (`…Iaudit`). Both `Iaudit` and `IAudit` suffixes exist; don't rename them.
- `User`, `Company`, `Fleet` and `Ship` map legacy tables. Those tables have `deleted_at`, but the models don't use `SoftDeletes`, so queries return soft-deleted rows.
- Type relationship return values in new code, as `Audit` does.

## Validation

- Validate inline with `$request->validate([...])`, rules as arrays, and `Rule::in([...])` for fixed sets. Every project endpoint does this.
- The only Form Requests are Breeze's. A Form Request is fine for a new admin form with many rules. Don't convert existing endpoints.
- An `exists:` rule on an array item (`answers.*.question_id`) runs one query per item. For a large array, check the ids with one `whereIn` query.

## Mobile API contract

- Never change the keys, types or nesting of an existing endpoint. An addition goes to a new versioned route, as `/api/v2/questions` did. The v1 endpoint keeps its own copy of the tree traversal on purpose.
- A new endpoint returns `{"success": true, "data": …}`, built as arrays with `map`, `groupBy` and `values`. An error returns `{"message": …}` with the right status code. Validation errors are Laravel's default 422 `{message, errors}`.
- The existing outliers stay as they are: `login`, `logout`, `companies` (a bare array), `answers` (201 with its own body), and the four endpoints that add `count`.
- Errors render as JSON only when the client sends `Accept: application/json`. Test with that header.

## Admin responses

- The admin panel has no JSON endpoints. An action is a form post that ends in `back()->with('success', …)` or `back()->with('error', …)`. A structured result goes in its own flash key (`importSummary`). Breeze code uses `status`.
- If a task needs a JSON endpoint for the panel, use the API shapes above.

## Queries

- Eloquent only. `app/` has no `DB::table` and no raw SQL. If raw SQL is unavoidable, bind its parameters.
- Eager-load with `with([...])`, nested where needed (`ship.fleet.company`, `answers.question`).
- Read questions through `QuestionHierarchyService::buildTree()` and the `questionRows()` and `active()` scopes on `QuestionIaudit`. A direct query usually misses either the legacy rows or the imported ones.
- Listings use `paginate(15)->withQueryString()`.
- Don't load a whole shared table. `AuditReportController::index` loads every user and ship; don't copy it.
- For many rows, use one `whereIn` update or delete, or `upsert`. The importers write row by row; don't extend that to larger data.

## Migrations and schema

- Change the schema only when the task requires it.
- Follow the `2026_08_29_*` migrations: an anonymous class, additive columns that are nullable or safely defaulted, a comment saying why, a working `down()`, and a file named `add_<x>_to_<table>_table`.
- A foreign key declared in a migration is not necessarily enforced. Legacy ids are `int unsigned`, reference keys are signed `bigint`, and some production tables were MyISAM. Check `SHOW CREATE TABLE` before relying on a constraint.
- Reference primary keys are hand-assigned (`max() + 1` in the importer). That is not safe for two imports at once.
- Index a new column that is used in `where`, a join or ordering.

## Transactions and errors

- Wrap multi-table writes in a transaction. `submitAudit` uses `DB::beginTransaction()`, `commit()` and `rollBack()` in a try/catch. Excel imports are already wrapped by `config/excel.php` (`transactions.handler` is `db`).
- Catch `Throwable`, log with context (`Log::error('…', ['exception' => $e])`), and give the user a generic message. Don't show exception or upstream response text; the `abort(502, …)` in `renderPdf` does, and it is not a pattern to copy.
- A stored file is not rolled back with the transaction. Store it after the commit, or delete it on failure.

## Auth

- The panel uses the `web` session guard (Breeze). The API uses Sanctum tokens, one new `mobile-{uuid}` token per login.
- `verified` passes for everyone, because `User` does not implement `MustVerifyEmail`. There are no roles, gates or policies: being logged in is the only check.
- Don't add a permission system unless asked. `refactor-schema.md` §8 holds the plan for one.

## Files and PDF

- Audit uploads use `$file->store("audits/{$audit->id}", 'public')`, with `path`, `url`, `original_name`, `size` and `mime` saved in the `files` JSON column.
- Imported workbooks are archived on the `local` disk under `question-imports/`.
- `renderPdf()` reads its settings from `config/services.php` (`pdfshift.key`, `browsershot.*`) and runs inside the request with a 120-second timeout.
- `barryvdh/laravel-dompdf` is installed and unused. Don't build on it.

## Excel imports

- Admin upload: `ExcelImportController` → `QuestionWorkbookImport` → `QuestionDataSheetImport` (`ToCollection`, `WithHeadingRow`).
  - It finds the sheet by a marker row, not by tab name.
  - It upserts by `[template, short_code]` so each `question_id` stays stable.
  - One row can write several tables. That is by design.
  - Rows dropped from the sheet are deleted or retired in `retireOrRemoveStaleTemplateData()`.
  - Read the class docblocks before changing it.
- Seeders: `WorkbookImport` and the per-sheet `ToModel` importers. No controller calls them.
- Heading keys are slugged (`config/excel.php`), so the `Short_Code` column arrives as `short_code`.

## Not in use

Jobs, queues, events, listeners, scheduled tasks, mail, notifications, cache calls and custom artisan commands do not exist here. Don't introduce one for a small task. If a task needs one, say so before building it.

## Tests

- Pest 3 on in-memory SQLite, with `RefreshDatabase` applied to `tests/Feature` in `tests/Pest.php`.
- Only stock Breeze tests exist, and Feature tests fail at migration time (see "Commands" in `CLAUDE.md`). A new feature test fails the same way until that migration is fixed. Write it if asked, and say it could not run.
