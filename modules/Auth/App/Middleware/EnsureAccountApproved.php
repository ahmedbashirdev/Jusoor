<?php

namespace Modules\Auth\App\Middleware;

use Closure;
use Illuminate\Http\Request;
use Modules\Auth\App\Enums\AccountStatus;
use Symfony\Component\HttpFoundation\Response;

class EnsureAccountApproved
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user) {
            return response()->json([
                'message' => __('auth::auth.unauthenticated'),
            ], 401);
        }

        $status = $user->account_status;

        if ($status === AccountStatus::PendingReview) {
            return response()->json([
                'message' => __('auth::auth.account_pending'),
                'account_status' => $status->value,
            ], 403);
        }

        if ($status === AccountStatus::Rejected) {
            return response()->json([
                'message' => __('auth::auth.account_rejected'),
                'account_status' => $status->value,
            ], 403);
        }

        return $next($request);
    }
}
