<?php

namespace Modules\Auth\App\Enums;

enum Permission: string
{
    case StudentDashboardAccess = 'student.dashboard.access';
    case CompanyDashboardAccess = 'company.dashboard.access';
    case MentorDashboardAccess = 'mentor.dashboard.access';

    case ViewOwnProfile = 'profile.view-own';
    case EditOwnProfile = 'profile.edit-own';

    /**
     * Permissions granted to each role.
     *
     * @return array<Role, list<self>>
     */
    public static function matrixForRole(Role $role): array
    {
        $shared = [
            self::ViewOwnProfile,
            self::EditOwnProfile,
        ];

        $dashboardPermission = match ($role) {
            Role::Student => self::StudentDashboardAccess,
            Role::Company => self::CompanyDashboardAccess,
            Role::Mentor => self::MentorDashboardAccess,
        };

        return [$dashboardPermission, ...$shared];
    }

    /** @return list<string> */
    public static function allValues(): array
    {
        return array_map(fn (self $p) => $p->value, self::cases());
    }
}
