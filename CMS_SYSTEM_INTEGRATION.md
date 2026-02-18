# CMS System Integration - Todo List & Progress Tracker

> **Reference**: See `CMS_SYSTEM_PLAN.md` for full implementation details

## Progress Overview

| Phase | Description | Status | Progress |
|-------|-------------|--------|----------|
| Phase 1 | Foundation (Role System & Admin Auth) | 🟢 Completed | 100% |
| Phase 2 | Admin Layout & Navigation | 🟢 Completed | 100% |
| Phase 3 | CMS Content Models | 🟢 Completed | 100% |
| Phase 4 | Admin Controllers | 🟢 Completed | 100% |
| Phase 5 | Admin Views | 🟢 Completed | 100% |
| Phase 6 | API for Marketing Website | 🔴 Not Started | 0% |

**Legend**: 🔴 Not Started | 🟡 In Progress | 🟢 Completed | ⏸️ Blocked

---

## Phase 1: Foundation (Role System & Admin Auth)

### 1.1 Role Enum & Migration

- [x] **Create Role enum**
  - File: `app/Enums/Role.php`
  - Values: `SuperAdmin`, `Admin`, `Member`
  - Test: Enum values accessible via `Role::SuperAdmin->value`

- [x] **Create migration for role column**
  - File: `database/migrations/xxxx_add_role_to_users_table.php`
  - Column: `role` string, default `'member'`
  - Run: `php artisan migrate`

- [x] **Update User model**
  - File: `app/Models/User.php`
  - Add `'role'` to `$fillable`
  - Add cast: `'role' => Role::class`
  - Add method: `isSuperAdmin(): bool`
  - Add method: `isAdmin(): bool` (returns true for admin OR super_admin)
  - Add method: `isMember(): bool`

- [x] **Create UserFactory state for roles**
  - File: `database/factories/UserFactory.php`
  - Add state: `->superAdmin()`
  - Add state: `->admin()`

### 1.2 Admin Middleware

- [x] **Create EnsureAdminRole middleware**
  - File: `app/Http/Middleware/EnsureAdminRole.php`
  - Logic: Check `auth()->user()->isAdmin()`
  - Redirect: To `route('dashboard')` if not admin
  - Abort: 403 if unauthenticated

- [x] **Create EnsureSuperAdminRole middleware**
  - File: `app/Http/Middleware/EnsureSuperAdminRole.php`
  - Logic: Check `auth()->user()->isSuperAdmin()`
  - Redirect: To `route('admin.dashboard')` if admin but not super
  - Abort: 403 if not admin at all

- [x] **Register middlewares in bootstrap/app.php**
  - Alias: `'admin' => EnsureAdminRole::class`
  - Alias: `'super_admin' => EnsureSuperAdminRole::class`

### 1.3 Admin Routes

- [x] **Create admin routes file**
  - File: `routes/admin.php`
  - Define route groups with middleware

- [x] **Register admin routes in bootstrap/app.php**
  - Load: `routes/admin.php`
  - Prefix: `/admin`

- [x] **Define initial routes**
  ```
  Guest routes (middleware: guest):
  - GET  /admin/login          → Admin\Auth\LoginController@create
  - POST /admin/login          → Admin\Auth\LoginController@store

  Auth routes (middleware: auth, admin):
  - POST /admin/logout         → Admin\Auth\LoginController@destroy
  - GET  /admin                → Admin\DashboardController@index
  - GET  /admin/dashboard      → Admin\DashboardController@index

  SuperAdmin routes (middleware: auth, super_admin):
  - Resource /admin/admins     → Admin\AdminController
  ```

### 1.4 Admin Auth Controllers

- [x] **Create Admin LoginController**
  - File: `app/Http/Controllers/Admin/Auth/LoginController.php`
  - Method: `create()` - Show admin login form
  - Method: `store()` - Handle login (check if user is admin)
  - Method: `destroy()` - Handle logout
  - Validation: Email, password required
  - Check: User must have admin or super_admin role

- [x] **Create admin login view**
  - File: `resources/views/admin/auth/login.blade.php`
  - **COPY FROM**: `views_old/pages/auth/signin.blade.php`
  - Modify: Admin branding, remove register link
  - Fields: Email, password, remember me

### 1.5 Admin Seeder & Command

- [x] **Create AdminSeeder**
  - File: `database/seeders/AdminSeeder.php`
  - Create default super admin user
  - Email: from env `ADMIN_EMAIL` or `admin@amuhi.id`
  - Password: from env `ADMIN_PASSWORD` or generated

- [x] **Create MakeAdmin artisan command**
  - File: `app/Console/Commands/MakeAdminCommand.php`
  - Signature: `make:admin {email} {--super}`
  - Logic: Find or create user, set role
  - Output: Show credentials if new user created

- [x] **Update DatabaseSeeder**
  - File: `database/seeders/DatabaseSeeder.php`
  - Call: `AdminSeeder::class`

### 1.6 Phase 1 Tests

- [x] **Create RoleEnumTest**
  - File: `tests/Unit/Enums/RoleTest.php`
  - Test: All enum values exist
  - Test: Enum can be cast on User model

- [x] **Create UserRoleTest**
  - File: `tests/Unit/Models/UserRoleTest.php`
  - Test: `isSuperAdmin()` returns correct value
  - Test: `isAdmin()` returns true for both admin and super_admin
  - Test: `isMember()` returns correct value
  - Test: Default role is member

- [x] **Create AdminMiddlewareTest**
  - File: `tests/Feature/Middleware/AdminMiddlewareTest.php`
  - Test: Member cannot access admin routes
  - Test: Admin can access admin routes
  - Test: SuperAdmin can access admin routes
  - Test: Unauthenticated redirected to admin login

- [x] **Create SuperAdminMiddlewareTest**
  - File: `tests/Feature/Middleware/SuperAdminMiddlewareTest.php`
  - Test: Admin cannot access super_admin routes
  - Test: SuperAdmin can access super_admin routes

- [x] **Create AdminLoginTest**
  - File: `tests/Feature/Admin/Auth/LoginTest.php`
  - Test: Admin login page renders
  - Test: Admin can login with valid credentials
  - Test: Member cannot login to admin
  - Test: Invalid credentials show error
  - Test: Admin can logout

- [x] **Create MakeAdminCommandTest**
  - File: `tests/Feature/Commands/MakeAdminCommandTest.php`
  - Test: Command creates new admin user
  - Test: Command promotes existing user to admin
  - Test: `--super` flag creates super_admin

### 1.7 Phase 1 Verification Checklist

- [x] Run `php artisan migrate` successfully
- [x] Run `php artisan db:seed --class=AdminSeeder`
- [x] Create super admin: `php artisan make:admin super@amuhi.id --super`
- [x] Create admin: `php artisan make:admin admin@amuhi.id`
- [x] Access `/admin/login` shows login form
- [x] Login as super admin redirects to admin dashboard
- [x] Login as member shows error
- [x] Access `/admin` as guest redirects to `/admin/login`
- [x] Run `php artisan test --filter=Admin` all pass

---

## Phase 2: Admin Layout & Navigation

### 2.1 Admin Layout

- [x] **Create admin master layout**
  - File: `resources/views/admin/layouts/admin.blade.php`
  - **STEP 1**: Copy `views_old/layouts/app.blade.php` to `admin/layouts/admin.blade.php`
  - **STEP 2**: Modify - Replace sidebar with admin sidebar
  - **STEP 3**: Modify - Replace header with admin header
  - **STEP 4**: Remove theme toggle (dark mode only)
  - Keep: Alpine.js stores, responsive design

- [x] **Create admin CSS/JS assets (if needed)**
  - Check: If separate admin assets needed
  - Update: `vite.config.js` if new entry point needed
  - Result: Reused existing `resources/css/app.css` and `resources/js/app.js`; no separate admin bundle required

### 2.2 Admin Sidebar

- [x] **Create AdminMenuHelper**
  - File: `app/Helpers/AdminMenuHelper.php`
  - Method: `getMenuGroups()` - Returns admin menu structure
  - Method: `getSvgIcons()` - Returns SVG icon map
  - Logic: Check user role for conditional items

- [x] **Define menu structure**
  ```php
  [
    ['title' => 'Dashboard', 'items' => [
      ['name' => 'Overview', 'icon' => 'dashboard', 'path' => '/admin'],
    ]],
    ['title' => 'Content', 'items' => [
      ['name' => 'News', 'icon' => 'news', 'path' => '/admin/news'],
      ['name' => 'Testimonies', 'icon' => 'quote', 'path' => '/admin/testimonies'],
    ]],
    ['title' => 'Portal', 'items' => [
      ['name' => 'Users', 'icon' => 'users', 'path' => '/admin/users'],
      ['name' => 'Events', 'icon' => 'calendar', 'path' => '/admin/events'],
      ['name' => 'Subscriptions', 'icon' => 'subscription', 'path' => '/admin/subscriptions'],
      ['name' => 'Invoices', 'icon' => 'invoice', 'path' => '/admin/invoices'],
      ['name' => 'Payments', 'icon' => 'payment', 'path' => '/admin/payments'],
    ]],
    ['title' => 'Admin', 'items' => [ // SuperAdmin only
      ['name' => 'Admins', 'icon' => 'shield', 'path' => '/admin/admins'],
    ]],
    ['title' => 'Settings', 'items' => [
      ['name' => 'Site Settings', 'icon' => 'settings', 'path' => '/admin/settings'],
      ['name' => 'Profile', 'icon' => 'user', 'path' => '/admin/profile'],
    ]],
  ]
  ```

- [x] **Create admin sidebar component**
  - File: `resources/views/admin/components/sidebar.blade.php`
  - **COPY FROM**: `views_old/layouts/sidebar.blade.php`
  - Use: `AdminMenuHelper::getMenuGroups()`
  - Conditional: Hide admin menu for non-super-admin

### 2.3 Admin Header

- [x] **Create admin header component**
  - File: `resources/views/admin/components/header.blade.php`
  - **COPY FROM**: `views_old/layouts/app-header.blade.php`
  - Include: Admin user dropdown
  - Include: Back to portal link
  - Remove: Theme toggle (dark mode only)
  - Remove: Portal-specific items (notifications, etc.)

- [x] **Create admin user dropdown**
  - File: `resources/views/admin/components/user-dropdown.blade.php`
  - **COPY FROM**: `views_old/components/header/user-dropdown.blade.php`
  - Show: Admin name, role badge
  - Links: Profile, Back to Portal, Logout

### 2.4 Admin Components (Reusable)

> **IMPORTANT**: Always COPY from `views_old/components/` first, then modify!

- [x] **Create stats card component**
  - File: `resources/views/admin/components/stats-card.blade.php`
  - **COPY FROM**: `views_old/components/ecommerce/ecommerce-metrics.blade.php`
  - Props: `title`, `value`, `icon`, `trend` (optional)
  - Use: Dashboard statistics

- [x] **Create data table component**
  - File: `resources/views/admin/components/data-table.blade.php`
  - **COPY FROM**: `views_old/components/tables/basic-tables/basic-tables-one.blade.php`
  - Props: `headers`, `slot` for rows
  - Features: Sortable headers, responsive

- [x] **Create form card component**
  - File: `resources/views/admin/components/form-card.blade.php`
  - **COPY FROM**: `views_old/components/common/component-card.blade.php`
  - Props: `title`, `description`, `action`, `method`
  - Slot: Form fields

- [x] **Create filters component**
  - File: `resources/views/admin/components/filters.blade.php`
  - **COPY FROM**: `views_old/components/ecommerce/product/` (find filter section)
  - Props: `filters` array
  - Features: Search, select filters, date range

- [x] **Create pagination component**
  - File: `resources/views/admin/components/pagination.blade.php`
  - **COPY FROM**: `views_old/components/ui/pagination/`
  - Props: `paginator` object
  - Style: Match admin design

- [x] **Create alert component**
  - File: `resources/views/admin/components/alert.blade.php`
  - **COPY FROM**: `views_old/components/ui/alert.blade.php`
  - Props: `type` (success, error, warning, info), `message`

- [x] **Create modal component**
  - File: `resources/views/admin/components/modal.blade.php`
  - **COPY FROM**: `views_old/components/example/modals-example/regular-modal.blade.php`
  - Props: `id`, `title`, `size`
  - Features: Alpine.js controlled

- [x] **Create badge component**
  - File: `resources/views/admin/components/badge.blade.php`
  - **COPY FROM**: `views_old/components/ui/badge/` (any badge file)
  - Props: `type`, `text`
  - Types: success, warning, danger, info, neutral

### 2.5 Phase 2 Verification Checklist

- [x] Admin layout renders without errors
- [x] Sidebar shows correct menu items
- [x] SuperAdmin sees "Admins" menu
- [x] Admin does NOT see "Admins" menu
- [x] Header shows current admin info
- [x] No theme toggle (dark mode only)
- [x] Sidebar collapse/expand works
- [x] Mobile responsive design works
- [x] All components render correctly
- [x] Dark mode applied to all components

---

## Phase 3: CMS Content Models

### 3.1 News Model & Migration

- [x] **Create NewsStatus enum**
  - File: `app/Enums/NewsStatus.php`
  - Values: `Draft`, `Published`, `Archived`

- [x] **Create news migration**
  - File: `database/migrations/xxxx_create_news_table.php`
  - Columns:
    - `id` bigIncrements
    - `slug` string, unique
    - `title` string
    - `summary` text
    - `category` string
    - `tags` json, nullable
    - `badge` string, nullable
    - `cover_image` string, nullable
    - `content` json
    - `read_time_minutes` integer, default 5
    - `author_name` string
    - `related_slugs` json, nullable
    - `status` string, default 'draft'
    - `published_at` timestamp, nullable
    - `timestamps`

- [x] **Create News model**
  - File: `app/Models/News.php`
  - Fillable: All columns except id, timestamps
  - Casts: `tags` array, `content` array, `related_slugs` array, `status` NewsStatus, `published_at` datetime
  - Scopes: `published()`, `draft()`, `archived()`
  - Methods: `isPublished()`, `getReadTimeAttribute()`

- [x] **Create NewsFactory**
  - File: `database/factories/NewsFactory.php`
  - Generate: Realistic news data
  - States: `published()`, `draft()`, `archived()`

- [x] **Create NewsSeeder**
  - File: `database/seeders/NewsSeeder.php`
  - Seed: 5-10 sample news articles

### 3.2 Testimony Model & Migration

- [x] **Create testimonies migration**
  - File: `database/migrations/xxxx_create_testimonies_table.php`
  - Columns:
    - `id` bigIncrements
    - `text` string (headline/quote)
    - `name` string
    - `role` string
    - `video_url` string
    - `sort_order` integer, default 0
    - `is_active` boolean, default true
    - `timestamps`

- [x] **Create Testimony model**
  - File: `app/Models/Testimony.php`
  - Fillable: All columns except id, timestamps
  - Casts: `is_active` boolean
  - Scopes: `active()`, `ordered()`

- [x] **Create TestimonyFactory**
  - File: `database/factories/TestimonyFactory.php`
  - Generate: Realistic testimony data
  - States: `active()`, `inactive()`

- [x] **Create TestimonySeeder**
  - File: `database/seeders/TestimonySeeder.php`
  - Seed: Sample testimonies from references.md

### 3.3 Site Settings Model & Migration

- [x] **Create site_settings migration**
  - File: `database/migrations/xxxx_create_site_settings_table.php`
  - Columns:
    - `id` bigIncrements
    - `key` string, unique
    - `value` json
    - `group` string, default 'general'
    - `timestamps`

- [x] **Create SiteSetting model**
  - File: `app/Models/SiteSetting.php`
  - Fillable: key, value, group
  - Casts: `value` array
  - Static methods: `get($key, $default = null)`, `set($key, $value)`

- [x] **Create SiteSettingSeeder**
  - File: `database/seeders/SiteSettingSeeder.php`
  - Seed: Default settings (site name, contact email, etc.)

### 3.4 Phase 3 Tests

- [x] **Create NewsTest**
  - File: `tests/Unit/Models/NewsTest.php`
  - Test: Fillable attributes
  - Test: Casts work correctly
  - Test: Scopes filter correctly
  - Test: Factory creates valid model

- [x] **Create TestimonyTest**
  - File: `tests/Unit/Models/TestimonyTest.php`
  - Test: Fillable attributes
  - Test: Scopes work correctly
  - Test: Factory creates valid model

- [x] **Create SiteSettingTest**
  - File: `tests/Unit/Models/SiteSettingTest.php`
  - Test: Get/set static methods
  - Test: Value cast to array

### 3.5 Phase 3 Verification Checklist

- [x] Run `php artisan migrate` successfully
- [x] Run `php artisan db:seed --class=NewsSeeder`
- [x] Run `php artisan db:seed --class=TestimonySeeder`
- [x] Run `php artisan db:seed --class=SiteSettingSeeder`
- [x] Verify data in database with tinker
- [x] Run `php artisan test --filter=News` all pass
- [x] Run `php artisan test --filter=Testimony` all pass
- [x] Run `php artisan test --filter=SiteSetting` all pass

---

## Phase 4: Admin Controllers

### 4.1 Dashboard Controller

- [x] **Create DashboardController**
  - File: `app/Http/Controllers/Admin/DashboardController.php`
  - Method: `index()` - Show dashboard with stats

- [x] **Implement dashboard stats**
  - Total users count
  - Total events count
  - Active subscriptions count
  - Published news count
  - Recent activity (last 10 from activity_logs)

- [x] **Create dashboard route**
  - Route: `GET /admin` → `DashboardController@index`
  - Name: `admin.dashboard`

### 4.2 User Management Controller

- [x] **Create UserController**
  - File: `app/Http/Controllers/Admin/UserController.php`
  - Method: `index()` - List member users with filters (admin/super_admin managed in admins page)
  - Method: `show($user)` - View user details
  - Method: `edit($user)` - Edit user form
  - Method: `update($user)` - Update user
  - Method: `destroy($user)` - Delete user

- [x] **Create UserIndexRequest (filters)**
  - File: `app/Http/Requests/Admin/UserIndexRequest.php`
  - Validate: search, status filter, per_page

- [x] **Create UserUpdateRequest**
  - File: `app/Http/Requests/Admin/UserUpdateRequest.php`
  - Validate: name, email (unique except self)

- [x] **Create user routes**
  - Route: Resource `/admin/users` → `UserController`
  - Name: `admin.users.*`
  - Except: `create`, `store` (users register themselves)

### 4.3 Admin Management Controller (SuperAdmin)

- [x] **Create AdminController**
  - File: `app/Http/Controllers/Admin/AdminController.php`
  - Method: `index()` - List admins and super_admins
  - Method: `create()` - Create admin form
  - Method: `store()` - Create new admin
  - Method: `edit($admin)` - Edit admin form
  - Method: `update($admin)` - Update admin role
  - Method: `destroy($admin)` - Remove admin access

- [x] **Create AdminStoreRequest**
  - File: `app/Http/Requests/Admin/AdminStoreRequest.php`
  - Validate: email (unique), name, password, role

- [x] **Create AdminUpdateRequest**
  - File: `app/Http/Requests/Admin/AdminUpdateRequest.php`
  - Validate: role (admin or super_admin only)

- [x] **Create admin routes (super_admin only)**
  - Route: Resource `/admin/admins` → `AdminController`
  - Name: `admin.admins.*`
  - Middleware: `super_admin`

### 4.4 Event Management Controller

- [x] **Create EventController**
  - File: `app/Http/Controllers/Admin/EventController.php`
  - Method: `index()` - List events
  - Method: `create()` - Create event form
  - Method: `store()` - Create event
  - Method: `show($event)` - View event with registrations
  - Method: `edit($event)` - Edit event form
  - Method: `update($event)` - Update event
  - Method: `destroy($event)` - Delete event

- [x] **Create EventStoreRequest**
  - File: `app/Http/Requests/Admin/EventStoreRequest.php`
  - Validate: title, description, location, image, starts_at, ends_at, status

- [x] **Create EventUpdateRequest**
  - File: `app/Http/Requests/Admin/EventUpdateRequest.php`
  - Validate: Same as store

- [x] **Create event routes**
  - Route: Resource `/admin/events` → `EventController`
  - Name: `admin.events.*`

### 4.5 News Controller

- [x] **Create NewsController**
  - File: `app/Http/Controllers/Admin/NewsController.php`
  - Method: `index()` - List news with filters
  - Method: `create()` - Create news form
  - Method: `store()` - Create news
  - Method: `show($news)` - Preview news
  - Method: `edit($news)` - Edit news form
  - Method: `update($news)` - Update news
  - Method: `destroy($news)` - Delete news
  - Method: `publish($news)` - Publish news
  - Method: `unpublish($news)` - Unpublish news

- [x] **Create NewsStoreRequest**
  - File: `app/Http/Requests/Admin/NewsStoreRequest.php`
  - Validate: title, slug (unique), summary, category, tags, content, author_name

- [x] **Create NewsUpdateRequest**
  - File: `app/Http/Requests/Admin/NewsUpdateRequest.php`
  - Validate: Same as store, slug unique except self

- [x] **Create news routes**
  - Route: Resource `/admin/news` → `NewsController`
  - Route: `POST /admin/news/{news}/publish` → `NewsController@publish`
  - Route: `POST /admin/news/{news}/unpublish` → `NewsController@unpublish`
  - Name: `admin.news.*`

### 4.6 Testimony Controller

- [x] **Create TestimonyController**
  - File: `app/Http/Controllers/Admin/TestimonyController.php`
  - Method: `index()` - List testimonies
  - Method: `create()` - Create form
  - Method: `store()` - Create testimony
  - Method: `edit($testimony)` - Edit form
  - Method: `update($testimony)` - Update testimony
  - Method: `destroy($testimony)` - Delete testimony
  - Method: `reorder()` - Update sort order (AJAX)

- [x] **Create TestimonyStoreRequest**
  - File: `app/Http/Requests/Admin/TestimonyStoreRequest.php`
  - Validate: text, name, role, video_url, is_active

- [x] **Create TestimonyUpdateRequest**
  - File: `app/Http/Requests/Admin/TestimonyUpdateRequest.php`
  - Validate: Same as store

- [x] **Create testimony routes**
  - Route: Resource `/admin/testimonies` → `TestimonyController`
  - Route: `POST /admin/testimonies/reorder` → `TestimonyController@reorder`
  - Name: `admin.testimonies.*`

### 4.7 Subscription Controller

- [x] **Create SubscriptionController**
  - File: `app/Http/Controllers/Admin/SubscriptionController.php`
  - Method: `index()` - List subscriptions
  - Method: `show($subscription)` - View details

- [x] **Create subscription routes**
  - Route: `GET /admin/subscriptions` → `SubscriptionController@index`
  - Route: `GET /admin/subscriptions/{subscription}` → `SubscriptionController@show`
  - Name: `admin.subscriptions.*`

### 4.8 Invoice Controller

- [x] **Create InvoiceController**
  - File: `app/Http/Controllers/Admin/InvoiceController.php`
  - Method: `index()` - List invoices
  - Method: `show($invoice)` - View invoice details

- [x] **Create invoice routes**
  - Route: `GET /admin/invoices` → `InvoiceController@index`
  - Route: `GET /admin/invoices/{invoice}` → `InvoiceController@show`
  - Name: `admin.invoices.*`

### 4.9 Payment Controller

- [x] **Create PaymentController**
  - File: `app/Http/Controllers/Admin/PaymentController.php`
  - Method: `index()` - List payments
  - Method: `show($payment)` - View payment details

- [x] **Create payment routes**
  - Route: `GET /admin/payments` → `PaymentController@index`
  - Route: `GET /admin/payments/{payment}` → `PaymentController@show`
  - Name: `admin.payments.*`

### 4.10 Settings Controller

- [x] **Create SettingController**
  - File: `app/Http/Controllers/Admin/SettingController.php`
  - Method: `index()` - Show settings form
  - Method: `update()` - Update settings

- [x] **Create SettingUpdateRequest**
  - File: `app/Http/Requests/Admin/SettingUpdateRequest.php`
  - Validate: Settings fields

- [x] **Create settings routes**
  - Route: `GET /admin/settings` → `SettingController@index`
  - Route: `PUT /admin/settings` → `SettingController@update`
  - Name: `admin.settings.*`

### 4.11 Phase 4 Tests

- [x] **Create AdminDashboardTest**
  - File: `tests/Feature/Admin/DashboardTest.php`
  - Test: Dashboard shows correct stats
  - Test: Recent activity displays

- [x] **Create AdminUserTest**
  - File: `tests/Feature/Admin/UserTest.php`
  - Test: Index shows users
  - Test: Can view user
  - Test: Can update user
  - Test: Can delete user

- [x] **Create AdminAdminTest**
  - File: `tests/Feature/Admin/AdminTest.php`
  - Test: SuperAdmin can access
  - Test: Admin cannot access
  - Test: Can create admin
  - Test: Can update admin role
  - Test: Cannot delete self

- [x] **Create AdminNewsTest**
  - File: `tests/Feature/Admin/NewsTest.php`
  - Test: CRUD operations
  - Test: Publish/unpublish
  - Test: Validation errors

- [x] **Create AdminTestimonyTest**
  - File: `tests/Feature/Admin/TestimonyTest.php`
  - Test: CRUD operations
  - Test: Reorder functionality

### 4.12 Phase 4 Verification Checklist

- [x] All routes registered (`php artisan route:list --path=admin`)
- [x] Dashboard loads with stats
- [x] User CRUD works
- [x] Admin CRUD works (as SuperAdmin)
- [x] Event CRUD works
- [x] News CRUD works
- [x] Testimony CRUD works
- [x] Subscription/Invoice/Payment views work
- [x] Settings update works
- [x] Run `php artisan test --filter=Admin` all pass

---

## Phase 5: Admin Views

> **MANDATORY UI WORKFLOW**:
> 1. **FIND** similar component/page in `resources/views_old/`
> 2. **COPY** the file to admin directory
> 3. **MODIFY** the copied file for admin use
> 4. **NEVER** create UI from scratch if similar component exists

### 5.0 Table Revision Rules (All Index Tables)

- [x] **Apply Basic Table 3 pattern to all index tables**
  - Source pattern: `views_old/components/tables/basic-tables/basic-tables-three.blade.php`
  - Use the same filter bar and table/list structure style

- [x] **Use icon-only actions for all table action columns**
  - Replace text actions (`View`, `Edit`, `Delete`) with action icons
  - Keep accessible labels (`aria-label`) on icon buttons/links

- [x] **Add avatar cell and remove ID column**
  - Add avatar/profile style column like Basic Table 3 where entity identity is shown
  - Remove `ID` column from all admin index tables

- [x] **Apply pagination on all index tables**
  - Users, Admins, Events, News, Testimonies, Subscriptions, Invoices, Payments

### 5.1 Dashboard Views

- [x] **Create dashboard index view**
  - File: `resources/views/admin/dashboard/index.blade.php`
  - **COPY FROM**: `views_old/pages/dashboard.blade.php` or `views_old/components/ecommerce/`
  - Include: Stats cards grid
  - Include: Recent activity table

### 5.2 User Views

- [x] **Create users index view**
  - File: `resources/views/admin/users/index.blade.php`
  - **COPY FROM**: `views_old/components/tables/basic-tables/basic-tables-three.blade.php`
  - Include: Member users only (exclude admin/super_admin from this page)
  - Include: Filters in Basic Table 3 layout (no role filter needed on this page)
  - Include: Data table with users
  - Include: Pagination

- [x] **Create users show view**
  - File: `resources/views/admin/users/show.blade.php`
  - **COPY FROM**: `views_old/components/ecommerce/transactions/customer-details.blade.php`
  - Display: User details, profile info
  - Display: Subscription status
  - Display: Activity history

- [x] **Create users edit view**
  - File: `resources/views/admin/users/edit.blade.php`
  - **COPY FROM**: `views_old/components/form/example-form/example-form-one.blade.php`
  - Form: Edit user details

### 5.3 Admin Views (SuperAdmin)

- [x] **Create admins index view**
  - File: `resources/views/admin/admins/index.blade.php`
  - **COPY FROM**: `views_old/components/tables/basic-tables/basic-tables-three.blade.php`
  - Table: List admins with role badge

- [x] **Create admins create view**
  - File: `resources/views/admin/admins/create.blade.php`
  - **COPY FROM**: `views_old/components/form/example-form/example-form-one.blade.php`
  - Form: Create new admin

- [x] **Create admins edit view**
  - File: `resources/views/admin/admins/edit.blade.php`
  - **COPY FROM**: `views_old/components/form/example-form/example-form-one.blade.php`
  - Form: Update admin role

### 5.4 Event Views

- [x] **Create events index view**
  - File: `resources/views/admin/events/index.blade.php`
  - **COPY FROM**: `views_old/components/tables/basic-tables/basic-tables-three.blade.php`
  - Table: List events with status

- [x] **Create events create view**
  - File: `resources/views/admin/events/create.blade.php`
  - **COPY FROM**: `views_old/components/form/example-form/example-form-two.blade.php`
  - Form: Create event
  - Include: Image upload (optional)

- [x] **Create events edit view**
  - File: `resources/views/admin/events/edit.blade.php`
  - **COPY FROM**: `views_old/components/form/example-form/example-form-two.blade.php`
  - Form: Edit event
  - Include: Image upload (optional)

- [x] **Create events show view**
  - File: `resources/views/admin/events/show.blade.php`
  - **COPY FROM**: `views_old/components/ecommerce/transactions/order-details.blade.php`
  - Display: Event details
  - Table: Registrations list

### 5.5 News Views

- [x] **Create news index view**
  - File: `resources/views/admin/content/news/index.blade.php`
  - **COPY FROM**: `views_old/components/tables/basic-tables/basic-tables-three.blade.php`
  - Table: List news with status, category
  - Filters: Status, category, search

- [x] **Create news create view**
  - File: `resources/views/admin/content/news/create.blade.php`
  - **COPY FROM**: `views_old/components/form/example-form/example-form-two.blade.php`
  - Form: All news fields
  - Component: Content block editor (paragraph, heading, list, quote)

- [x] **Create news edit view**
  - File: `resources/views/admin/content/news/edit.blade.php`
  - **COPY FROM**: Same as create view
  - Form: Same as create, pre-filled

- [x] **Create news show view**
  - File: `resources/views/admin/content/news/show.blade.php`
  - **COPY FROM**: `views_old/components/ecommerce/transactions/order-details.blade.php`
  - Display: News preview

- [x] **Create content block editor component**
  - File: `resources/views/admin/components/content-editor.blade.php`
  - **REFERENCE**: `views_old/components/form/form-elements/text-area-inputs.blade.php`
  - Feature: Add/remove blocks
  - Types: paragraph, heading, list, quote
  - Alpine.js: Dynamic block management

### 5.6 Testimony Views

- [x] **Create testimonies index view**
  - File: `resources/views/admin/content/testimonies/index.blade.php`
  - **COPY FROM**: `views_old/components/tables/basic-tables/basic-tables-three.blade.php`
  - Table: List testimonies
  - Feature: Drag-and-drop reorder

- [x] **Create testimonies create view**
  - File: `resources/views/admin/content/testimonies/create.blade.php`
  - **COPY FROM**: `views_old/components/form/example-form/example-form-one.blade.php`
  - Form: Testimony fields
  - Preview: YouTube video embed

- [x] **Create testimonies edit view**
  - File: `resources/views/admin/content/testimonies/edit.blade.php`
  - **COPY FROM**: Same as create view
  - Form: Same as create

### 5.7 Subscription Views

- [x] **Create subscriptions index view**
  - File: `resources/views/admin/subscriptions/index.blade.php`
  - Table: Subscriptions with user, plan, status

- [x] **Create subscriptions show view**
  - File: `resources/views/admin/subscriptions/show.blade.php`
  - Display: Subscription details

### 5.8 Invoice Views

- [x] **Create invoices index view**
  - File: `resources/views/admin/invoices/index.blade.php`
  - Table: Invoices with user, amount, status

- [x] **Create invoices show view**
  - File: `resources/views/admin/invoices/show.blade.php`
  - Display: Invoice details

### 5.9 Payment Views

- [x] **Create payments index view**
  - File: `resources/views/admin/payments/index.blade.php`
  - Table: Payments with invoice, amount, status

- [x] **Create payments show view**
  - File: `resources/views/admin/payments/show.blade.php`
  - Display: Payment details

### 5.10 Settings Views

- [x] **Create settings index view**
  - File: `resources/views/admin/settings/index.blade.php`
  - Form: Site settings
  - Sections: General, SEO, Contact

### 5.11 Phase 5 Verification Checklist

- [x] All views render without errors
- [x] Forms submit correctly
- [x] Validation errors display
- [x] Flash messages display
- [x] Tables paginate correctly
- [x] Filters work correctly
- [x] Action columns use icons only (no action text)
- [x] Avatar/profile cell style applied on index tables
- [x] ID column removed from index tables
- [x] Users index shows members only
- [x] Dark mode applied
- [x] Mobile responsive
- [x] Content editor works

---

## Phase 6: API for Marketing Website

### 6.1 API Routes

- [ ] **Create CMS API routes**
  - File: Add to `routes/api.php`
  - Prefix: `/api/v1/cms`
  - Routes:
    - `GET /news` - List published news
    - `GET /news/{slug}` - Get news by slug
    - `GET /testimonies` - List active testimonies
    - `GET /events` - List upcoming/ongoing events (for timeline)
    - `GET /events/{id}` - Get event details
    - `GET /settings/{key}` - Get setting value

### 6.2 API Controllers

- [ ] **Create CmsController**
  - File: `app/Http/Controllers/Api/CmsController.php`
  - Method: `newsIndex()` - Return paginated published news
  - Method: `newsShow($slug)` - Return single news
  - Method: `testimonies()` - Return active testimonies ordered
  - Method: `eventsIndex()` - Return upcoming/ongoing events
  - Method: `eventsShow($id)` - Return single event
  - Method: `setting($key)` - Return setting value

### 6.3 API Resources

- [ ] **Create NewsResource**
  - File: `app/Http/Resources/NewsResource.php`
  - Transform: News model to API response

- [ ] **Create TestimonyResource**
  - File: `app/Http/Resources/TestimonyResource.php`
  - Transform: Testimony model to API response

- [ ] **Create EventResource**
  - File: `app/Http/Resources/EventResource.php`
  - Transform: Event model to API response
  - Include: Formatted date fields (day, month, year) for timeline UI
  - Example response:
    ```json
    {
      "id": 1,
      "title": "Penipuan Tiket",
      "description": "Webinar edukasi gratis...",
      "location": "Online",
      "image": "/storage/events/image.jpg",
      "starts_at": "2025-02-08T00:00:00Z",
      "ends_at": "2025-02-08T02:00:00Z",
      "status": "upcoming",
      "day": "08",
      "month": "Feb",
      "year": "2025"
    }
    ```

### 6.4 API Caching

- [ ] **Implement response caching**
  - Cache: News list (5 min)
  - Cache: Single news (5 min)
  - Cache: Testimonies (5 min)
  - Cache: Events list (5 min)
  - Cache: Settings (15 min)
  - Invalidate: On create/update/delete

### 6.5 Phase 6 Tests

- [ ] **Create CmsApiTest**
  - File: `tests/Feature/Api/CmsTest.php`
  - Test: News list returns published only
  - Test: News show returns correct data
  - Test: News 404 for unpublished
  - Test: Testimonies returns active ordered
  - Test: Events list returns upcoming/ongoing only
  - Test: Events show returns correct data with formatted dates
  - Test: Settings returns value or null

### 6.6 Phase 6 Verification Checklist

- [ ] `curl /api/v1/cms/news` returns JSON
- [ ] `curl /api/v1/cms/news/{slug}` returns single news
- [ ] `curl /api/v1/cms/testimonies` returns array
- [ ] `curl /api/v1/cms/events` returns events with day/month/year
- [ ] `curl /api/v1/cms/events/{id}` returns single event
- [ ] `curl /api/v1/cms/settings/site_name` returns value
- [ ] Run `php artisan test --filter=CmsApi` all pass

---

## Final Verification

### Full System Test

- [ ] Fresh database migration works
- [ ] All seeders run without error
- [ ] Admin login flow works
- [ ] All CRUD operations work
- [ ] API endpoints respond correctly
- [ ] Dark mode works throughout
- [ ] Mobile responsive works
- [ ] All tests pass: `php artisan test`

### Security Checklist

- [ ] Member cannot access `/admin/*`
- [ ] Admin cannot access `/admin/admins/*`
- [ ] SuperAdmin can access everything
- [ ] CSRF protection on all forms
- [ ] Rate limiting on admin login
- [ ] No sensitive data in API responses

### Performance Checklist

- [ ] Database queries optimized (no N+1)
- [ ] API responses cached
- [ ] Assets compiled for production
- [ ] Images optimized

---

## Notes & Issues

### Blockers
<!-- Add any blockers here -->

### Decisions Made
<!-- Document any decisions during implementation -->

### Technical Debt
<!-- Note any shortcuts taken for future cleanup -->

---

## Changelog

| Date | Phase | Description | Author |
|------|-------|-------------|--------|
| 2026-02-17 | Phase 3 | Implemented CMS content models, migrations, factories, seeders, and model unit tests | Codex |
| 2026-02-17 | Phase 4 | Implemented all admin controllers, form requests, routes, stub views, and feature tests (38 tests passing) | Codex |
