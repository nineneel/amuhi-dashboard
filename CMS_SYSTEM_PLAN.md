# CMS System Implementation Plan

## Overview

Create a unified CMS system at `portal.amuhi.id/admin` to manage content and analytics for:
- **Portal Amuhi** (portal.amuhi.id) - Member portal
- **Amuhi.id** (amuhi.id) - Marketing website

## Architecture Decisions

| Decision | Choice | Rationale |
|----------|--------|-----------|
| Database | Same database | Shared user relationships, simpler maintenance |
| Auth System | Same users table + role column | Single source of truth, existing 2FA works |
| Login Page | Separate `/admin/login` | Security by obscurity + cleaner UX |
| Admin Creation | Artisan command only | Prevents unauthorized admin registration |

---

## Phase 1: Foundation (Role System & Admin Auth)

### 1.1 Add Role to Users Table

**Migration**: `database/migrations/xxxx_add_role_to_users_table.php`
```php
$table->string('role')->default('member');
```

**Enum**: `app/Enums/Role.php`
- `SuperAdmin` - Full CMS access + can manage all admins and super admins
- `Admin` - Full CMS access (cannot see other admins/super admins)
- `Member` - Portal access only (default)

**Role Permissions**:
| Feature | SuperAdmin | Admin | Member |
|---------|------------|-------|--------|
| View admin list | ✓ | ✗ | ✗ |
| Manage admins | ✓ | ✗ | ✗ |
| Manage content (news, testimonies) | ✓ | ✓ | ✗ |
| Manage portal data (users, events, etc.) | ✓ | ✓ | ✗ |
| Access portal dashboard | ✓ | ✓ | ✓ |

**Update User Model**: Add role to fillable, cast to enum, add helpers:
- `isSuperAdmin()` - Check if user is super admin
- `isAdmin()` - Check if user is admin or super admin
- `isMember()` - Check if user is member

### 1.2 Admin Authentication

**Middleware**: `app/Http/Middleware/EnsureAdminRole.php`
- Check user role is admin or super_admin
- Redirect to portal dashboard if not admin

**Middleware**: `app/Http/Middleware/EnsureSuperAdminRole.php`
- Check user role is super_admin only
- Used for admin management routes

**Register in**: `bootstrap/app.php` as `admin` and `super_admin` aliases

### 1.3 Admin Routes

**File**: `routes/admin.php` (new file, require in bootstrap/app.php)
```
/admin/login     - Admin login page (guest only)
/admin           - Admin dashboard (requires admin)
/admin/*         - All CMS routes (requires admin)
/admin/admins/*  - Admin management (requires super_admin)
```

### 1.4 Admin Seeder & Command

**Seeder**: Create initial super admin user
**Command**: `php artisan make:admin {email} {--super}` to promote users or create new admins
- Without `--super`: creates admin role
- With `--super`: creates super_admin role

---

## Phase 2: Admin Layout & Navigation

### 2.1 Admin Layout

**File**: `resources/views/admin/layouts/admin.blade.php`
- **IMPORTANT**: Copy from `views_old/layouts/app.blade.php` first, then modify
- Different sidebar menu for admin
- Admin header with current admin info
- Dark mode only (no theme toggle needed)

### 2.2 Admin Sidebar Navigation

**File**: `app/Helpers/AdminMenuHelper.php`

Menu Structure:
```
Dashboard
├── Overview (quick stats only for MVP)

Content (for amuhi.id)
├── News
├── Testimonies

Portal Management
├── Users
├── Events
├── Subscriptions
├── Invoices
├── Payments

Admin Management (SuperAdmin only)
├── Admins

Settings
├── Site Settings
├── Admin Profile
```

### 2.3 Admin Components

**IMPORTANT**: Always find and copy existing components from `views_old/components/` before creating new ones. Modify after copying.

**Source Components to Copy**:
| Admin Component | Copy From (`views_old/`) |
|-----------------|--------------------------|
| `data-table.blade.php` | `components/tables/basic-tables/` |
| `stats-card.blade.php` | `components/ecommerce/ecommerce-metrics.blade.php` |
| `filters.blade.php` | `components/ecommerce/product/` |
| `form-card.blade.php` | `components/common/component-card.blade.php` |
| `pagination.blade.php` | `components/ui/pagination/` |
| `modal.blade.php` | `components/example/modals-example/` |
| `alert.blade.php` | `components/ui/alert.blade.php` |
| `badge.blade.php` | `components/ui/badge/` |

**Admin Components to Create** (by copying and modifying):
- `admin/components/data-table.blade.php` - Reusable data tables
- `admin/components/stats-card.blade.php` - Dashboard stats
- `admin/components/filters.blade.php` - Table filters
- `admin/components/form-card.blade.php` - Form wrapper

---

## Phase 3: CMS Content Models (for amuhi.id)

### 3.1 News

**Table**: `news`
```
- id
- slug (unique)
- title
- summary (text)
- category
- tags (json) - e.g. ['Haji 2026', 'Regulasi', 'Timeline']
- badge (nullable) - e.g. 'Update Lapangan'
- cover_image
- content (json) - structured content blocks
- read_time_minutes (integer)
- author_name
- related_slugs (json, nullable) - e.g. ['news-strategic-alliance']
- status (enum: draft, published, archived)
- published_at (timestamp, nullable)
- timestamps
```

**Content JSON Structure** (matching amuhi.id implementation):
```json
[
  { "type": "paragraph", "text": "..." },
  { "type": "heading", "text": "..." },
  { "type": "list", "items": ["...", "..."] },
  { "type": "quote", "text": "...", "cite": "..." }
]
```

**Model**: `app/Models/News.php`
**Enum**: `app/Enums/NewsStatus.php` (Draft, Published, Archived)

### 3.2 Testimonies

**Table**: `testimonies`
```
- id
- text (headline/quote)
- name (person name)
- role (person role/company)
- video_url (YouTube embed URL)
- sort_order (integer, for ordering)
- is_active (boolean)
- timestamps
```

**Model**: `app/Models/Testimony.php`

### 3.3 Site Settings

**Table**: `site_settings`
- id, key (unique), value (json), group, timestamps

**Model**: `app/Models/SiteSetting.php`

---

## Phase 4: Admin Controllers

### 4.1 Dashboard
**File**: `app/Http/Controllers/Admin/DashboardController.php`
- Quick stats only (users count, events count, subscriptions count, news count)
- Recent activity from activity_logs
- *Note: Full analytics integration deferred to post-MVP*

### 4.2 User Management
**File**: `app/Http/Controllers/Admin/UserController.php`
- CRUD for users (index, show, edit, update, destroy)
- Filter by role (members only for Admin, all for SuperAdmin)
- Subscription status view

### 4.3 Admin Management (SuperAdmin only)
**File**: `app/Http/Controllers/Admin/AdminController.php`
- List all admins and super_admins
- Create new admin/super_admin
- Edit admin roles
- Deactivate admins
- Protected by `super_admin` middleware

### 4.4 Event Management
**File**: `app/Http/Controllers/Admin/EventController.php`
- CRUD for events
- Registration management
- Status updates
- Image upload (optional) for event display on amuhi.id

**Note**: Event model already exists with `image` column. No migration needed.

### 4.5 Content Management
**Files**:
- `app/Http/Controllers/Admin/NewsController.php`
- `app/Http/Controllers/Admin/TestimonyController.php`

### 4.6 Subscription & Invoice Management
**Files**:
- `app/Http/Controllers/Admin/SubscriptionController.php`
- `app/Http/Controllers/Admin/InvoiceController.php`
- `app/Http/Controllers/Admin/PaymentController.php`

### 4.7 Settings
**File**: `app/Http/Controllers/Admin/SettingController.php`
- Site settings CRUD
- SEO defaults
- Contact information

---

## Phase 5: Admin Views

> **IMPORTANT UI WORKFLOW**: Before creating any view:
> 1. Find similar component/page in `resources/views_old/`
> 2. Copy the file to admin directory
> 3. Modify the copied file for admin use
> 4. Never create UI from scratch if a similar component exists

### 5.1 Directory Structure
```
resources/views/admin/
├── layouts/
│   └── admin.blade.php
├── components/
│   ├── sidebar.blade.php
│   ├── header.blade.php
│   ├── data-table.blade.php
│   ├── stats-card.blade.php
│   └── form-card.blade.php
├── auth/
│   └── login.blade.php
├── dashboard/
│   └── index.blade.php
├── users/
│   ├── index.blade.php
│   ├── show.blade.php
│   └── edit.blade.php
├── admins/ (SuperAdmin only)
│   ├── index.blade.php
│   ├── create.blade.php
│   └── edit.blade.php
├── events/
│   ├── index.blade.php
│   ├── create.blade.php
│   ├── edit.blade.php
│   └── show.blade.php
├── content/
│   ├── news/
│   │   ├── index.blade.php
│   │   ├── create.blade.php
│   │   ├── edit.blade.php
│   │   └── show.blade.php
│   └── testimonies/
│       ├── index.blade.php
│       ├── create.blade.php
│       └── edit.blade.php
├── subscriptions/
├── invoices/
└── settings/
```

### 5.2 UI Component Source Mapping

**MANDATORY**: Copy from `views_old/` before modifying. Never create from scratch.

| Admin View | Copy From (`views_old/`) |
|------------|--------------------------|
| **Layouts** | |
| `admin/layouts/admin.blade.php` | `layouts/app.blade.php` |
| `admin/components/sidebar.blade.php` | `layouts/sidebar.blade.php` |
| `admin/components/header.blade.php` | `layouts/app-header.blade.php` |
| **Auth** | |
| `admin/auth/login.blade.php` | `pages/auth/signin.blade.php` |
| **Dashboard** | |
| `admin/dashboard/index.blade.php` | `pages/dashboard.blade.php` or `components/ecommerce/` |
| **Tables/Lists** | |
| Index views (users, news, etc.) | `components/tables/basic-tables/` |
| **Forms** | |
| Create/Edit views | `components/form/example-form/` |
| Form inputs | `components/form/form-elements/` |
| **Details/Show** | |
| Show views | `components/ecommerce/transactions/` |
| **Common** | |
| Cards | `components/common/component-card.blade.php` |
| Pagination | `components/ui/pagination/` |
| Alerts | `components/ui/alert.blade.php` |
| Modals | `components/example/modals-example/` |
| Badges | `components/ui/badge/` |

---

## Phase 6: API for Marketing Website (amuhi.id)

### 6.1 Public API Endpoints
**Prefix**: `/api/v1/cms/`
```
GET /news                - List published news
GET /news/{slug}         - Get news by slug
GET /testimonies         - List active testimonies (sorted by sort_order)
GET /events              - List upcoming/ongoing events (for timeline display)
GET /events/{id}         - Get event details
GET /settings/{key}      - Get public setting
```

### 6.2 Events API Response (for amuhi.id timeline)

Based on current amuhi.id UI (timeline with date, title, description):
```json
{
  "data": [
    {
      "id": 1,
      "title": "Penipuan Tiket",
      "description": "Webinar edukasi gratis membahas...",
      "location": "Online",
      "image": "/storage/events/penipuan-tiket.jpg",  // optional
      "starts_at": "2025-02-08T00:00:00Z",
      "ends_at": "2025-02-08T02:00:00Z",
      "status": "upcoming",
      "day": "08",
      "month": "Feb",
      "year": "2025"
    }
  ]
}
```

### 6.3 API Controllers
**File**: `app/Http/Controllers/Api/CmsController.php`
- Method: `newsIndex()` - Return paginated published news
- Method: `newsShow($slug)` - Return single news
- Method: `testimonies()` - Return active testimonies ordered
- Method: `eventsIndex()` - Return upcoming/ongoing events
- Method: `eventsShow($id)` - Return single event
- Method: `setting($key)` - Return setting value

**File**: `app/Http/Controllers/Api/EventController.php` (alternative)
- Separate controller for event endpoints

### 6.4 API Resources
**Files**:
- `app/Http/Resources/NewsResource.php`
- `app/Http/Resources/TestimonyResource.php`
- `app/Http/Resources/EventResource.php` - Include formatted date (day, month, year)

### 6.5 API Caching
- Read-only endpoints
- Cache responses (5-15 min TTL)
- No authentication required

---

## Post-MVP: Google Analytics Integration (Deferred)

*To be implemented after MVP is complete*

### Configuration
**Config**: `config/analytics.php`
- GA4 Property ID for portal.amuhi.id
- GA4 Property ID for amuhi.id
- Service account credentials path

### Analytics Service
**File**: `app/Services/AnalyticsService.php`
- Fetch data from Google Analytics Data API
- Methods: `getPageViews()`, `getSessions()`, `getTopPages()`, `getUserDemographics()`
- Cache responses (15 min TTL)

### Admin Analytics Dashboard
**File**: `app/Http/Controllers/Admin/AnalyticsController.php`
- Combined view of both properties
- Date range filtering
- Export to CSV

### Required Package
```bash
composer require google/analytics-data
```

---

## Implementation Order (MVP)

1. **Phase 1**: Role system (SuperAdmin, Admin, Member), admin auth, routes
2. **Phase 2**: Admin layout, sidebar, base components
3. **Phase 3**: News and Testimony migrations and models
4. **Phase 4**: Dashboard + User management + Admin management (SuperAdmin)
5. **Phase 4 cont.**: Event, subscription, invoice controllers
6. **Phase 4 cont.**: News and Testimony controllers
7. **Phase 5**: Views for each section
8. **Phase 6**: Public API for amuhi.id

---

## Critical Files to Create/Modify

### New Files
- `app/Enums/Role.php`
- `app/Enums/NewsStatus.php`
- `app/Http/Middleware/EnsureAdminRole.php`
- `app/Http/Middleware/EnsureSuperAdminRole.php`
- `routes/admin.php`
- `app/Helpers/AdminMenuHelper.php`
- `app/Console/Commands/MakeAdminCommand.php`
- `database/migrations/xxxx_add_role_to_users_table.php`
- `database/migrations/xxxx_create_news_table.php`
- `database/migrations/xxxx_create_testimonies_table.php`
- `database/migrations/xxxx_create_site_settings_table.php`
- `app/Models/News.php`, `Testimony.php`, `SiteSetting.php`
- Admin controllers (8+ files)
- Admin views (25+ files)

### Modified Files
- `app/Models/User.php` - Add role, isSuperAdmin(), isAdmin(), isMember()
- `bootstrap/app.php` - Register admin middlewares, load admin routes
- `database/seeders/DatabaseSeeder.php` - Add admin seeder

---

## Verification

### After Phase 1
- [ ] Run `php artisan migrate` successfully
- [ ] Create super admin with `php artisan make:admin test@admin.com --super`
- [ ] Create admin with `php artisan make:admin admin@test.com`
- [ ] Access `/admin/login`, login as super admin
- [ ] Super admin can see admin management menu
- [ ] Admin cannot see admin management menu
- [ ] Non-admin users redirected when accessing `/admin`

### After Phase 2-5
- [ ] Admin dashboard loads with quick stats
- [ ] CRUD operations work for all entities
- [ ] SuperAdmin can manage other admins
- [ ] Admin cannot access `/admin/admins`
- [ ] News create/edit with structured content blocks
- [ ] Testimonies with video embed preview
- [ ] Components render correctly (dark mode, responsive)

### After Phase 6
- [ ] API endpoints return correct JSON
- [ ] Public endpoints work without auth
- [ ] Test with `curl` or Postman

### Run Tests
```bash
php artisan test --filter=Admin
```

---

## Security Considerations

1. Admin routes protected by `admin` middleware
2. Admin management routes protected by `super_admin` middleware
3. Rate limiting on admin login
4. Activity logging for admin actions (existing `activity_logs` table)
5. CSRF protection on all forms
6. Admin accounts created via CLI only (no public registration)
7. Two-factor auth inherited from existing system
