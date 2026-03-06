<?php

namespace App\Http\Middleware;

use Closure;
use Examyou\RestAPI\Exceptions\ApiException;
use Illuminate\Http\Request;

class EnsureCompanyAssigned
{
    public function handle(Request $request, Closure $next)
    {
        $user = auth('api')->user();

        if (!$user || !$user->company_id) {
            throw new ApiException(
                'Company setup required. Please complete onboarding.',
                null,
                403,
                ['needs_onboarding' => true]
            );
        }

        return $next($request);
    }
}
