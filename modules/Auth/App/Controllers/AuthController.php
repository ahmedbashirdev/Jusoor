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

/**
 * @tags Auth
 */
class AuthController extends Controller
{
    public function __construct(
        private readonly AuthService $authService
    ) {}

    /**
     * Register Student
     *
     * Creates a student account with immediate access (no admin review required).
     * A verification email is sent automatically. The token is returned immediately.
     *
     * @unauthenticated
     *
     * @response 201 {
     *   "message": "Account created successfully",
     *   "token": "1|abc123...",
     *   "user": {"id": 1, "name": "Sara", "email": "sara@student.com", "role": "student", "account_status": "active", "email_verified": false},
     *   "redirect": "/verify-email"
     * }
     */
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

    /**
     * Register Company
     *
     * Creates a company account. Requires admin approval (1-3 business days) before full dashboard access.
     * No token is returned. The user can log in but will see a pending review status.
     *
     * The `phone` and `whatsapp` fields accept international format (e.g. `+966 555 123 456`).
     * For the "same as phone" checkbox, simply send the same value in both fields.
     * The `website` field is optional.
     *
     * @unauthenticated
     *
     * @response 201 {
     *   "message": "Your request has been received",
     *   "detail": "Your request will be reviewed within 1-3 business days",
     *   "user": {"id": 2, "name": "Ahmed", "email": "ahmed@company.sa", "role": "company", "account_status": "pending_review", "email_verified": false}
     * }
     */
    public function registerCompany(RegisterCompanyRequest $request): JsonResponse
    {
        $user = $this->authService->registerCompany($request->validated());

        return response()->json([
            'message' => __('auth::auth.request_received'),
            'detail' => __('auth::auth.review_period'),
            'user' => $this->formatUser($user),
        ], 201);
    }

    /**
     * Register Mentor
     *
     * Creates a mentor account. Requires admin approval (1-3 business days) before full dashboard access.
     * No token is returned. The `bio` field has a 300 character limit.
     *
     * @unauthenticated
     *
     * @response 201 {
     *   "message": "Your request has been received",
     *   "detail": "Your request will be reviewed within 1-3 business days",
     *   "user": {"id": 3, "name": "Dr. Sarah", "email": "sarah@mentor.com", "role": "mentor", "account_status": "pending_review", "email_verified": false}
     * }
     */
    public function registerMentor(RegisterMentorRequest $request): JsonResponse
    {
        $user = $this->authService->registerMentor($request->validated());

        return response()->json([
            'message' => __('auth::auth.request_received'),
            'detail' => __('auth::auth.review_period'),
            'user' => $this->formatUser($user),
        ], 201);
    }

    /**
     * Login
     *
     * Authenticates a user and returns a bearer token. Works for all roles (student, company, mentor).
     * Use the `redirect` field to navigate the user to the correct page after login.
     *
     * Possible `redirect` values:
     * - `/verify-email` — email not yet verified
     * - `/pending-review` — account awaiting admin approval
     * - `/account-rejected` — account was rejected
     * - `/dashboard/student` — active student
     * - `/dashboard/company` — approved company
     * - `/dashboard/mentor` — approved mentor
     *
     * @unauthenticated
     *
     * @response 200 {
     *   "message": "Logged in successfully",
     *   "token": "2|xyz789...",
     *   "user": {"id": 1, "name": "Sara", "email": "sara@student.com", "role": "student", "account_status": "active", "email_verified": true},
     *   "redirect": "/dashboard/student"
     * }
     * @response 401 {
     *   "message": "Invalid email or password"
     * }
     */
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

    /**
     * Logout
     *
     * Revokes the current access token. The user must log in again to get a new token.
     *
     * @response 200 {
     *   "message": "Logged out successfully"
     * }
     */
    public function logout(Request $request): JsonResponse
    {
        $this->authService->logout($request->user());

        return response()->json([
            'message' => __('auth::auth.logout_success'),
        ]);
    }

    /**
     * Get Current User
     *
     * Returns the authenticated user's profile, role permissions, and redirect path.
     * Use on app load to restore the session and determine where to navigate.
     *
     * The `permissions` array can be used for client-side feature gating.
     *
     * @response 200 {
     *   "user": {"id": 1, "name": "Sara", "email": "sara@student.com", "role": "student", "account_status": "active", "email_verified": true},
     *   "permissions": ["student.dashboard.access", "profile.view-own", "profile.edit-own"],
     *   "redirect": "/dashboard/student"
     * }
     */
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
