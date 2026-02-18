<?php

use App\Enums\PaymentApprovalStatus;
use App\InvoiceStatus;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\SubscriptionPlan;
use App\Models\User;
use App\Notifications\InAppMessageNotification;
use App\PaymentStatus;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

uses(RefreshDatabase::class);

it('updates the same approval row when admin approves payment and shows it on admin page', function () {
    Notification::fake();

    $admin = User::factory()->admin()->create();
    $member = User::factory()->create();
    $plan = SubscriptionPlan::factory()->create([
        'name' => 'Annual Membership',
        'duration_days' => 365,
        'is_active' => true,
    ]);

    $payment = createManualPaymentForUser($member, $plan);
    $payment->recordApprovalHistory(
        newStatus: PaymentApprovalStatus::PendingReview,
        proofImage: 'payment-proofs/test-proof.jpg',
        actorId: $member->id,
        actorRole: $member->role->value,
        notes: 'Payment proof submitted by user.',
    );

    $this->actingAs($admin)
        ->post(route('admin.payment-approvals.approve', $payment), [
            'admin_notes' => 'Transfer amount validated.',
        ])
        ->assertRedirect(route('admin.payment-approvals.index'));

    Notification::assertSentTo(
        $member,
        InAppMessageNotification::class,
        function (InAppMessageNotification $notification, array $channels): bool {
            expect($channels)->toContain('mail');
            expect($notification->title)->toBe(__('ui.admin.payment_approved'));

            return true;
        }
    );

    expect($payment->fresh()->currentApprovalStatus())->toBe(PaymentApprovalStatus::Approved);

    $this->assertDatabaseCount('payment_approvals', 1);

    $this->assertDatabaseHas('payment_approvals', [
        'payment_id' => $payment->id,
        'previous_approval_status' => null,
        'new_approval_status' => PaymentApprovalStatus::Approved->value,
        'proof_image' => 'payment-proofs/test-proof.jpg',
        'actor_id' => $member->id,
        'actor_role' => $member->role->value,
        'responded_by_id' => $admin->id,
        'responded_by_role' => $admin->role->value,
        'notes' => 'Transfer amount validated.',
    ]);

    $this->actingAs($admin)
        ->get(route('admin.payment-approvals.show', $payment))
        ->assertSuccessful()
        ->assertSee(__('ui.admin.approval_history'))
        ->assertSee('Transfer amount validated.');
});

it('emails admins when a member uploads payment proof', function () {
    Storage::fake('public');
    Notification::fake();

    $member = User::factory()->create();
    $admin = User::factory()->admin()->create();
    $superAdmin = User::factory()->superAdmin()->create();

    SubscriptionPlan::factory()->create([
        'name' => 'Register as Member',
        'duration_days' => 0,
        'is_active' => true,
    ]);

    $annualPlan = SubscriptionPlan::factory()->create([
        'name' => 'Annual Membership',
        'duration_days' => 365,
        'is_active' => true,
    ]);

    $this->actingAs($member)
        ->post(route('payments.upload-proof'), [
            'plan_id' => $annualPlan->id,
            'proof_image' => UploadedFile::fake()->image('proof-image.jpg'),
        ])
        ->assertRedirect(route('payments.show'))
        ->assertSessionHas('success');

    Notification::assertSentTo(
        $admin,
        InAppMessageNotification::class,
        function (InAppMessageNotification $notification, array $channels): bool {
            expect($channels)->toContain('mail');
            expect($notification->title)->toBe('New Payment Proof Uploaded');

            return true;
        }
    );

    Notification::assertSentTo(
        $superAdmin,
        InAppMessageNotification::class,
        function (InAppMessageNotification $notification): bool {
            expect($notification->title)->toBe('New Payment Proof Uploaded');

            return true;
        }
    );
});

it('records reupload history and shows the timeline on the user payment page', function () {
    Storage::fake('public');

    $member = User::factory()->create();
    $admin = User::factory()->admin()->create();

    SubscriptionPlan::factory()->create([
        'name' => 'Register as Member',
        'duration_days' => 0,
        'is_active' => true,
    ]);

    $annualPlan = SubscriptionPlan::factory()->create([
        'name' => 'Annual Membership',
        'duration_days' => 365,
        'is_active' => true,
    ]);

    $payment = createManualPaymentForUser($member, $annualPlan, [
        'status' => PaymentStatus::Failed->value,
    ]);
    Storage::disk('public')->put('payment-proofs/test-proof.jpg', 'old-proof-content');

    $payment->recordApprovalHistory(
        newStatus: PaymentApprovalStatus::PendingReview,
        proofImage: 'payment-proofs/test-proof.jpg',
        actorId: $member->id,
        actorRole: $member->role->value,
        notes: 'Payment proof submitted by user.',
    );

    $payment->respondLatestApprovalHistory(
        newStatus: PaymentApprovalStatus::Rejected,
        adminId: $admin->id,
        adminRole: $admin->role->value,
        notes: 'Proof is unreadable.',
    );

    $this->actingAs($member)
        ->post(route('payments.reupload-proof'), [
            'proof_image' => UploadedFile::fake()->image('new-proof.jpg'),
        ])
        ->assertRedirect(route('payments.show'))
        ->assertSessionHas('success');

    $this->assertDatabaseCount('payment_approvals', 2);

    $this->assertDatabaseHas('payment_approvals', [
        'payment_id' => $payment->id,
        'previous_approval_status' => PaymentApprovalStatus::Rejected->value,
        'new_approval_status' => PaymentApprovalStatus::PendingReview->value,
        'actor_id' => $member->id,
        'actor_role' => $member->role->value,
        'notes' => null,
        'responded_by_id' => null,
    ]);

    Storage::disk('public')->assertExists('payment-proofs/test-proof.jpg');

    $reSubmissionHistory = $payment->fresh()->approvalHistories()->latest()->first();

    expect($reSubmissionHistory->proof_image)->not()->toBeNull();
    Storage::disk('public')->assertExists($reSubmissionHistory->proof_image);

    $this->actingAs($member)
        ->get(route('payments.show'))
        ->assertSuccessful()
        ->assertSee(__('ui.payments.approval_history'))
        ->assertSee('Proof is unreadable.')
        ->assertDontSee('Payment proof re-submitted by user.');
});

it('shows latest history status on admin payment approval index', function () {
    $admin = User::factory()->admin()->create();
    $member = User::factory()->create();
    $plan = SubscriptionPlan::factory()->create([
        'name' => 'Annual Membership',
        'duration_days' => 365,
        'is_active' => true,
    ]);

    $payment = createManualPaymentForUser($member, $plan);
    $payment->recordApprovalHistory(
        newStatus: PaymentApprovalStatus::PendingReview,
        proofImage: 'payment-proofs/test-proof.jpg',
        actorId: $member->id,
        actorRole: $member->role->value,
        notes: 'Payment proof submitted by user.',
    );

    $this->actingAs($admin)
        ->get(route('admin.payment-approvals.index'))
        ->assertSuccessful()
        ->assertSee(__('ui.admin.need_approval'))
        ->assertSee('bg-error-50')
        ->assertSee('animate-ping')
        ->assertDontSee('Need to review')
        ->assertDontSee('waiting for approval action.');
});

function createManualPaymentForUser(User $user, SubscriptionPlan $plan, array $overrides = []): Payment
{
    $invoice = Invoice::query()->create([
        'user_id' => $user->id,
        'invoice_number' => 'INV-TEST-'.Str::upper(Str::random(8)),
        'amount' => 3000000,
        'due_date' => now()->addDays(7)->toDateString(),
        'status' => InvoiceStatus::Pending->value,
    ]);

    return Payment::query()->create(array_merge([
        'invoice_id' => $invoice->id,
        'gateway_transaction_id' => 'MANUAL-TEST-'.Str::upper(Str::random(8)),
        'amount' => 3000000,
        'status' => PaymentStatus::Pending->value,
        'payment_method' => 'manual',
        'subscription_plan_id' => $plan->id,
    ], $overrides));
}
