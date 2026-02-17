# CMS System Integration - Todo List & Progress Tracker

> **Reference**: See `CMS_SYSTEM_PLAN.md` for full implementation details

## Progress Overview

| Phase | Description | Status | Progress |
|-------|-------------|--------|----------|
| Phase 1 | Foundation (Role System & Admin Auth) | 🟢 Completed | 100% |
| Phase 2 | Admin Layout & Navigation | 🔴 Not Started | 0% |
| Phase 3 | CMS Content Models | 🔴 Not Started | 0% |
| Phase 4 | Admin Controllers | 🔴 Not Started | 0% |
| Phase 5 | Admin Views | 🔴 Not Started | 0% |
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

- [ ] **Create admin master layout**
  - File: `resources/views/admin/layouts/admin.blade.php`
  - **STEP 1**: Copy `views_old/layouts/app.blade.php` to `admin/layouts/admin.blade.php`
  - **STEP 2**: Modify - Replace sidebar with admin sidebar
  - **STEP 3**: Modify - Replace header with admin header
  - **STEP 4**: Remove theme toggle (dark mode only)
  - Keep: Alpine.js stores, responsive design

- [ ] **Create admin CSS/JS assets (if needed)**
  - Check: If separate admin assets needed
  - Update: `vite.config.js` if new entry point needed

### 2.2 Admin Sidebar

- [ ] **Create AdminMenuHelper**
  - File: `app/Helpers/AdminMenuHelper.php`
  - Method: `getMenuGroups()` - Returns admin menu structure
  - Method: `getSvgIcons()` - Returns SVG icon map
  - Logic: Check user role for conditional items

- [ ] **Define menu structure**
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

- [ ] **Create admin sidebar component**
  - File: `resources/views/admin/components/sidebar.blade.php`
  - **COPY FROM**: `views_old/layouts/sidebar.blade.php`
  - Use: `AdminMenuHelper::getMenuGroups()`
  - Conditional: Hide admin menu for non-super-admin

### 2.3 Admin Header

- [ ] **Create admin header component**
  - File: `resources/views/admin/components/header.blade.php`
  - **COPY FROM**: `views_old/layouts/app-header.blade.php`
  - Include: Admin user dropdown
  - Include: Back to portal link
  - Remove: Theme toggle (dark mode only)
  - Remove: Portal-specific items (notifications, etc.)

- [ ] **Create admin user dropdown**
  - File: `resources/views/admin/components/user-dropdown.blade.php`
  - **COPY FROM**: `views_old/components/header/user-dropdown.blade.php`
  - Show: Admin name, role badge
  - Links: Profile, Back to Portal, Logout

### 2.4 Admin Components (Reusable)

> **IMPORTANT**: Always COPY from `views_old/components/` first, then modify!

- [ ] **Create stats card component**
  - File: `resources/views/admin/components/stats-card.blade.php`
  - **COPY FROM**: `views_old/components/ecommerce/ecommerce-metrics.blade.php`
  - Props: `title`, `value`, `icon`, `trend` (optional)
  - Use: Dashboard statistics

- [ ] **Create data table component**
  - File: `resources/views/admin/components/data-table.blade.php`
  - **COPY FROM**: `views_old/components/tables/basic-tables/basic-tables-one.blade.php`
  - Props: `headers`, `slot` for rows
  - Features: Sortable headers, responsive

- [ ] **Create form card component**
  - File: `resources/views/admin/components/form-card.blade.php`
  - **COPY FROM**: `views_old/components/common/component-card.blade.php`
  - Props: `title`, `description`, `action`, `method`
  - Slot: Form fields

- [ ] **Create filters component**
  - File: `resources/views/admin/components/filters.blade.php`
  - **COPY FROM**: `views_old/components/ecommerce/product/` (find filter section)
  - Props: `filters` array
  - Features: Search, select filters, date range

- [ ] **Create pagination component**
  - File: `resources/views/admin/components/pagination.blade.php`
  - **COPY FROM**: `views_old/components/ui/pagination/`
  - Props: `paginator` object
  - Style: Match admin design

- [ ] **Create alert component**
  - File: `resources/views/admin/components/alert.blade.php`
  - **COPY FROM**: `views_old/components/ui/alert.blade.php`
  - Props: `type` (success, error, warning, info), `message`

- [ ] **Create modal component**
  - File: `resources/views/admin/components/modal.blade.php`
  - **COPY FROM**: `views_old/components/example/modals-example/regular-modal.blade.php`
  - Props: `id`, `title`, `size`
  - Features: Alpine.js controlled

- [ ] **Create badge component**
  - File: `resources/views/admin/components/badge.blade.php`
  - **COPY FROM**: `views_old/components/ui/badge/` (any badge file)
  - Props: `type`, `text`
  - Types: success, warning, danger, info, neutral

### 2.5 Phase 2 Verification Checklist

- [ ] Admin layout renders without errors
- [ ] Sidebar shows correct menu items
- [ ] SuperAdmin sees "Admins" menu
- [ ] Admin does NOT see "Admins" menu
- [ ] Header shows current admin info
- [ ] No theme toggle (dark mode only)
- [ ] Sidebar collapse/expand works
- [ ] Mobile responsive design works
- [ ] All components render correctly
- [ ] Dark mode applied to all components

---

## Phase 3: CMS Content Models

### 3.1 News Model & Migration

- [ ] **Create NewsStatus enum**
  - File: `app/Enums/NewsStatus.php`
  - Values: `Draft`, `Published`, `Archived`

- [ ] **Create news migration**
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

- [ ] **Create News model**
  - File: `app/Models/News.php`
  - Fillable: All columns except id, timestamps
  - Casts: `tags` array, `content` array, `related_slugs` array, `status` NewsStatus, `published_at` datetime
  - Scopes: `published()`, `draft()`, `archived()`
  - Methods: `isPublished()`, `getReadTimeAttribute()`

- [ ] **Create NewsFactory**
  - File: `database/factories/NewsFactory.php`
  - Generate: Realistic news data
  - States: `published()`, `draft()`, `archived()`

- [ ] **Create NewsSeeder**
  - File: `database/seeders/NewsSeeder.php`
  - Seed: 5-10 sample news articles

### 3.2 Testimony Model & Migration

- [ ] **Create testimonies migration**
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

- [ ] **Create Testimony model**
  - File: `app/Models/Testimony.php`
  - Fillable: All columns except id, timestamps
  - Casts: `is_active` boolean
  - Scopes: `active()`, `ordered()`

- [ ] **Create TestimonyFactory**
  - File: `database/factories/TestimonyFactory.php`
  - Generate: Realistic testimony data
  - States: `active()`, `inactive()`

- [ ] **Create TestimonySeeder**
  - File: `database/seeders/TestimonySeeder.php`
  - Seed: Sample testimonies from references.md

### 3.3 Site Settings Model & Migration

- [ ] **Create site_settings migration**
  - File: `database/migrations/xxxx_create_site_settings_table.php`
  - Columns:
    - `id` bigIncrements
    - `key` string, unique
    - `value` json
    - `group` string, default 'general'
    - `timestamps`

- [ ] **Create SiteSetting model**
  - File: `app/Models/SiteSetting.php`
  - Fillable: key, value, group
  - Casts: `value` array
  - Static methods: `get($key, $default = null)`, `set($key, $value)`

- [ ] **Create SiteSettingSeeder**
  - File: `database/seeders/SiteSettingSeeder.php`
  - Seed: Default settings (site name, contact email, etc.)

### 3.4 Phase 3 Tests

- [ ] **Create NewsTest**
  - File: `tests/Unit/Models/NewsTest.php`
  - Test: Fillable attributes
  - Test: Casts work correctly
  - Test: Scopes filter correctly
  - Test: Factory creates valid model

- [ ] **Create TestimonyTest**
  - File: `tests/Unit/Models/TestimonyTest.php`
  - Test: Fillable attributes
  - Test: Scopes work correctly
  - Test: Factory creates valid model

- [ ] **Create SiteSettingTest**
  - File: `tests/Unit/Models/SiteSettingTest.php`
  - Test: Get/set static methods
  - Test: Value cast to array

### 3.5 Phase 3 Verification Checklist

- [ ] Run `php artisan migrate` successfully
- [ ] Run `php artisan db:seed --class=NewsSeeder`
- [ ] Run `php artisan db:seed --class=TestimonySeeder`
- [ ] Run `php artisan db:seed --class=SiteSettingSeeder`
- [ ] Verify data in database with tinker
- [ ] Run `php artisan test --filter=News` all pass
- [ ] Run `php artisan test --filter=Testimony` all pass
- [ ] Run `php artisan test --filter=SiteSetting` all pass

---

## Phase 4: Admin Controllers

### 4.1 Dashboard Controller

- [ ] **Create DashboardController**
  - File: `app/Http/Controllers/Admin/DashboardController.php`
  - Method: `index()` - Show dashboard with stats

- [ ] **Implement dashboard stats**
  - Total users count
  - Total events count
  - Active subscriptions count
  - Published news count
  - Recent activity (last 10 from activity_logs)

- [ ] **Create dashboard route**
  - Route: `GET /admin` → `DashboardController@index`
  - Name: `admin.dashboard`

### 4.2 User Management Controller

- [ ] **Create UserController**
  - File: `app/Http/Controllers/Admin/UserController.php`
  - Method: `index()` - List users with filters
  - Method: `show($user)` - View user details
  - Method: `edit($user)` - Edit user form
  - Method: `update($user)` - Update user
  - Method: `destroy($user)` - Delete user

- [ ] **Create UserIndexRequest (filters)**
  - File: `app/Http/Requests/Admin/UserIndexRequest.php`
  - Validate: search, role filter, status filter, per_page

- [ ] **Create UserUpdateRequest**
  - File: `app/Http/Requests/Admin/UserUpdateRequest.php`
  - Validate: name, email (unique except self)

- [ ] **Create user routes**
  - Route: Resource `/admin/users` → `UserController`
  - Name: `admin.users.*`
  - Except: `create`, `store` (users register themselves)

### 4.3 Admin Management Controller (SuperAdmin)

- [ ] **Create AdminController**
  - File: `app/Http/Controllers/Admin/AdminController.php`
  - Method: `index()` - List admins and super_admins
  - Method: `create()` - Create admin form
  - Method: `store()` - Create new admin
  - Method: `edit($admin)` - Edit admin form
  - Method: `update($admin)` - Update admin role
  - Method: `destroy($admin)` - Remove admin access

- [ ] **Create AdminStoreRequest**
  - File: `app/Http/Requests/Admin/AdminStoreRequest.php`
  - Validate: email (unique), name, password, role

- [ ] **Create AdminUpdateRequest**
  - File: `app/Http/Requests/Admin/AdminUpdateRequest.php`
  - Validate: role (admin or super_admin only)

- [ ] **Create admin routes (super_admin only)**
  - Route: Resource `/admin/admins` → `AdminController`
  - Name: `admin.admins.*`
  - Middleware: `super_admin`

### 4.4 Event Management Controller

- [ ] **Create EventController**
  - File: `app/Http/Controllers/Admin/EventController.php`
  - Method: `index()` - List events
  - Method: `create()` - Create event form
  - Method: `store()` - Create event
  - Method: `show($event)` - View event with registrations
  - Method: `edit($event)` - Edit event form
  - Method: `update($event)` - Update event
  - Method: `destroy($event)` - Delete event

- [ ] **Create EventStoreRequest**
  - File: `app/Http/Requests/Admin/EventStoreRequest.php`
  - Validate: title, description, location, image, starts_at, ends_at, status

- [ ] **Create EventUpdateRequest**
  - File: `app/Http/Requests/Admin/EventUpdateRequest.php`
  - Validate: Same as store

- [ ] **Create event routes**
  - Route: Resource `/admin/events` → `EventController`
  - Name: `admin.events.*`

### 4.5 News Controller

- [ ] **Create NewsController**
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

- [ ] **Create NewsStoreRequest**
  - File: `app/Http/Requests/Admin/NewsStoreRequest.php`
  - Validate: title, slug (unique), summary, category, tags, content, author_name

- [ ] **Create NewsUpdateRequest**
  - File: `app/Http/Requests/Admin/NewsUpdateRequest.php`
  - Validate: Same as store, slug unique except self

- [ ] **Create news routes**
  - Route: Resource `/admin/news` → `NewsController`
  - Route: `POST /admin/news/{news}/publish` → `NewsController@publish`
  - Route: `POST /admin/news/{news}/unpublish` → `NewsController@unpublish`
  - Name: `admin.news.*`

### 4.6 Testimony Controller

- [ ] **Create TestimonyController**
  - File: `app/Http/Controllers/Admin/TestimonyController.php`
  - Method: `index()` - List testimonies
  - Method: `create()` - Create form
  - Method: `store()` - Create testimony
  - Method: `edit($testimony)` - Edit form
  - Method: `update($testimony)` - Update testimony
  - Method: `destroy($testimony)` - Delete testimony
  - Method: `reorder()` - Update sort order (AJAX)

- [ ] **Create TestimonyStoreRequest**
  - File: `app/Http/Requests/Admin/TestimonyStoreRequest.php`
  - Validate: text, name, role, video_url, is_active

- [ ] **Create TestimonyUpdateRequest**
  - File: `app/Http/Requests/Admin/TestimonyUpdateRequest.php`
  - Validate: Same as store

- [ ] **Create testimony routes**
  - Route: Resource `/admin/testimonies` → `TestimonyController`
  - Route: `POST /admin/testimonies/reorder` → `TestimonyController@reorder`
  - Name: `admin.testimonies.*`

### 4.7 Subscription Controller

- [ ] **Create SubscriptionController**
  - File: `app/Http/Controllers/Admin/SubscriptionController.php`
  - Method: `index()` - List subscriptions
  - Method: `show($subscription)` - View details

- [ ] **Create subscription routes**
  - Route: `GET /admin/subscriptions` → `SubscriptionController@index`
  - Route: `GET /admin/subscriptions/{subscription}` → `SubscriptionController@show`
  - Name: `admin.subscriptions.*`

### 4.8 Invoice Controller

- [ ] **Create InvoiceController**
  - File: `app/Http/Controllers/Admin/InvoiceController.php`
  - Method: `index()` - List invoices
  - Method: `show($invoice)` - View invoice details

- [ ] **Create invoice routes**
  - Route: `GET /admin/invoices` → `InvoiceController@index`
  - Route: `GET /admin/invoices/{invoice}` → `InvoiceController@show`
  - Name: `admin.invoices.*`

### 4.9 Payment Controller

- [ ] **Create PaymentController**
  - File: `app/Http/Controllers/Admin/PaymentController.php`
  - Method: `index()` - List payments
  - Method: `show($payment)` - View payment details

- [ ] **Create payment routes**
  - Route: `GET /admin/payments` → `PaymentController@index`
  - Route: `GET /admin/payments/{payment}` → `PaymentController@show`
  - Name: `admin.payments.*`

### 4.10 Settings Controller

- [ ] **Create SettingController**
  - File: `app/Http/Controllers/Admin/SettingController.php`
  - Method: `index()` - Show settings form
  - Method: `update()` - Update settings

- [ ] **Create SettingUpdateRequest**
  - File: `app/Http/Requests/Admin/SettingUpdateRequest.php`
  - Validate: Settings fields

- [ ] **Create settings routes**
  - Route: `GET /admin/settings` → `SettingController@index`
  - Route: `PUT /admin/settings` → `SettingController@update`
  - Name: `admin.settings.*`

### 4.11 Phase 4 Tests

- [ ] **Create AdminDashboardTest**
  - File: `tests/Feature/Admin/DashboardTest.php`
  - Test: Dashboard shows correct stats
  - Test: Recent activity displays

- [ ] **Create AdminUserTest**
  - File: `tests/Feature/Admin/UserTest.php`
  - Test: Index shows users
  - Test: Can view user
  - Test: Can update user
  - Test: Can delete user

- [ ] **Create AdminAdminTest**
  - File: `tests/Feature/Admin/AdminTest.php`
  - Test: SuperAdmin can access
  - Test: Admin cannot access
  - Test: Can create admin
  - Test: Can update admin role
  - Test: Cannot delete self

- [ ] **Create AdminNewsTest**
  - File: `tests/Feature/Admin/NewsTest.php`
  - Test: CRUD operations
  - Test: Publish/unpublish
  - Test: Validation errors

- [ ] **Create AdminTestimonyTest**
  - File: `tests/Feature/Admin/TestimonyTest.php`
  - Test: CRUD operations
  - Test: Reorder functionality

### 4.12 Phase 4 Verification Checklist

- [ ] All routes registered (`php artisan route:list --path=admin`)
- [ ] Dashboard loads with stats
- [ ] User CRUD works
- [ ] Admin CRUD works (as SuperAdmin)
- [ ] Event CRUD works
- [ ] News CRUD works
- [ ] Testimony CRUD works
- [ ] Subscription/Invoice/Payment views work
- [ ] Settings update works
- [ ] Run `php artisan test --filter=Admin` all pass

---

## Phase 5: Admin Views

> **MANDATORY UI WORKFLOW**:
> 1. **FIND** similar component/page in `resources/views_old/`
> 2. **COPY** the file to admin directory
> 3. **MODIFY** the copied file for admin use
> 4. **NEVER** create UI from scratch if similar component exists

### 5.1 Dashboard Views

- [ ] **Create dashboard index view**
  - File: `resources/views/admin/dashboard/index.blade.php`
  - **COPY FROM**: `views_old/pages/dashboard.blade.php` or `views_old/components/ecommerce/`
  - Include: Stats cards grid
  - Include: Recent activity table

### 5.2 User Views

- [ ] **Create users index view**
  - File: `resources/views/admin/users/index.blade.php`
  - **COPY FROM**: `views_old/components/tables/basic-tables/basic-tables-one.blade.php`
  - Include: Filters (search, role)
  - Include: Data table with users
  - Include: Pagination

- [ ] **Create users show view**
  - File: `resources/views/admin/users/show.blade.php`
  - **COPY FROM**: `views_old/components/ecommerce/transactions/customer-details.blade.php`
  - Display: User details, profile info
  - Display: Subscription status
  - Display: Activity history

- [ ] **Create users edit view**
  - File: `resources/views/admin/users/edit.blade.php`
  - **COPY FROM**: `views_old/components/form/example-form/example-form-one.blade.php`
  - Form: Edit user details

### 5.3 Admin Views (SuperAdmin)

- [ ] **Create admins index view**
  - File: `resources/views/admin/admins/index.blade.php`
  - **COPY FROM**: `views_old/components/tables/basic-tables/basic-tables-one.blade.php`
  - Table: List admins with role badge

- [ ] **Create admins create view**
  - File: `resources/views/admin/admins/create.blade.php`
  - **COPY FROM**: `views_old/components/form/example-form/example-form-one.blade.php`
  - Form: Create new admin

- [ ] **Create admins edit view**
  - File: `resources/views/admin/admins/edit.blade.php`
  - **COPY FROM**: `views_old/components/form/example-form/example-form-one.blade.php`
  - Form: Update admin role

### 5.4 Event Views

- [ ] **Create events index view**
  - File: `resources/views/admin/events/index.blade.php`
  - **COPY FROM**: `views_old/components/tables/basic-tables/basic-tables-one.blade.php`
  - Table: List events with status

- [ ] **Create events create view**
  - File: `resources/views/admin/events/create.blade.php`
  - **COPY FROM**: `views_old/components/form/example-form/example-form-two.blade.php`
  - Form: Create event
  - Include: Image upload (optional)

- [ ] **Create events edit view**
  - File: `resources/views/admin/events/edit.blade.php`
  - **COPY FROM**: `views_old/components/form/example-form/example-form-two.blade.php`
  - Form: Edit event
  - Include: Image upload (optional)

- [ ] **Create events show view**
  - File: `resources/views/admin/events/show.blade.php`
  - **COPY FROM**: `views_old/components/ecommerce/transactions/order-details.blade.php`
  - Display: Event details
  - Table: Registrations list

### 5.5 News Views

- [ ] **Create news index view**
  - File: `resources/views/admin/content/news/index.blade.php`
  - **COPY FROM**: `views_old/components/tables/basic-tables/basic-tables-one.blade.php`
  - Table: List news with status, category
  - Filters: Status, category, search

- [ ] **Create news create view**
  - File: `resources/views/admin/content/news/create.blade.php`
  - **COPY FROM**: `views_old/components/form/example-form/example-form-two.blade.php`
  - Form: All news fields
  - Component: Content block editor (paragraph, heading, list, quote)

- [ ] **Create news edit view**
  - File: `resources/views/admin/content/news/edit.blade.php`
  - **COPY FROM**: Same as create view
  - Form: Same as create, pre-filled

- [ ] **Create news show view**
  - File: `resources/views/admin/content/news/show.blade.php`
  - **COPY FROM**: `views_old/components/ecommerce/transactions/order-details.blade.php`
  - Display: News preview

- [ ] **Create content block editor component**
  - File: `resources/views/admin/components/content-editor.blade.php`
  - **REFERENCE**: `views_old/components/form/form-elements/text-area-inputs.blade.php`
  - Feature: Add/remove blocks
  - Types: paragraph, heading, list, quote
  - Alpine.js: Dynamic block management

### 5.6 Testimony Views

- [ ] **Create testimonies index view**
  - File: `resources/views/admin/content/testimonies/index.blade.php`
  - **COPY FROM**: `views_old/components/tables/basic-tables/basic-tables-one.blade.php`
  - Table: List testimonies
  - Feature: Drag-and-drop reorder

- [ ] **Create testimonies create view**
  - File: `resources/views/admin/content/testimonies/create.blade.php`
  - **COPY FROM**: `views_old/components/form/example-form/example-form-one.blade.php`
  - Form: Testimony fields
  - Preview: YouTube video embed

- [ ] **Create testimonies edit view**
  - File: `resources/views/admin/content/testimonies/edit.blade.php`
  - **COPY FROM**: Same as create view
  - Form: Same as create

### 5.7 Subscription Views

- [ ] **Create subscriptions index view**
  - File: `resources/views/admin/subscriptions/index.blade.php`
  - Table: Subscriptions with user, plan, status

- [ ] **Create subscriptions show view**
  - File: `resources/views/admin/subscriptions/show.blade.php`
  - Display: Subscription details

### 5.8 Invoice Views

- [ ] **Create invoices index view**
  - File: `resources/views/admin/invoices/index.blade.php`
  - Table: Invoices with user, amount, status

- [ ] **Create invoices show view**
  - File: `resources/views/admin/invoices/show.blade.php`
  - Display: Invoice details

### 5.9 Payment Views

- [ ] **Create payments index view**
  - File: `resources/views/admin/payments/index.blade.php`
  - Table: Payments with invoice, amount, status

- [ ] **Create payments show view**
  - File: `resources/views/admin/payments/show.blade.php`
  - Display: Payment details

### 5.10 Settings Views

- [ ] **Create settings index view**
  - File: `resources/views/admin/settings/index.blade.php`
  - Form: Site settings
  - Sections: General, SEO, Contact

### 5.11 Phase 5 Verification Checklist

- [ ] All views render without errors
- [ ] Forms submit correctly
- [ ] Validation errors display
- [ ] Flash messages display
- [ ] Tables paginate correctly
- [ ] Filters work correctly
- [ ] Dark mode applied
- [ ] Mobile responsive
- [ ] Content editor works

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
| | | | |
