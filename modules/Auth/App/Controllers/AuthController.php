<?php

namespace Modules\Auth\App\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Auth\App\Requests\LoginRequest;
use Modules\Auth\App\Requests\RegisterCompanyRequest;
use Modules\Auth\App\Requests\RegisterMentorRequest;
use Modules\Auth\App\Requests\RegisterStudentRequest;
use Modules\Auth\App\Services\AuthService;

class AuthController extends Controller
{
    public function __construct(
        private readonly AuthService $authService
    ) {}

    public function registerStudent(RegisterStudentRequest $request): JsonResponse
    {
        $result = $this->authService->registerStudent($request->validated());

        return response()->json([
            'message' => __('auth::auth.register_success'),
            'token' => $result['token'],
            'user' => $this->formatUser($result['user']),
            'redirect' => $result['user']->getRedirectPath(),
        ], 201);
    }

    public function registerCompany(RegisterCompanyRequest $request): JsonResponse
    {
        $user = $this->authService->registerCompany($request->validated());

        return response()->json([
            'message' => __('auth::auth.request_received'),
            'detail' => __('auth::auth.review_period'),
            'user' => $this->formatUser($user),
        ], 201);
    }

    public function registerMentor(RegisterMentorRequest $request): JsonResponse
    {
        $user = $this->authService->registerMentor($request->validated());

        return response()->json([
            'message' => __('auth::auth.request_received'),
            'detail' => __('auth::auth.review_period'),
            'user' => $this->formatUser($user),
        ], 201);
    }

    public function login(LoginRequest $request): JsonResponse
    {
        $result = $this->authService->login(
            $request->validated('email'),
            $request->validated('password')
        );

        if (! $result) {
            return response()->json([
                'message' => __('auth::auth.invalid_credentials'),
            ], 401);
        }

        $user = $result['user'];

        return response()->json([
            'message' => __('auth::auth.login_success'),
            'token' => $result['token'],
            'user' => $this->formatUser($user),
            'redirect' => $user->getRedirectPath(),
        ]);
    }

    public function logout(Request $request): JsonResponse
    {
        $this->authService->logout($request->user());

        return response()->json([
            'message' => __('auth::auth.logout_success'),
        ]);
    }

    public function me(Request $request): JsonResponse
    {
        $user = $request->user();
        $user->load('roles.permissions');

        return response()->json([
            'user' => $this->formatUser($user),
            'permissions' => $user->getAllPermissions()->pluck('name'),
            'redirect' => $user->getRedirectPath(),
        ]);
    }

    private function formatUser($user): array
    {
        return [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'role' => $user->getRoleEnum()?->value,
            'account_status' => $user->account_status->value,
            'email_verified' => $user->hasVerifiedEmail(),
        ];
    }
}
