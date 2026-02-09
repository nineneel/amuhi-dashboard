# Amuhi Dashboard - Project Plan

## Project Overview

**Project Name:** Amuhi Dashboard
**Description:** [Brief description of what this dashboard is for]
**Target Users:** [Who will use this dashboard?]

---

## Authentication

### Login Page
- **Route:** `/login`
- **Features:**
  - [x] Email/Username login
  - [x] Password field
  - [x] Remember me
  - [x] Forgot password link
  - [x] Two-factor authentication (if enabled)

### Signup Page
- **Route:** `/signup` or `/register`
- **Required Fields:**
  - [x] Member Type [ppui_pihk, pt, personal]*
  - [x] Name *
  - [x] Phone number *
  - [x] Email *
  - [x] Password *
  - [x] PT Name (optional)
- **Features:**
  - [x] Email verification required
  - [x] Terms & conditions checkbox
- Notes:
  - The fields will increase over time

---

## Main Pages

### Dashboard Page*
- **Route:** `/dashboard`
- **Widgets/Sections:**
  - [x] Welcome message
  - [x] Recent activity
  - [x] Upcoming events
  - [x] Quick actions

### Profile Page
- **Route:** `/profile`
- **Editable Fields:**
  - [x] Member Type [ppui_pihk, pt, personal]
  - [x] Profile photo / Company Logo
  - [x] Company Name
  - [x] Name
  - [x] Email
  - [x] Phone
  - [x] Address
  - [x] Bio/Description
  - [x] Subscription Status [active, inactive, pending, expired] (using full subsripition table for tracing payment history for invoice feature)
- **Additional Sections:**
  - [x] Change password
  - [x] Two-factor authentication

### Events Page
- **Route:** `/events`
- **Features:**
  - [x] List view
  - [x] Calendar view
  - [x] Event details modal/page
  - [x] Register for event
  - [x] View registered events
- **Event Fields to Display:**
  - [x] Title
  - [x] Date/Time
  - [x] Location
  - [x] Description
  - [x] Status (upcoming, ongoing, past)

### Invoice Page
- **Route:** `/invoices`
- **Features:**
  - [x] List of invoices
  - [x] Invoice details view
  - [x] Download PDF
  - [x] Payment status
  - [x] Payment integration (specify): comming soon
- **Invoice Fields:**
  - [x] Invoice number
  - [x] Date
  - [x] Amount
  - [x] Status (paid, pending, overdue)

### 6 Programs Page (Coming Soon)
- **Route:** `/programs`
- **Programs List:**
  1. AMUHI Academy
  2. AMUHI Check
  3. AMUHI Protect
  4. AMUHI Care
  5. AMUHI Network
  6. AMUHI Digital
- **Coming Soon Page Style:**
  - [x] Simple placeholder
  - [x] Comming Soon Banner

### Settings Page*
- **Route:** `/settings`
- **Sections:**
  - [x] Account settings
  - [x] Notification preferences (just email for now)
  - [x] Privacy settings
  - [x] Language/Locale
  - [x] Theme (dark/light mode)
  ------- Profile Section --------
  - [x] Two-factor authentication
  - [x] Change password
  - [x] Delete account

---

## Navigation & Flow

### Sidebar Navigation
| Menu Item | Route | Icon | Separator | Paid Only |
|-----------|-------|------|-----------|-----------|
| Dashboard | `/dashboard` | home | | No |
| --- | | | Yes | |
| Events | `/events` | calendar | | Yes |
| Programs | `/programs` | grid | | Yes |
| --- | | | Yes | |
| Invoices | `/invoices` | file-text | | Yes |
| --- | | | Yes | |
| Profile | `/profile` | user | | No |
| Settings | `/settings` | settings | | No |
| --- | | | Yes | |
| Logout | `/logout` | log-out | | No |

### Header Navigation (Right Side)
| Item | Position | Description |
|------|----------|-------------|
| Notification Bell | Right | Bell icon with unread count badge, opens notification dropdown/page |
| Profile Dropdown | Far Right | User photo + Name/Company Name, dropdown with quick links |

**Profile Dropdown Menu:**
- View Profile
- Settings
- Logout

### User Flows

#### Flow 1: User Registration & Payment
```
Landing Page → Signup → Email Verification → Payment Page → Dashboard (Full Access)
```
1. User clicks "Sign Up"
2. Fills registration form (member type, name, phone, email, password)
3. Receives verification email
4. Clicks verification link
5. Redirected to **Payment Page** showing:
   - Subscription plan details
   - Amount to pay
   - Services included in subscription
   - Payment gateway options
6. User completes payment via **Payment Gateway**
7. Payment gateway confirms → User status **auto-updated** to "active"
8. User gains full access to Dashboard & all features

#### Flow 2: Unpaid User Experience
```
Login → Dashboard (Limited) → Payment Banner → Pay via Gateway → Full Access
```
1. Unpaid user logs in
2. Sees Dashboard with **payment reminder banner**
3. Can only access: Dashboard, Profile, Settings, Notifications
4. Events, Programs, Invoices are **locked** (shows "Subscribe to access")
5. Clicks "Subscribe Now" → Redirected to Payment Gateway
6. Completes payment → Auto-unlocks full access

#### Flow 3: Returning User Login
```
Login → [2FA if enabled] → Dashboard
```
1. User enters email/password
2. If 2FA enabled: enters code
3. Lands on Dashboard (full or limited based on payment status)

#### Flow 4: Event Registration (Paid Users Only)
```
Events List → Event Details → Register Button → Added to Calendar
```
1. User browses events (list/calendar view)
2. Clicks event for details
3. Clicks "Register" button (no form, one-click)
4. Event added to user's calendar
5. User receives confirmation notification
- **Note:** Events are FREE, no payment required. Registration is just to track attendance.

#### Flow 5: Invoice Tracking (Paid Users Only)
```
Invoices List → Invoice Details → Download PDF
```
1. User views invoice list (subscription history)
2. Clicks invoice for details
3. Downloads PDF for records
- **Note:** Invoices are for tracking subscription payments only. No in-app payment.

#### Flow 6: Profile Update
```
Profile → Edit Fields → Save → Success Message
```

#### Flow 7: Settings Update
```
Settings → Select Section → Update Preferences → Save
```

### Page Access Rules
| Page | Guest | Unpaid User | Paid User | Notes |
|------|-------|-------------|-----------|-------|
| Login | Yes | Redirect | Redirect | |
| Signup | Yes | Redirect | Redirect | |
| Dashboard | No | Yes (limited) | Yes | Unpaid sees payment banner |
| Profile | No | Yes | Yes | |
| Settings | No | Yes | Yes | |
| Notifications | No | Yes | Yes | Header access |
| Events | No | **Locked** | Yes | Requires subscription |
| Programs | No | **Locked** | Yes | Requires subscription |
| Invoices | No | **Locked** | No | Requires subscription |

### Payment Status States
| Status | Description | Access Level |
|--------|-------------|--------------|
| `unpaid` | New user, hasn't paid yet | Limited (Dashboard, Profile, Settings, Notifications) |
| `pending` | Payment in progress (gateway processing) | Limited + "Processing payment" message |
| `active` | Payment confirmed by gateway, subscription active | Full access |
| `expired` | Subscription expired | Limited + "Renew subscription" prompt |

---

## Technical Requirements

### Database
- **Existing Tables to Use:** `users`
- **New Tables Needed:**

| Table | Purpose | Key Fields |
|-------|---------|------------|
| `user_profiles` | Extended user info | `user_id`, `member_type`, `company_name (nullable)`, `name`, `phone`, `address`, `bio`, `photo` |
| `subscription_plans` | Available subscription plans | `name`, `price`, `duration`, `features` |
| `subscriptions` | User's subscription record | `user_id`, `plan_id`, `status`, `starts_at`, `ends_at`, `gateway_subscription_id` |
| `invoices` | Bills for users | `user_id`, `subscription_id`, `invoice_number`, `amount`, `due_date`, `status` |
| `payments` | Payment transactions | `invoice_id`, `gateway_transaction_id`, `amount`, `status`, `paid_at` |
| `events` | Event details | `title`, `description`, `location`, `starts_at`, `ends_at`, `status` |
| `event_registrations` | User-event registration | `user_id`, `event_id`, `registered_at`, `status` |
| `user_settings` | User preferences | `user_id`, `notification_email`, `notification_app`, `language`, `theme`, `privacy_settings` |
| `activity_logs` | Track user actions | `user_id`, `action`, `description`, `subject_type`, `subject_id`, `ip_address`, `created_at` |
| `notifications` | In-app & email notifications | `user_id`, `type`, `title`, `message`, `data`, `read_at`, `sent_via` (email/app) |

### User Roles
- [x] Multiple roles (specify): admin, user, moderator 

### Design/UI
- **UI Template Reference:** Use template from @ui-template
- **Color Scheme:** Use the same color scheme with template for now, but eazy to change or update in setting by leter
- **Dark Mode Support:** [x] Yes [-] No

### Notifications
- [x] Email notifications
- [x] In-app notifications (list view, no push)
- [-] ~~Push notifications~~ (not needed)

---

## Priority & Order

**Build Order (1 = first):**
- [x] 1. SignUp Page
- [x] 2. Login Page  
- [x] 3. Profile Page
- [x] 4. Invoice Page
- [x] 5. Event Page
- [x] 6. Program Page
- [x] 7. Setting
- [x] 8. Dashboard Page

---

## Questions to Clarify

1. What do the asterisks (*) on Settings and Dashboard mean? its mean (i don't know the field yet, but already filled in on this plan)
2. Are there existing models/migrations already set up? (not yet)
3. What is the relationship between users and events/invoices? (none, the invoices are just for registration in the begining)
4. Is there an admin panel for managing this, or just user-facing? (For now just user facing, but leter on we will use admin panel as well)

