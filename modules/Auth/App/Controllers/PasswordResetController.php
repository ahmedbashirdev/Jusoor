<?php

namespace Modules\Auth\App\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Modules\Auth\App\Models\User;
use Modules\Auth\App\Requests\ForgotPasswordRequest;
use Modules\Auth\App\Requests\ResetPasswordRequest;

/**
 * @tags Auth
 */
class PasswordResetController extends Controller
{
    /**
     * Forgot Password
     *
     * Sends a password reset link to the user's email address.
     * The email contains a link with a token that is used in the Reset Password endpoint.
     *
     * @unauthenticated
     *
     * @response 200 {
     *   "message": "Password reset link sent"
     * }
     * @response 422 {
     *   "message": "We can't find a user with that email address."
     * }
     */
    public function forgotPassword(ForgotPasswordRequest $request): JsonResponse
    {
        $status = Password::sendResetLink(
            $request->only('email')
        );

        if ($status === Password::RESET_LINK_SENT) {
            return response()->json([
                'message' => __('auth::auth.password_reset_sent'),
            ]);
        }

        return response()->json([
            'message' => __($status),
        ], 422);
    }

    /**
     * Reset Password
     *
     * Resets the user's password using the token received via email.
     * After a successful reset, all existing tokens are revoked — the user must log in again.
     *
     * Extract the `token` and `email` from the reset link URL query parameters.
     *
     * @unauthenticated
     *
     * @response 200 {
     *   "message": "Password changed successfully"
     * }
     * @response 422 {
     *   "message": "Password reset failed"
     * }
     */
    public function resetPassword(ResetPasswordRequest $request): JsonResponse
    {
        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function (User $user, string $password) {
                $user->forceFill([
                    'password' => $password,
                    'remember_token' => Str::random(60),
                ])->save();

                $user->tokens()->delete();
            }
        );

        if ($status === Password::PASSWORD_RESET) {
            return response()->json([
                'message' => __('auth::auth.password_reset_success'),
            ]);
        }

        return response()->json([
            'message' => __('auth::auth.password_reset_failed'),
        ], 422);
    }
}
