# HRMS — Global Uppercase Input Handling System

A **DRY, scalable** input-handling system that enforces UPPERCASE on all user-entered textual data across the HRMS application. The system spans the **Angular frontend**, the **Laravel backend**, and the **database**, while explicitly excluding case-sensitive fields.

---

## Architecture Overview

```
┌─────────────────────────────────────────────────────────────────┐
│                        ANGULAR FRONTEND                         │
│                                                                 │
│  ┌───────────────────────────────────────────────────────────┐  │
│  │  appUppercase Directive  (shared/directives/)             │  │
│  │  • Listens to input & paste events                        │  │
│  │  • Converts value to UPPERCASE in real-time               │  │
│  │  • Preserves cursor position                              │  │
│  │  • Defers conversion during IME composition (CJK input)   │  │
│  │  • Works with Reactive Forms, Template-driven, and plain  │  │
│  └───────────────────────────────────────────────────────────┘  │
│                                                                 │
│  Exception: Fields WITHOUT the directive are left untouched     │
│  (email, username, password, URLs, system IDs)                  │
└─────────────────────────────────────────────────────────────────┘
                              │
                              ▼  HTTP Request
┌─────────────────────────────────────────────────────────────────┐
│                        LARAVEL BACKEND                          │
│                                                                 │
│  ┌───────────────────────────────────────────────────────────┐  │
│  │  UppercaseInput Middleware  (Http/Middleware/)             │  │
│  │  • Runs on EVERY request (registered globally in Kernel)  │  │
│  │  • Reads exception list from config/uppercase.php         │  │
│  │  • Recursively uppercases string fields, skips exceptions │  │
│  │  • Handles nested / array payloads                        │  │
│  └───────────────────────────────────────────────────────────┘  │
│                                                                 │
│  ┌───────────────────────────────────────────────────────────┐  │
│  │  UppercaseHelper  (Helpers/)                              │  │
│  │  • Static methods for ad-hoc / Eloquent mutator use       │  │
│  │  • UppercaseHelper::convert($value)                       │  │
│  │  • UppercaseHelper::convertArray($data, $except)          │  │
│  └───────────────────────────────────────────────────────────┘  │
│                                                                 │
│  ┌───────────────────────────────────────────────────────────┐  │
│  │  StoreEmployeeRequest  (Http/Requests/)                   │  │
│  │  • Example FormRequest — validation runs AFTER middleware │  │
│  │  • No extra uppercase logic needed in request classes     │  │
│  └───────────────────────────────────────────────────────────┘  │
└─────────────────────────────────────────────────────────────────┘
                              │
                              ▼  Persist
┌─────────────────────────────────────────────────────────────────┐
│                          DATABASE                               │
│                                                                 │
│  One-time migration: UPPER() on all existing text columns       │
│  (employees, departments, designations)                         │
│  Exception columns (email, username, password) are excluded.    │
└─────────────────────────────────────────────────────────────────┘
```

---

## File Structure

```
HRMS-project/
├── frontend/
│   └── src/app/
│       ├── shared/
│       │   ├── directives/
│       │   │   ├── uppercase.directive.ts        ← Reusable directive
│       │   │   └── uppercase.directive.spec.ts   ← Unit tests
│       │   ├── uppercase-config.ts               ← Exception field list
│       │   └── shared.module.ts                  ← Exports directive
│       └── modules/employee/
│           ├── components/
│           │   ├── employee-form.component.ts    ← Example form
│           │   └── employee-form.component.html  ← Template with directive usage
│           └── employee.module.ts
│
├── backend/
│   ├── app/
│   │   ├── Helpers/
│   │   │   └── UppercaseHelper.php               ← Static helper class
│   │   └── Http/
│   │       ├── Controllers/
│   │       │   └── EmployeeController.php        ← Example controller
│   │       ├── Kernel.php                        ← Middleware registration
│   │       ├── Middleware/
│   │       │   └── UppercaseInput.php            ← Global middleware
│   │       └── Requests/
│   │           └── StoreEmployeeRequest.php      ← Example form request
│   ├── config/
│   │   └── uppercase.php                         ← Centralized exception list
│   ├── database/migrations/
│   │   └── 2026_05_21_..._normalize_existing_records_to_uppercase.php
│   ├── routes/
│   │   └── api.php                               ← Example API routes
│   └── tests/Unit/
│       ├── UppercaseHelperTest.php               ← Helper unit tests
│       └── UppercaseInputMiddlewareTest.php      ← Middleware unit tests
│
└── README.md
```

---

## Quick Start

### Frontend (Angular)

1. **Import `SharedModule`** into any feature module that needs uppercase inputs:

   ```typescript
   import { SharedModule } from '../../shared/shared.module';

   @NgModule({
     imports: [SharedModule],
   })
   export class EmployeeModule {}
   ```

2. **Add `appUppercase`** to any input or textarea that should auto-uppercase:

   ```html
   <input formControlName="employeeName" appUppercase />
   ```

3. **Omit the directive** on exception fields (email, password, etc.):

   ```html
   <input formControlName="email" />   <!-- no appUppercase -->
   ```

### Backend (Laravel)

1. **Register the middleware globally** in `app/Http/Kernel.php`:

   ```php
   protected $middleware = [
       \App\Http\Middleware\UppercaseInput::class,
   ];
   ```

2. **Publish `config/uppercase.php`** and add/remove exception fields as needed.

3. **Run the migration** to normalize existing data:

   ```bash
   php artisan migrate
   ```

4. **Use `UppercaseHelper`** in Eloquent mutators for extra safety:

   ```php
   use App\Helpers\UppercaseHelper;

   public function setNameAttribute(string $value): void
   {
       $this->attributes['name'] = UppercaseHelper::convert($value);
   }
   ```

---

## Exception Fields

The following fields are **never uppercased** (configured in one place):

| Field               | Reason                            |
|---------------------|-----------------------------------|
| `email`             | Case-sensitive by RFC             |
| `username`          | May be case-sensitive             |
| `password`          | Must preserve original casing     |
| `url` / `website`   | URLs are case-sensitive           |
| `token` / `api_key` | System-generated, format-sensitive|
| `_token` / `_method`| Laravel CSRF & method spoofing    |

Add new exceptions to:
- **Frontend**: `frontend/src/app/shared/uppercase-config.ts`
- **Backend**: `backend/config/uppercase.php`

---

## Edit-Mode Handling

- Existing records are already uppercase (via migration + middleware on prior saves).
- When a user edits a record, the `appUppercase` directive enforces uppercase on any modification in real-time.
- The middleware re-validates on the backend, so even programmatic edits are caught.

---

## Testing

### Frontend

```bash
cd frontend
ng test   # Runs Jasmine/Karma specs for the directive
```

Tests cover:
- Mixed-case → UPPERCASE conversion
- Cursor position preservation
- Exception fields remain unchanged
- Empty / numeric / special character inputs
- Reactive forms, template-driven, and plain elements
- IME composition: no conversion during `compositionstart`→`compositionend`
- Post-composition: normal uppercasing resumes after `compositionend`

### Backend

```bash
cd backend
php artisan test --filter=Uppercase
# or
vendor/bin/phpunit tests/Unit/UppercaseHelperTest.php
vendor/bin/phpunit tests/Unit/UppercaseInputMiddlewareTest.php
```

Tests cover:
- `UppercaseHelper::convert()` — strings, null, unicode
- `UppercaseHelper::convertArray()` — nested data, exception keys
- `UppercaseInput` middleware — regular fields, exception fields, nested payloads, non-string values

---

## Design Principles

| Principle          | Implementation                                              |
|--------------------|-------------------------------------------------------------|
| **DRY**            | Single directive, single middleware, single config per layer |
| **Never trust frontend** | Backend middleware enforces uppercase independently    |
| **Scalable**       | Add a field name to the config to exempt; add `appUppercase` to a template to opt-in |
| **Cursor-safe**    | Directive saves and restores `selectionStart` / `selectionEnd` |
| **IME-safe**       | Conversion deferred during composition; applied on `compositionend` |
| **UTF-8 safe**     | `mb_strtoupper()` on backend, native `toUpperCase()` on frontend |
| **ERP-ready**      | Modular structure supports unlimited modules and forms      |
