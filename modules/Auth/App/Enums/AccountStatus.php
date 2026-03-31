<?php

namespace Modules\Auth\App\Enums;

enum AccountStatus: string
{
    case Active = 'active';
    case PendingReview = 'pending_review';
    case Approved = 'approved';
    case Rejected = 'rejected';

    public function canAccessDashboard(): bool
    {
        return match ($this) {
            self::Active, self::Approved => true,
            self::PendingReview, self::Rejected => false,
        };
    }
}
