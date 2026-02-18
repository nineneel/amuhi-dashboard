<?php

use App\InvoiceStatus;
use App\PaymentStatus;
use App\SubscriptionStatus;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\SiteSetting;
use App\Models\Subscription;
use App\Models\SubscriptionPlan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('shows subscriptions index and details page', function () {
    $admin = User::factory()->admin()->create();
    $member = User::factory()->create();
    $plan = SubscriptionPlan::factory()->create(['name' => 'Premium Plan']);

    $subscription = Subscription::query()->create([
        'user_id' => $member->id,
        'subscription_plan_id' => $plan->id,
        'status' => SubscriptionStatus::Active,
        'starts_at' => now()->subDays(5),
        'ends_at' => now()->addDays(25),
    ]);

    $this->actingAs($admin)
        ->get(route('admin.subscriptions.index'))
        ->assertSuccessful()
        ->assertSee($member->name)
        ->assertSee($plan->name);

    $this->actingAs($admin)
        ->get(route('admin.subscriptions.show', $subscription))
        ->assertSuccessful()
        ->assertSee($member->email);
});

it('shows invoices index and details page', function () {
    $admin = User::factory()->admin()->create();
    $member = User::factory()->create();
    $plan = SubscriptionPlan::factory()->create();

    $subscription = Subscription::query()->create([
        'user_id' => $member->id,
        'subscription_plan_id' => $plan->id,
        'status' => SubscriptionStatus::Active,
        'starts_at' => now()->subDays(10),
        'ends_at' => now()->addDays(20),
    ]);

    $invoice = Invoice::query()->create([
        'user_id' => $member->id,
        'subscription_id' => $subscription->id,
        'invoice_number' => 'INV-TEST-001',
        'amount' => 250000,
        'due_date' => now()->addDays(7)->toDateString(),
        'status' => InvoiceStatus::Pending,
    ]);

    $this->actingAs($admin)
        ->get(route('admin.invoices.index'))
        ->assertSuccessful()
        ->assertSee('INV-TEST-001');

    $this->actingAs($admin)
        ->get(route('admin.invoices.show', $invoice))
        ->assertSuccessful()
        ->assertSee('INV-TEST-001');
});

it('shows payments index and details page', function () {
    $admin = User::factory()->admin()->create();
    $member = User::factory()->create();
    $plan = SubscriptionPlan::factory()->create();

    $subscription = Subscription::query()->create([
        'user_id' => $member->id,
        'subscription_plan_id' => $plan->id,
        'status' => SubscriptionStatus::Active,
        'starts_at' => now()->subDays(10),
        'ends_at' => now()->addDays(20),
    ]);

    $invoice = Invoice::query()->create([
        'user_id' => $member->id,
        'subscription_id' => $subscription->id,
        'invoice_number' => 'INV-TEST-002',
        'amount' => 500000,
        'due_date' => now()->addDays(5)->toDateString(),
        'status' => InvoiceStatus::Paid,
    ]);

    $payment = Payment::query()->create([
        'invoice_id' => $invoice->id,
        'gateway_transaction_id' => 'TXN-TEST-001',
        'amount' => 500000,
        'status' => PaymentStatus::Success,
        'paid_at' => now(),
    ]);

    $this->actingAs($admin)
        ->get(route('admin.payments.index'))
        ->assertSuccessful()
        ->assertSee('TXN-TEST-001');

    $this->actingAs($admin)
        ->get(route('admin.payments.show', $payment))
        ->assertSuccessful()
        ->assertSee('TXN-TEST-001');
});

it('shows settings page and updates settings', function () {
    $admin = User::factory()->admin()->create();

    SiteSetting::query()->create([
        'key' => 'site_name',
        'value' => ['text' => 'Old Name'],
        'group' => 'general',
    ]);

    $this->actingAs($admin)
        ->get(route('admin.settings.index'))
        ->assertSuccessful();

    $this->actingAs($admin)
        ->put(route('admin.settings.update'), [
            'settings' => [
                'site_name' => 'New Site Name',
                'admin_email' => 'admin@example.com',
            ],
        ])
        ->assertRedirect(route('admin.settings.index'));

    expect(SiteSetting::get('site_name'))->toBe('New Site Name')
        ->and(SiteSetting::get('admin_email'))->toBe('admin@example.com');
});
