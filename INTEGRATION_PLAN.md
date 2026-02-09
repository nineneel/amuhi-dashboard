# Amuhi Dashboard - Integration Plan

This document outlines the technical implementation strategy for building the Amuhi Dashboard based on the requirements in `DASHBOARD_PLAN.md`.

---

## 1. Tech Stack & Dependencies

### Core Stack
- **PHP:** 8.3.20
- **Laravel:** 12
- **Database:** MySQL
- **Frontend:** Blade templates (from @ui-template)
- **Testing:** Pest 4
- **Code Style:** Laravel Pint

### Additional Packages

| Package | Purpose | Status |
|---------|---------|--------|
| `barryvdh/laravel-dompdf` | PDF generation for invoices | **Approved** |
| `pragmarx/google2fa-laravel` | Two-factor authentication | **Approved** |

### Payment Gateway
- **Provider:** [Yokke](https://www.yokke.co.id/id)
- **Integration:** Custom HTTP client (no official Laravel package)
- **Webhook:** Handle payment callbacks
- **Note:** Integration done in final phase - use demo/placeholder until then

---

## 2. Database Architecture

### Migration Order
Migrations must be created in this order to respect foreign key constraints:

```
1. users (exists)
2. user_profiles
3. user_settings
4. subscription_plans
5. subscriptions
6. invoices
7. payments
8. events
9. event_registrations
10. activity_logs
11. notifications
```

### Model Relationships

```
User
├── hasOne: UserProfile
├── hasOne: UserSettings
├── hasMany: Subscriptions
├── hasMany: Invoices
├── hasMany: EventRegistrations
├── hasMany: ActivityLogs
├── hasMany: Notifications

Subscription
├── belongsTo: User
├── belongsTo: SubscriptionPlan
├── hasMany: Invoices

Invoice
├── belongsTo: User
├── belongsTo: Subscription
├── hasMany: Payments

Event
├── hasMany: EventRegistrations

EventRegistration
├── belongsTo: User
├── belongsTo: Event
```

### Enums

| Enum | Values | Implementation |
|------|--------|----------------|
| `MemberType` | `PpuiPihk`, `Pt`, `Personal` | String-backed: `ppui_pihk`, `pt`, `personal` |
| `SubscriptionStatus` | `Unpaid`, `Pending`, `Active`, `Expired` | String-backed: `unpaid`, `pending`, `active`, `expired` |
| `InvoiceStatus` | `Pending`, `Paid`, `Overdue`, `Cancelled` | String-backed: `pending`, `paid`, `overdue`, `cancelled` |
| `PaymentStatus` | `Pending`, `Success`, `Failed`, `Refunded` | String-backed: `pending`, `success`, `failed`, `refunded` |
| `EventStatus` | `Upcoming`, `Ongoing`, `Past`, `Cancelled` | String-backed: `upcoming`, `ongoing`, `past`, `cancelled` |

**Note:** All enums are located in `app/` directory (not `app/Enums/`) and use string-backed enum pattern for database compatibility.

---

## 3. Authentication (Fully Custom)

### Routes
```
GET    /register                 → RegisterController@create
POST   /register                 → RegisterController@store
GET    /login                    → LoginController@create
POST   /login                    → LoginController@store
POST   /logout                   → LogoutController@__invoke
GET    /email/verify             → VerificationController@notice
GET    /email/verify/{id}/{hash} → VerificationController@verify
POST   /email/resend             → VerificationController@resend
GET    /forgot-password          → ForgotPasswordController@create
POST   /forgot-password          → ForgotPasswordController@store
GET    /reset-password/{token}   → ResetPasswordController@create
POST   /reset-password           → ResetPasswordController@store
GET    /two-factor/challenge     → TwoFactorController@challenge
POST   /two-factor/challenge     → TwoFactorController@verifyChallenge
GET    /two-factor               → TwoFactorController@show
POST   /two-factor/enable        → TwoFactorController@enable
POST   /two-factor/disable       → TwoFactorController@disable
```

### Controllers
```
app/Http/Controllers/Auth/
├── RegisterController.php
├── LoginController.php
├── LogoutController.php
├── VerificationController.php
├── ForgotPasswordController.php
├── ResetPasswordController.php
└── TwoFactorController.php
```

### Form Requests
```
app/Http/Requests/Auth/
├── RegisterRequest.php
├── LoginRequest.php
├── ForgotPasswordRequest.php
└── ResetPasswordRequest.php
```

### Two-Factor Authentication
- Use `pragmarx/google2fa-laravel` package
- Store secret (encrypted) in `users.two_factor_secret`, toggle via `users.two_factor_enabled`
- Recovery codes stored in `user_settings.two_factor_recovery_codes`
- Challenge route protects login flow; enable/disable managed under `/two-factor`

---

## 4. Frontend Integration

### Layout Structure
```
resources/views/
├── layouts/
│   ├── app.blade.php          → Main authenticated layout
│   ├── guest.blade.php        → Auth pages layout
│   └── partials/
│       ├── sidebar.blade.php
│       ├── header.blade.php
│       ├── footer.blade.php
│       └── notifications-dropdown.blade.php
├── auth/
│   ├── login.blade.php
│   ├── register.blade.php
│   ├── verify-email.blade.php
│   ├── forgot-password.blade.php
│   ├── reset-password.blade.php
│   └── two-factor.blade.php
├── dashboard/
│   └── index.blade.php
├── profile/
│   ├── index.blade.php
│   └── edit.blade.php
├── events/
│   ├── index.blade.php
│   ├── show.blade.php
│   └── calendar.blade.php
├── invoices/
│   ├── index.blade.php
│   └── show.blade.php
├── programs/
│   └── index.blade.php        → Coming soon page
├── settings/
│   ├── index.blade.php
│   ├── account.blade.php
│   ├── notifications.blade.php
│   ├── privacy.blade.php
│   ├── security.blade.php     → 2FA, change password
│   └── appearance.blade.php   → Theme, language
└── components/
    ├── payment-banner.blade.php
    ├── subscription-locked.blade.php
    └── ...
```

### Template Mapping (@ui-template → Blade)

| Feature | UI Template Reference | Status |
|---------|----------------------|--------|
| Login | `@ui-template/auth-login-minimal.html` | ✅ Done |
| Register | `@ui-template/auth-register-minimal.html` | ✅ Done |
| Forgot Password | `@ui-template/auth-reset-minimal.html` | ✅ Done |
| Reset Password | `@ui-template/auth-resetting-minimal.html` | ✅ Done |
| Verify Email | `@ui-template/auth-verify-minimal.html` | ✅ Done |
| 2FA Challenge | `@ui-template/auth-verify-minimal.html` | ✅ Done |
| Dashboard | `@ui-template/analytics-*.html` or `@ui-template/index*.html` | Pending |
| Profile | `@ui-template/apps-contacts-*.html` or `@ui-template/settings-*.html` | ✅ Done |
| Events | `@ui-template/apps-calendar.html` | Pending |
| Invoices | `@ui-template/invoices-*.html` | Pending |
| Settings | `@ui-template/settings-*.html` | ✅ Done |

**Note:** All auth pages use the `auth-minimal-wrapper` layout for a consistent centered card design.

### Asset Setup (Vite)
```javascript
// vite.config.js
export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.js',
            ],
            refresh: true,
        }),
    ],
});
```

Copy required assets from `@ui-template/assets/` to `resources/`:
- CSS files → `resources/css/`
- JS files → `resources/js/`
- Images → `public/images/`
- Vendor libraries → `public/vendors/`

### Dark Mode
- Store preference in `user_settings.theme` (`light`, `dark`, `system`)
- Apply via CSS class on `<html>` or `<body>`
- JavaScript toggle with localStorage fallback for guests

---

## 5. Middleware & Access Control

### Custom Middleware

| Middleware | Purpose | File |
|------------|---------|------|
| `EnsureEmailIsVerified` | Require verified email | `app/Http/Middleware/EnsureEmailIsVerified.php` |
| `EnsureSubscriptionActive` | Require paid subscription | `app/Http/Middleware/EnsureSubscriptionActive.php` |
| `RedirectIfAuthenticated` | Redirect logged users from guest pages | `app/Http/Middleware/RedirectIfAuthenticated.php` |

### Route Groups (bootstrap/app.php)

```php
->withMiddleware(function (Middleware $middleware) {
    $middleware->alias([
        'verified' => \App\Http\Middleware\EnsureEmailIsVerified::class,
        'subscribed' => \App\Http\Middleware\EnsureSubscriptionActive::class,
        'guest' => \App\Http\Middleware\RedirectIfAuthenticated::class,
    ]);
})
```

### Route Protection

```php
// routes/web.php

// Guest routes
Route::middleware('guest')->group(function () {
    Route::get('/login', ...);
    Route::get('/register', ...);
});

// Authenticated + Verified routes
Route::middleware(['auth', 'verified'])->group(function () {
    // Available to all verified users (paid or unpaid)
    Route::get('/dashboard', ...);
    Route::get('/profile', ...);
    Route::get('/settings', ...);

    // Requires active subscription
    Route::middleware('subscribed')->group(function () {
        Route::get('/events', ...);
        Route::get('/programs', ...);
        Route::get('/invoices', ...);
    });
});
```

---

## 6. Build Phases

### Phase 1: Foundation ✅ **COMPLETED**
- [x] Install approved packages (`barryvdh/laravel-dompdf`, `pragmarx/google2fa-laravel`)
- [x] Create all migrations
- [x] Create all models with relationships
- [x] Create factories and seeders
- [x] Create enums
- [x] Setup Vite and copy assets from @ui-template
- [x] Create base layouts (app, guest, partials)

**Completion Notes:**
- All 5 enums created with string-backed values
- 11 migrations created and successfully ran
- 10 models created with full Eloquent relationships
- User model updated with MustVerifyEmail and 2FA fields
- 8 factories created for testing
- SubscriptionPlanSeeder created with 2 demo plans (Annual: 500,000 / Monthly: 50,000)
- Assets copied from @ui-template to Laravel directories
- Base layouts created: app.blade.php, guest.blade.php, and 4 partials

### Phase 2: Authentication ✅ **COMPLETED**
- [x] Register page (with member type selection)
- [x] Login page
- [x] Email verification flow
- [x] Forgot/Reset password
- [x] Two-factor authentication
- [x] UI template styling applied (auth-minimal centered layout)

**Completion Notes:**
- All authentication routes, controllers, form requests, and middleware implemented
- All auth pages styled with UI template using `auth-minimal-wrapper` (centered card layout)
- Consistent design across all auth pages: login, register, forgot password, reset password, verify email, 2FA
- Logo positioned at top center of card
- Responsive layout with proper spacing and form validation feedback
- Laravel Pint formatting applied to all files

### Phase 3: User Pages ✅ **COMPLETED**
- [x] Profile page (view & edit)
- [x] Settings page (all sections)
- [x] Payment banner component (for unpaid users)

**Completion Notes:**
- Profile index + edit views built from template; hooked to user data, avatar/initial badge, plan badge, and action buttons.
- Settings shell with sidebar + all section stubs wired; sidebar full-height, single scroll, footer spacing optimized.
- Payment banner moved under header, full-width with flush margins; clickable CTA + status-aware messaging.

### Phase 4: Subscription & Payment (Demo Mode)
- [x] Payment page (after email verification) - **demo/placeholder UI**
- [x] Demo "Pay Now" button - simulates successful payment
- [x] Subscription status management (manual toggle for testing)
- [x] Seeder to create demo subscription plans

### Phase 5: Core Features
- [x] Events list & calendar view
- [x] Event registration (one-click)
- [x] Invoice list & details
- [x] Invoice PDF download
- [x] Programs page (coming soon)

### Phase 6: Dashboard & Polish
- [ ] Dashboard widgets (welcome, activity, events, quick actions)
- [ ] Activity logging
- [ ] Notifications (in-app & email)
- [ ] Dark mode toggle
- [ ] Final testing

### Phase 7: Yokke Payment Integration (Final)
- [ ] Yokke API integration
- [ ] Replace demo payment with real gateway
- [ ] Webhook handler for payment confirmation
- [ ] Production payment testing

---

## 7. Payment Gateway Integration (Yokke) - Phase 7

> **Note:** This integration is done in the final phase. Until then, use demo mode.

### Demo Mode (Phase 4)
During development, payment will work in demo mode:
- Payment page shows subscription plan details
- "Pay Now" button simulates successful payment
- Clicking "Pay Now" directly updates subscription to `active`
- No real payment processing
- Useful for testing the full user flow

```php
// Demo payment controller action
public function simulatePayment(Request $request): RedirectResponse
{
    $user = $request->user();
    $user->subscription()->update(['status' => SubscriptionStatus::Active]);

    return redirect()->route('dashboard')
        ->with('success', 'Payment successful! (Demo Mode)');
}
```

### Production Mode (Phase 7)

#### Configuration
```php
// config/services.php
'yokke' => [
    'merchant_id' => env('YOKKE_MERCHANT_ID'),
    'api_key' => env('YOKKE_API_KEY'),
    'api_secret' => env('YOKKE_API_SECRET'),
    'base_url' => env('YOKKE_BASE_URL', 'https://api.yokke.co.id'),
    'callback_url' => env('YOKKE_CALLBACK_URL'),
],
```

#### Service Class
```
app/Services/YokkePaymentService.php
```

Methods:
- `createTransaction(Invoice $invoice): array`
- `checkStatus(string $transactionId): array`
- `handleCallback(array $payload): void`

#### Webhook Route
```php
Route::post('/webhooks/yokke', [YokkeWebhookController::class, 'handle'])
    ->name('webhooks.yokke');
```

#### Payment Flow (Production)
1. User completes registration → Email verified
2. Redirect to payment page → Show subscription plan
3. User clicks "Pay Now" → Create Yokke transaction
4. Redirect to Yokke payment page
5. User completes payment
6. Yokke sends webhook → Update subscription to `active`
7. User redirected back → Full dashboard access

---

## 8. Testing Strategy (Pest)

### Test Files
```
tests/Feature/
├── Auth/
│   ├── RegistrationTest.php
│   ├── LoginTest.php
│   ├── EmailVerificationTest.php
│   ├── PasswordResetTest.php
│   └── TwoFactorTest.php
├── Profile/
│   └── ProfileTest.php
├── Settings/
│   └── SettingsTest.php
├── Events/
│   ├── EventListTest.php
│   └── EventRegistrationTest.php
├── Invoices/
│   ├── InvoiceListTest.php
│   └── InvoicePdfTest.php
├── Subscription/
│   ├── SubscriptionAccessTest.php
│   └── PaymentWebhookTest.php
└── Dashboard/
    └── DashboardTest.php
```

### Key Test Scenarios
- Registration creates user + profile + settings
- Unverified users cannot access dashboard
- Unpaid users cannot access events/invoices/programs
- Payment webhook updates subscription status
- Event registration adds to user's calendar
- Invoice PDF downloads correctly

---

## 9. File Structure Summary

```
app/
├── Enums/
│   ├── MemberType.php
│   ├── SubscriptionStatus.php
│   ├── InvoiceStatus.php
│   ├── PaymentStatus.php
│   └── EventStatus.php
├── Http/
│   ├── Controllers/
│   │   ├── Auth/
│   │   ├── DashboardController.php
│   │   ├── ProfileController.php
│   │   ├── SettingsController.php
│   │   ├── EventController.php
│   │   ├── InvoiceController.php
│   │   ├── ProgramController.php
│   │   ├── NotificationController.php
│   │   └── PaymentController.php
│   ├── Middleware/
│   │   ├── EnsureEmailIsVerified.php
│   │   ├── EnsureSubscriptionActive.php
│   │   └── RedirectIfAuthenticated.php
│   └── Requests/
│       ├── Auth/
│       ├── ProfileUpdateRequest.php
│       └── SettingsUpdateRequest.php
├── Models/
│   ├── User.php
│   ├── UserProfile.php
│   ├── UserSettings.php
│   ├── SubscriptionPlan.php
│   ├── Subscription.php
│   ├── Invoice.php
│   ├── Payment.php
│   ├── Event.php
│   ├── EventRegistration.php
│   ├── ActivityLog.php
│   └── Notification.php
├── Services/
│   ├── YokkePaymentService.php
│   └── ActivityLogService.php
└── Notifications/
    ├── EmailVerificationNotification.php
    ├── PasswordResetNotification.php
    ├── PaymentSuccessNotification.php
    └── EventReminderNotification.php
```

---

## Progress & Next Steps

### Completed
1. ~~**Approve packages**~~ - **Done** (dompdf + google2fa approved)
2. ~~**Phase 1: Foundation**~~ - **Done** (migrations, models, factories, seeders, layouts, assets)
3. ~~**Phase 2: Authentication**~~ - **Done** (routes, controllers, form requests, middleware, 2FA, UI template styling)
4. ~~**Phase 3: User Page**~~ - **Done** (profile page, setting page, dashboard reminder overlay)
5. ~~**Phase 4: Subscription (Demo)**~~ - **Done** (payment page, demo pay flow, subscription status toggle, plan seeder)
6. ~~**Phase 5: Core Features**~~ - **Done** (events list + calendar + registration, invoices list + details + PDF, programs page)

### To Do
1. **Phase 6: Dashboard & Polish** - Dashboard widgets, notifications, dark mode
2. **Phase 7: Payment Integration** - Yokke payment gateway integration

### Next Phase
**Phase 6: Dashboard & Polish** - Dashboard widgets, notifications, dark mode.
