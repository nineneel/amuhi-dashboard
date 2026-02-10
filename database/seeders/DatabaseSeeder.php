<?php

namespace Database\Seeders;

use App\EventStatus;
use App\InvoiceStatus;
use App\MemberType;
use App\Models\ActivityLog;
use App\Models\Event;
use App\Models\Invoice;
use App\Models\Notification;
use App\Models\Payment;
use App\Models\Subscription;
use App\Models\SubscriptionPlan;
use App\Models\User;
use App\PaymentStatus;
use App\SubscriptionStatus;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(SubscriptionPlanSeeder::class);

        $user = User::query()->updateOrCreate(
            ['email' => 'test@example.com'],
            [
                'name' => 'Test User',
                'email_verified_at' => now(),
                'password' => Hash::make('password'),
            ]
        );

        $user->profile()->updateOrCreate(
            ['user_id' => $user->id],
            [
                'member_type' => MemberType::PpuiPihk,
                'company_name' => 'PT AMUHI Nusantara',
                'phone' => '+62 812-3456-7890',
                'address' => 'Jl. Jend. Sudirman No. 10, Jakarta',
                'bio' => 'AMUHI member focused on compliance, service excellence, and member collaboration.',
            ]
        );

        $user->settings()->updateOrCreate(
            ['user_id' => $user->id],
            [
                'notification_email' => true,
                'notification_app' => true,
                'language' => 'id',
                'theme' => 'dark',
                'privacy_settings' => [
                    'profile_visible' => true,
                    'show_email' => false,
                    'show_phone' => true,
                ],
            ]
        );

        $this->call(EventSeeder::class);

        $plan = SubscriptionPlan::query()
            ->where('name', 'Monthly Membership')
            ->first();

        if (! $plan) {
            $plan = SubscriptionPlan::query()->where('is_active', true)->first();
        }

        if (! $plan) {
            return;
        }

        $startsAt = now()->subDays(15);
        $endsAt = $plan->duration_days ? $startsAt->copy()->addDays($plan->duration_days) : null;

        $subscription = Subscription::query()->updateOrCreate(
            [
                'user_id' => $user->id,
                'subscription_plan_id' => $plan->id,
            ],
            [
                'status' => SubscriptionStatus::Active->value,
                'starts_at' => $startsAt,
                'ends_at' => $endsAt,
                'gateway_subscription_id' => 'demo-sub-'.Str::upper(Str::random(8)),
            ]
        );

        $invoices = [
            [
                'invoice_number' => 'INV-2026-0001',
                'amount' => $plan->price,
                'due_date' => now()->subDays(12),
                'status' => InvoiceStatus::Paid->value,
            ],
            [
                'invoice_number' => 'INV-2026-0002',
                'amount' => $plan->price,
                'due_date' => now()->addDays(10),
                'status' => InvoiceStatus::Pending->value,
            ],
            [
                'invoice_number' => 'INV-2026-0003',
                'amount' => $plan->price,
                'due_date' => now()->subDays(3),
                'status' => InvoiceStatus::Overdue->value,
            ],
            [
                'invoice_number' => 'INV-2026-0004',
                'amount' => $plan->price,
                'due_date' => now()->addDays(25),
                'status' => InvoiceStatus::Cancelled->value,
            ],
        ];

        $createdInvoices = collect();

        foreach ($invoices as $invoice) {
            $createdInvoices->push(Invoice::query()->updateOrCreate(
                ['invoice_number' => $invoice['invoice_number']],
                [
                    'user_id' => $user->id,
                    'subscription_id' => $subscription->id,
                    'amount' => $invoice['amount'],
                    'due_date' => $invoice['due_date'],
                    'status' => $invoice['status'],
                ]
            ));
        }

        $paidInvoice = $createdInvoices->firstWhere('status', InvoiceStatus::Paid->value);

        if ($paidInvoice) {
            Payment::query()->updateOrCreate(
                ['invoice_id' => $paidInvoice->id],
                [
                    'gateway_transaction_id' => 'demo-pay-'.Str::upper(Str::random(8)),
                    'amount' => $paidInvoice->amount,
                    'status' => PaymentStatus::Success->value,
                    'paid_at' => now()->subDays(7),
                ]
            );
        }

        $nextEvent = Event::query()
            ->whereIn('status', [EventStatus::Upcoming, EventStatus::Ongoing])
            ->orderBy('starts_at')
            ->first();

        $activityLogs = [
            [
                'action' => 'Membership activated',
                'description' => 'Your membership is active and ready to use.',
                'subject_type' => Subscription::class,
                'subject_id' => $subscription->id,
            ],
            [
                'action' => 'Event registration',
                'description' => $nextEvent
                    ? 'You registered for '.$nextEvent->title.'.'
                    : 'You registered for a member event.',
                'subject_type' => $nextEvent ? Event::class : null,
                'subject_id' => $nextEvent?->id,
            ],
            [
                'action' => 'Invoice paid',
                'description' => $paidInvoice
                    ? 'Invoice #'.$paidInvoice->invoice_number.' was paid successfully.'
                    : 'Your latest invoice was paid successfully.',
                'subject_type' => $paidInvoice ? Invoice::class : null,
                'subject_id' => $paidInvoice?->id,
            ],
            [
                'action' => 'Program interest saved',
                'description' => 'You bookmarked AMUHI Academy for upcoming sessions.',
                'subject_type' => null,
                'subject_id' => null,
            ],
            [
                'action' => 'Profile updated',
                'description' => 'You refreshed your company profile details.',
                'subject_type' => null,
                'subject_id' => null,
            ],
        ];

        foreach ($activityLogs as $activity) {
            ActivityLog::query()->updateOrCreate(
                [
                    'user_id' => $user->id,
                    'action' => $activity['action'],
                ],
                [
                    'description' => $activity['description'],
                    'subject_type' => $activity['subject_type'],
                    'subject_id' => $activity['subject_id'],
                    'ip_address' => '127.0.0.1',
                ]
            );
        }

        $notifications = [
            [
                'type' => 'membership',
                'title' => 'Membership active',
                'message' => 'Your AMUHI membership is active. Next renewal will be calculated automatically.',
                'sent_via' => 'app',
                'read_at' => now()->subDays(1),
            ],
            [
                'type' => 'event',
                'title' => 'New event added',
                'message' => $nextEvent
                    ? $nextEvent->title.' is now open for registration.'
                    : 'A new AMUHI event has been added to the calendar.',
                'sent_via' => 'app',
                'read_at' => null,
            ],
            [
                'type' => 'invoice',
                'title' => 'Invoice due soon',
                'message' => 'Your next invoice is due in 10 days. Review the invoice details anytime.',
                'sent_via' => 'app',
                'read_at' => null,
            ],
            [
                'type' => 'program',
                'title' => 'Program update',
                'message' => 'AMUHI Academy sessions will open for registration next month.',
                'sent_via' => 'app',
                'read_at' => now()->subDays(2),
            ],
            [
                'type' => 'security',
                'title' => 'Account security reminder',
                'message' => 'Enable two-factor authentication to keep your account secure.',
                'sent_via' => 'app',
                'read_at' => null,
            ],
        ];

        foreach ($notifications as $notification) {
            Notification::query()->updateOrCreate(
                [
                    'user_id' => $user->id,
                    'title' => $notification['title'],
                ],
                [
                    'type' => $notification['type'],
                    'message' => $notification['message'],
                    'data' => $notification['data'] ?? null,
                    'read_at' => $notification['read_at'],
                    'sent_via' => $notification['sent_via'],
                ]
            );
        }
    }
}
