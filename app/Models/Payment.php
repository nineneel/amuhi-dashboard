<?php

namespace App\Models;

use App\Enums\PaymentApprovalStatus;
use App\PaymentStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Payment extends Model
{
    protected $fillable = [
        'invoice_id',
        'gateway_transaction_id',
        'amount',
        'status',
        'paid_at',
        'payment_method',
        'subscription_plan_id',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'status' => PaymentStatus::class,
            'paid_at' => 'datetime',
        ];
    }

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }

    public function subscriptionPlan(): BelongsTo
    {
        return $this->belongsTo(SubscriptionPlan::class);
    }

    public function latestApprovalHistory(): HasOne
    {
        return $this->hasOne(PaymentApproval::class)->latestOfMany();
    }

    public function approvalHistories(): HasMany
    {
        return $this->hasMany(PaymentApproval::class)->latest();
    }

    public function currentApprovalStatus(): ?PaymentApprovalStatus
    {
        $latestHistory = $this->relationLoaded('latestApprovalHistory')
            ? $this->latestApprovalHistory
            : $this->latestApprovalHistory()->first();

        return $latestHistory?->new_approval_status;
    }

    public function latestProofImagePath(): ?string
    {
        if ($this->relationLoaded('approvalHistories')) {
            return $this->approvalHistories->first(fn (PaymentApproval $history) => ! empty($history->proof_image))?->proof_image;
        }

        return $this->approvalHistories()
            ->whereNotNull('proof_image')
            ->latest()
            ->value('proof_image');
    }

    public function isPendingReview(): bool
    {
        return $this->currentApprovalStatus() === PaymentApprovalStatus::PendingReview;
    }

    public function isApproved(): bool
    {
        return $this->currentApprovalStatus() === PaymentApprovalStatus::Approved;
    }

    public function isRejected(): bool
    {
        return $this->currentApprovalStatus() === PaymentApprovalStatus::Rejected;
    }

    public function isInsufficientNominal(): bool
    {
        return $this->currentApprovalStatus() === PaymentApprovalStatus::InsufficientNominal;
    }

    public function canReupload(): bool
    {
        $currentApprovalStatus = $this->currentApprovalStatus();

        return $currentApprovalStatus === PaymentApprovalStatus::Rejected
            || $currentApprovalStatus === PaymentApprovalStatus::InsufficientNominal;
    }

    public function isManualPayment(): bool
    {
        return $this->payment_method === 'manual';
    }

    public function recordApprovalHistory(
        PaymentApprovalStatus $newStatus,
        ?PaymentApprovalStatus $previousStatus = null,
        ?string $proofImage = null,
        ?int $actorId = null,
        ?string $actorRole = null,
        ?string $notes = null
    ): void {
        $this->approvalHistories()->create([
            'previous_approval_status' => $previousStatus,
            'new_approval_status' => $newStatus,
            'proof_image' => $proofImage,
            'actor_id' => $actorId,
            'actor_role' => $actorRole,
            'notes' => $notes,
            'responded_by_id' => null,
            'responded_by_role' => null,
            'responded_at' => null,
        ]);
    }

    public function respondLatestApprovalHistory(
        PaymentApprovalStatus $newStatus,
        int $adminId,
        string $adminRole,
        ?string $notes = null
    ): void {
        $latestHistory = $this->relationLoaded('latestApprovalHistory')
            ? $this->latestApprovalHistory
            : $this->latestApprovalHistory()->first();

        if (! $latestHistory) {
            return;
        }

        $latestHistory->update([
            'new_approval_status' => $newStatus,
            'notes' => $notes,
            'responded_by_id' => $adminId,
            'responded_by_role' => $adminRole,
            'responded_at' => now(),
        ]);
    }
}
