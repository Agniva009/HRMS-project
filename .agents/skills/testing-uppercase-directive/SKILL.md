---
name: testing-uppercase-directive
description: Test the HRMS uppercase directive and IME composition handling. Use when verifying changes to the appUppercase Angular directive or backend uppercase middleware.
---

# Testing the Uppercase Directive

## Repo Structure

This repo contains **source files only** — no `package.json`, `angular.json`, `composer.json`, or build infrastructure. You cannot run `ng test`, `ng serve`, or `php artisan test` directly.

### Key Files
- **Frontend directive:** `frontend/src/app/shared/directives/uppercase.directive.ts`
- **Frontend tests (spec):** `frontend/src/app/shared/directives/uppercase.directive.spec.ts`
- **Frontend config (exceptions):** `frontend/src/app/shared/uppercase-config.ts`
- **Backend middleware:** `backend/app/Http/Middleware/UppercaseInput.php`
- **Backend helper:** `backend/app/Helpers/UppercaseHelper.php`
- **Backend config (exceptions):** `backend/config/uppercase.php`
- **Example form:** `frontend/src/app/modules/employee/components/employee-form.component.html`

## Testing Approach: Standalone HTML Test Harness

Since there's no runnable Angular app, create a standalone HTML file that replicates the exact algorithm from `uppercase.directive.ts`:

1. Copy the core logic (composing flag, input/compositionstart/compositionend event handlers, cursor preservation)
2. Create form fields tagged with `data-uppercase="true"` for uppercase fields
3. Create exception fields (email, username) without the uppercase logic
4. Add a **live status panel** showing raw `.value` for each field, cursor position, composing flag state, and last event name
5. Add **"Simulate IME Start"** and **"Simulate IME End"** buttons that programmatically fire `compositionstart`/`compositionend` events and toggle the `composing` flag

## Key Test Cases

1. **Auto-uppercase on input:** Type lowercase text in an uppercase field → raw `.value` should be uppercase (not CSS)
2. **Exception fields:** Type mixed-case text in email/username → raw `.value` preserves original casing
3. **IME composition suppression:** Click "Simulate IME Start" → type text → value stays lowercase (composing=true skips conversion)
4. **compositionend triggers conversion:** Click "Simulate IME End" → value uppercases immediately
5. **Cursor position:** Insert text mid-string → cursor stays at insertion point, doesn't jump to end

## Tips

- Always verify the **raw `.value`** (not just visual appearance) — CSS `text-transform` can fake uppercase visually
- The status panel makes assertions objective and screenshot-friendly
- IME composition can't easily be tested with real CJK input on a standard Linux VM — the programmatic simulation approach (dispatching compositionstart/compositionend events) is the same technique the Angular unit tests use
- No CI is configured on this repo — there are no checks to wait for
- No secrets or credentials are needed for testing

## Devin Secrets Needed

None — this repo has no authentication, database, or API requirements for testing.
