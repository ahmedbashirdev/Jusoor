<?php

namespace Modules\Auth\App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Modules\Auth\App\Enums\AccountStatus;
use Modules\Auth\App\Enums\Role;
use Modules\Auth\App\Notifications\ResetPasswordNotification;
use Modules\Auth\App\Notifications\VerifyEmailNotification;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, HasRoles, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'account_status',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'account_status' => AccountStatus::class,
        ];
    }

    public function companyProfile(): HasOne
    {
        return $this->hasOne(CompanyProfile::class);
    }

    public function mentorProfile(): HasOne
    {
        return $this->hasOne(MentorProfile::class);
    }

    public function getRoleEnum(): ?Role
    {
        $roleName = $this->roles->first()?->name;

        return $roleName ? Role::tryFrom($roleName) : null;
    }

    public function getRedirectPath(): string
    {
        $role = $this->getRoleEnum();

        if (! $role) {
            return '/';
        }

        if (! $this->hasVerifiedEmail()) {
            return '/verify-email';
        }

        if (! $this->account_status->canAccessDashboard()) {
            return match ($this->account_status) {
                AccountStatus::PendingReview => '/pending-review',
                AccountStatus::Rejected => '/account-rejected',
                default => '/',
            };
        }

        return $role->dashboardRedirect();
    }

    public function sendEmailVerificationNotification(): void
    {
        $this->notify(new VerifyEmailNotification);
    }

    public function sendPasswordResetNotification($token): void
    {
        $this->notify(new ResetPasswordNotification($token));
    }
}
