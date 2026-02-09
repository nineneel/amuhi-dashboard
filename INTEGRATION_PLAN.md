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

| Enum | Values |
|------|--------|
| `MemberType` | `PPUIPIHK`, `PT`, `PERSONAL` |
| `SubscriptionStatus` | `Unpaid`, `Pending`, `Active`, `Expired` |
| `InvoiceStatus` | `Pending`, `Paid`, `Overdue`, `Cancelled` |
| `PaymentStatus` | `Pending`, `Success`, `Failed`, `Refunded` |
| `EventStatus` | `Upcoming`, `Ongoing`, `Past`, `Cancelled` |

---

## 3. Authentication (Fully Custom)

### Routes
```
POST   /register          → RegisterController@store
GET    /login             → LoginController@showLoginForm
POST   /login             → LoginController@login
POST   /logout            → LoginController@logout
GET    /email/verify      → VerificationController@notice
GET    /email/verify/{id} → VerificationController@verify
POST   /email/resend      → VerificationController@resend
GET    /forgot-password   → ForgotPasswordController@showForm
POST   /forgot-password   → ForgotPasswordController@sendResetLink
GET    /reset-password    → ResetPasswordController@showForm
POST   /reset-password    → ResetPasswordController@reset
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
- Store secret in `users` table (encrypted column)
- Recovery codes stored in `user_settings`

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

| Feature | UI Template Reference |
|---------|----------------------|
| Login | `@ui-template/auth-login-cover.html` |
| Register | `@ui-template/auth-register-creative.html` |
| Dashboard | `@ui-template/analytics-*.html` or `@ui-template/index*.html` |
| Profile | `@ui-template/apps-contacts-*.html` or `@ui-template/settings-*.html` |
| Events | `@ui-template/apps-calendar.html` |
| Invoices | `@ui-template/invoices-*.html` |
| Settings | `@ui-template/settings-*.html` |

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

### Phase 1: Foundation
- [ ] Install approved packages (`barryvdh/laravel-dompdf`, `pragmarx/google2fa-laravel`)
- [ ] Create all migrations
- [ ] Create all models with relationships
- [ ] Create factories and seeders
- [ ] Create enums
- [ ] Setup Vite and copy assets from @ui-template
- [ ] Create base layouts (app, guest, partials)

### Phase 2: Authentication
- [ ] Register page (with member type selection)
- [ ] Login page
- [ ] Email verification flow
- [ ] Forgot/Reset password
- [ ] Two-factor authentication

### Phase 3: User Pages
- [ ] Profile page (view & edit)
- [ ] Settings page (all sections)
- [ ] Payment banner component (for unpaid users)

### Phase 4: Subscription & Payment (Demo Mode)
- [ ] Payment page (after email verification) - **demo/placeholder UI**
- [ ] Demo "Pay Now" button - simulates successful payment
- [ ] Subscription status management (manual toggle for testing)
- [ ] Seeder to create demo subscription plans

### Phase 5: Core Features
- [ ] Events list & calendar view
- [ ] Event registration (one-click)
- [ ] Invoice list & details
- [ ] Invoice PDF download
- [ ] Programs page (coming soon)

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

## Next Steps

1. ~~**Approve packages**~~ - **Done** (dompdf + google2fa approved)
2. **Start Phase 1** - Foundation (migrations, models, layouts)
3. **Review @ui-template** - Identify exact templates to use for each page
