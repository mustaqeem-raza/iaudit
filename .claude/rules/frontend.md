---
paths:
  - "resources/**"
  - "public/css/**"
  - "public/js/**"
---

# Frontend rules

These add frontend specifics to the principles in `CLAUDE.md`. Keep existing classes, ids and markup unless the requested change needs them altered.

## Stack

- Admin views are plain HTML in Blade, styled by one hand-written stylesheet, `public/css/admin.css`. Its look and class names come from the approved mockup (`Revamp/iAudit Database Management.html`, outside the repo). A page should read like the mockup's HTML.
- No Tailwind, Alpine, axios, jQuery or DataTables, and no build step: no Vite, no npm scripts, no `resources/js` or `resources/css`. Don't add a CSS framework or a JS library.
- Fonts are Inter (body) and Space Grotesk (headings), loaded from Google Fonts in `layouts/head.blade.php`.

## Simple and not repeated

Pages are plain HTML anyone can read, and nothing is written twice. Repetition is removed with the simplest tool that works, in this order:

1. **The layout does it once.** The page header, the browser tab title, success/error flashes, validation errors and the delete confirmation are all handled by the layouts. Pages don't print them.
2. **`@include` a plain HTML file** for a block used in more than one place: `layouts/head.blade.php`, `layouts/brand.blade.php`, `layouts/messages.blade.php`. Pass a variable only when needed (`@include('layouts.brand', ['href' => route('dashboard')])`).
3. **A loop over a short list** where items repeat in one place, with the list at the top of the same file (the sidebar's `$sections`), or a constant / config value (`QuestionIaudit::PRIORITIES`, `config/database-management.php`).

Don't write Blade components with props or slots (`resources/views/components` is gone on purpose): they hide the HTML in another file. The only components are the two layouts.

## Layouts

- A logged-in page is `<x-app-layout title="…" subtitle="…">`; an auth page is `<x-guest-layout>`. `title` and `subtitle` are plain attributes (bind with `:title="…"` for a PHP value); the layout draws the teal header and the tab title from them. `@extends`, `@section`, `@stack` and `@push` are not used, except in `errors/minimal.blade.php` (below).
- `layouts/head.blade.php` is the one `<head>`, used by both layouts. It links `admin.css` with a `?v=filemtime` query so browsers pick up edits. The tab title comes from `config('app.name')`, so set `APP_NAME=iAudit` in `.env`. There is no CSRF meta tag; forms carry `@csrf`.
- `layouts/messages.blade.php`, included by both layouts, shows `session('success')`, `session('error')` and every validation error from every error bag. The guest layout also shows a `session('status')` sentence (Breeze password reset). Pages show only their own special messages (profile's "Saved.", verify-email's link-sent notice).
- `errors/minimal.blade.php` replaces Laravel's error layout, so every error page (401–503) has the iAudit look inside the guest layout. It uses `@yield('code')` / `@yield('message')` because Laravel's error views `@extends('errors::minimal')`.
- `/` redirects to `/dashboard`; there is no welcome page.
- Page shell:

  ```blade
  <x-app-layout title="Questions" subtitle="Create, edit, retire">
      <section class="card">
          <h2>Card title</h2>
          <p class="sub">Short description.</p>
          …
      </section>
  </x-app-layout>
  ```

- The sidebar is `layouts/navigation.blade.php`. To add a section, add one line to `$sections` at the top and one `<symbol id="icon-…">` to the icon list under it. A `['heading' => '…']` line prints a group title (`.nav-group`), as for "Database management". Under 900px wide the CSS turns the sidebar into a scrolling row; there is no hamburger menu.
- A section that isn't built yet points its route at `coming-soon.blade.php` with `Route::view(...)`, taking its text from `coming_soon` in `config/database-management.php`. When the real screen lands, point the route at its controller and remove the config entry, then update `docs/database-management-progress.md`.
- The Overview page (`overview.blade.php`) follows the prototype's first screen. Its capability list and `weeks_done` come from `config/database-management.php`; raise `weeks_done` when a week is signed off.

## CSS classes (from `admin.css`)

- Layout: `.card`, `.stack` (vertical gap), `.row` (horizontal, wraps), `.spread` (space-between), `.actions` (right-aligned buttons), `.form` (two-column form grid, `.wide` spans both), `.measure` (narrow form column), `.grow`, `.filters`.
- Text: `.sub` (muted), `.mono`, `.num`, `.list`.
- Buttons: `.btn`, plus `.primary`, `.danger`, `.sm`, `.link`. A link that looks like a button is `<a class="btn">`.
- Badges: `.pill`, plus `.good`, `.warn`, `.bad`, `.teal`, `.lav`. Use `@class([...])` when the variant depends on data.
- Messages: `.flash.good`, `.flash.bad`, `.note` (warning box).
- Tables: `.tablewrap > table.data`, `td.q` for long text, `tr.retired`, `td.empty` for the empty-state row, `.acts` for row buttons (kept on one line; forms inside it are laid out inline).
- Overview: `.hero`, `.lead`, `.try` with a `<dl class="stats">`, `.grid2`, `.opt` / `.opt.rec`, `.trade`, `table.cmp`, and the `.arch` diagram (`.cols`, `.tier`, `.tier.dash`, `.arrow`).
- Paging: `{{ $paginator->links() }}` renders `resources/views/pagination.blade.php` ("Showing 1 to 15 of 80" and the page numbers; set in `AppServiceProvider`). Pages don't write their own count line.
- Colours, fonts and corner radii are CSS variables at the top of `admin.css` (one dark-mode block follows the system setting). Use a variable, never a raw value, in new rules. Add a class to `admin.css` rather than an inline `style`. The file starts with a contents list; put new rules in the matching section.

## Forms

- A field is a label wrapping its control, as in the mockup. No `id`/`for` pair is needed:

  ```blade
  <label class="field">Email
      <input name="email" type="email" value="{{ old('email') }}" required>
  </label>
  ```

  The same works for `<select>` and `<textarea>`. Add `wide` (`class="field wide"`) to span both columns of a `.form` grid.
- Validation errors are listed once, at the top of the page, by the layout. Don't add `@error` blocks under fields.
- Every form has `@csrf`. Verbs other than POST use `@method(...)`. Repopulate with `old()`.
- Client-side validation is HTML5 attributes only (`required`, `type`, `minlength`, `accept`). The server decides.
- Add and edit are their own pages, not pop-ups; one view can serve both, as `questions/form.blade.php` does.
- A destructive form asks first by carrying the message: `<form … data-confirm="Delete question {{ $code }}?">`. The app layout's one script shows the confirm box.
- Filters on a list page are a `GET` form, so they stay in the URL and work with paging.

## JavaScript

- The only script is in `layouts/app.blade.php`: it shows the confirm box for any form with `data-confirm`. Pages have none.
- If a page truly needs more, write a small inline vanilla `<script>` at the end of that page and say why in a comment. Don't add a library.
- The panel has no AJAX: every action is a form post followed by a redirect and a flash message.

## Performance

- No queries or lazy-loaded relationships in Blade. The controller passes eager-loaded data.
- Encode a base64 image once into a variable and reuse it, as `audit-pdf-report.blade.php` does.

## PDF report views

`audit-pdf-report.blade.php` (live) and `static-audit-pdf-report.blade.php` (the design mockup) are standalone HTML documents, not layout pages, and don't use `admin.css`.

- Edit the live view. Touch the mockup only if asked.
- JavaScript does not run when the PDF is rendered, so no script-driven layout. All styling is `public/css/pdf-style.css`, inlined with `file_get_contents`.
- Each sheet is one `<section class="page">` with a fixed aspect ratio and `overflow: hidden`. Pagination is manual: content that doesn't fit is clipped, not moved to the next page. There are no `@page` or `page-break` rules.
- Sizes use `clamp(min, Nvw, max)` against the fixed viewport described in `CLAUDE.md`.
- Keep the constraints noted in the CSS comments: font weight at most 700, no `mix-blend-mode`, and an Arial-first font stack.
- Answer cells come from the `$answerCell('question text')` closure at the top of the live view. The string must match the database `question_text` after normalization. The normalizer is duplicated in `AuditReportController::buildAuditData()`: change both or neither.
- `{!! !!}` is used only for helpers that escape their own output with `e()`. A new helper that returns HTML must do the same.
- After any change, check `/audit/{id}/report` and `/audit/{id}/pdf`.

## Escaping and accessibility

- Output with `{{ }}`. The admin views contain no `{!! !!}`.
- Every input has a label (wrapping it, or `aria-label` for search boxes and filter selects), `aria-hidden="true"` on decorative SVG icons, `role="status"` / `role="alert"` on flash messages.
- Admin pages target current evergreen browsers. The PDF renders in Chromium (PDFShift or Browsershot).

## Known gaps in the views

- The profile and register forms still use a `name` field, but `users` has `first_name` and `last_name`. The sidebar shows `first_name last_name`.
