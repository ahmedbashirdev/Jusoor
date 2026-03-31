<?php

namespace Modules\Auth\App\Enums;

enum Role: string
{
    case Student = 'student';
    case Company = 'company';
    case Mentor = 'mentor';

    public function requiresReview(): bool
    {
        return match ($this) {
            self::Company, self::Mentor => true,
            self::Student => false,
        };
    }

    public function defaultAccountStatus(): AccountStatus
    {
        return $this->requiresReview()
            ? AccountStatus::PendingReview
            : AccountStatus::Active;
    }

    public function dashboardRedirect(): string
    {
        return match ($this) {
            self::Student => '/dashboard/student',
            self::Company => '/dashboard/company',
            self::Mentor => '/dashboard/mentor',
        };
    }
}
