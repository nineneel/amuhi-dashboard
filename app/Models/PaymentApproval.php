<?php

namespace App\Models;

use App\Enums\PaymentApprovalStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PaymentApproval extends Model
{
    protected $fillable = [
        'payment_id',
        'previous_approval_status',
        'new_approval_status',
        'proof_image',
        'actor_id',
        'actor_role',
        'notes',
        'responded_by_id',
        'responded_by_role',
        'responded_at',
    ];

    protected function casts(): array
    {
        return [
            'previous_approval_status' => PaymentApprovalStatus::class,
            'new_approval_status' => PaymentApprovalStatus::class,
            'responded_at' => 'datetime',
        ];
    }

    public function payment(): BelongsTo
    {
        return $this->belongsTo(Payment::class);
    }

    public function actor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'actor_id');
    }

    public function respondedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'responded_by_id');
    }
}
