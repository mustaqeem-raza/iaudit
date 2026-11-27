---
paths:
  - "resources/**"
  - "public/css/**"
  - "public/js/**"
---

# Frontend rules

These add frontend specifics to the principles in `CLAUDE.md`. Keep existing classes, ids and markup unless the requested change needs them altered.

## Stack

- Admin views are plain HTML in Blade, styled by one hand-written stylesheet, `public/css/admin.css`. Its look and class names come from the approved mockup (`Revamp/iAudit Database Management.html`, outside the repo).
- No Tailwind, Alpine, axios, jQuery or DataTables, and no build step: no Vite, no npm scripts, no `resources/js` or `resources/css`. Don't add a CSS framework or a JS library. Keep pages simple enough to read without one.
- Fonts are Inter (body) and Space Grotesk (headings), loaded from Google Fonts in the layouts.

## Layouts

- A logged-in page is `<x-app-layout>`; an auth page is `<x-guest-layout>`. Fill them with `{{ $slot }}` and, on app pages, `<x-slot name="header">`. `@extends`, `@section`, `@stack` and `@push` are not used.
- The `<head>` block is duplicated in `layouts/app.blade.php` and `layouts/guest.blade.php`. A change to one goes in both. `admin.css` is linked with a `?v=filemtime` query so browsers pick up edits.
- `layouts/app.blade.php` is the shell: sidebar (`layouts/navigation.blade.php`), the teal header, the `session('success')` / `session('error')` flash, then the page. Pages don't print those two flashes themselves.
- Page shell:

  ```blade
  <x-app-layout>
      <x-slot name="header">
          <div>
              <h1>Title</h1>
              <small>One-line subtitle</small>
          </div>
      </x-slot>

      <section class="card">
          <h2>Card title</h2>
          <p class="sub">Short description.</p>
          …
      </section>
  </x-app-layout>
  ```

- A sidebar entry is one `<a class="nav-btn">` in `layouts/navigation.blade.php` with an inline SVG icon and `@if (request()->routeIs('…')) aria-current="page" @endif`. Under 900px wide the CSS turns the sidebar into a scrolling row; there is no hamburger menu.
- A section that isn't built yet points its route at `coming-soon.blade.php` with `Route::view(...)`, taking its text from `config/coming-soon.php`. When the real screen lands, point the route at its controller and remove the config entry.

## CSS classes (from `admin.css`)

- Layout: `.card`, `.stack` (vertical gap), `.row` (horizontal, wraps), `.spread` (space-between), `.actions` (right-aligned buttons), `.grid2`, `.measure` (narrow form column), `.grow`, `.filters`, `.count`.
- Text: `.sub` (muted), `.mono`, `.num`, `.list`.
- Buttons: `.btn`, plus `.primary`, `.danger`, `.sm`, `.link`. A link that looks like a button is `<a class="btn">`.
- Badges: `.pill`, plus `.good`, `.warn`, `.bad`, `.teal`, `.lav`. Use `@class([...])` when the variant depends on data.
- Messages: `.flash.good`, `.flash.bad`, `.note` (warning box), `.banner`, `.err` (field error).
- Tables: `.tablewrap > table.data`, `td.q` for long text, `tr.retired`, `td.empty` for the empty-state row, `.acts` for row buttons (kept on one line). Pagination is `{{ $paginator->links() }}`; `AppServiceProvider` sets Laravel's plain `pagination::default` view, styled by `.pagination`.
- Colours are CSS variables at the top of `admin.css` (light and dark). Use a variable, never a hex value, in new rules. Add a class to `admin.css` rather than an inline `style`.

## Forms

- Each field is a `.field` block:

  ```blade
  <div class="field">
      <label for="email">Email</label>
      <input id="email" name="email" type="email" value="{{ old('email') }}" required>
      @error('email')
          <p class="err">{{ $message }}</p>
      @enderror
  </div>
  ```

  For a named error bag: `@error('password', 'updatePassword')`.
- Every form has `@csrf`. Verbs other than POST use `@method(...)`. Repopulate with `old()`.
- Client-side validation is HTML5 attributes only (`required`, `type`, `accept`). The server decides.
- Add and edit are their own pages, not pop-ups. A destructive action asks with the browser's confirm box: `<form … onsubmit="return confirm('…');">`.
- Filters on a list page are a `GET` form, so they stay in the URL and work with paging.

## JavaScript

- None by default. The only script in the admin views is the inline `onsubmit="return confirm(…)"` on destructive forms.
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
- `for` and `id` on every label and input, `aria-label` on inputs without a visible label (search boxes, filter selects), `aria-hidden="true"` on decorative SVG icons, `role="status"` / `role="alert"` on flash messages.
- Admin pages target current evergreen browsers. The PDF renders in Chromium (PDFShift or Browsershot).

## Not in use

No admin page uses these any more. Don't build on them; deleting them is a separate cleanup:

- The Breeze components in `resources/views/components` (`x-text-input`, `x-primary-button`, `x-modal`, `x-dropdown`, `x-nav-link`…).
- `dashboard.blade.php`, which is orphaned and links to a route that no longer exists.

## Known gaps in the views

- The profile and register forms still use a `name` field, but `users` has `first_name` and `last_name`. The sidebar shows `first_name last_name`.
