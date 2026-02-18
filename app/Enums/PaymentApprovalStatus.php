<?php

namespace App\Enums;

enum PaymentApprovalStatus: string
{
    case PendingReview = 'pending_review';
    case Approved = 'approved';
    case InsufficientNominal = 'insufficient';
    case Rejected = 'rejected';

    public function label(): string
    {
        return match ($this) {
            self::PendingReview => 'Submitted',
            self::Approved => 'Approved',
            self::InsufficientNominal => 'Insufficient Nominal',
            self::Rejected => 'Rejected',
        };
    }

    public function badgeColor(): string
    {
        return match ($this) {
            self::PendingReview => 'warning',
            self::Approved => 'success',
            self::InsufficientNominal => 'warning',
            self::Rejected => 'error',
        };
    }
}
