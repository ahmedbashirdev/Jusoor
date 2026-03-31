<?php

namespace Modules\Auth\App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Modules\Auth\App\Enums\AccountStatus;
use Modules\Auth\App\Enums\Role;
use Modules\Auth\App\Models\User;

class AuthService
{
    public function registerStudent(array $data): array
    {
        return DB::transaction(function () use ($data) {
            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => $data['password'],
                'account_status' => AccountStatus::Active,
            ]);

            $user->assignRole(Role::Student->value);
            $user->sendEmailVerificationNotification();

            $token = $user->createToken('auth-token')->plainTextToken;

            return [
                'user' => $user,
                'token' => $token,
            ];
        });
    }

    public function registerCompany(array $data): User
    {
        return DB::transaction(function () use ($data) {
            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => $data['password'],
                'account_status' => AccountStatus::PendingReview,
            ]);

            $user->assignRole(Role::Company->value);

            $user->companyProfile()->create([
                'company_name' => $data['company_name'],
                'industry' => $data['industry'],
                'company_size' => $data['company_size'],
                'phone' => $data['phone'],
                'whatsapp' => $data['whatsapp'],
                'website' => $data['website'] ?? null,
            ]);

            $user->sendEmailVerificationNotification();

            return $user;
        });
    }

    public function registerMentor(array $data): User
    {
        return DB::transaction(function () use ($data) {
            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => $data['password'],
                'account_status' => AccountStatus::PendingReview,
            ]);

            $user->assignRole(Role::Mentor->value);

            $user->mentorProfile()->create([
                'specialization' => $data['specialization'],
                'years_of_experience' => $data['years_of_experience'],
                'bio' => $data['bio'],
            ]);

            $user->sendEmailVerificationNotification();

            return $user;
        });
    }

    public function login(string $email, string $password): ?array
    {
        $user = User::where('email', $email)->first();

        if (! $user || ! Hash::check($password, $user->password)) {
            return null;
        }

        $token = $user->createToken('auth-token')->plainTextToken;

        return [
            'user' => $user,
            'token' => $token,
        ];
    }

    public function logout(User $user): void
    {
        $user->currentAccessToken()->delete();
    }
}
